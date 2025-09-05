from flask import Flask, render_template, request, jsonify
import requests
import pandas as pd
import numpy as np
import pickle
from datetime import datetime, timedelta

app = Flask(__name__)
import mysql.connector

# Database connection
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="disaster_db"
)

# -------------------------
# Load trained drought model
# -------------------------
with open("Models/drought/drought_model.pkl", "rb") as f:
    model = pickle.load(f)

# -------------------------
# Country -> representative coordinates (center-ish)
# You can add more or wire this to a country->centroid service.
# -------------------------
def get_all_countries():
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT country_name FROM country_coords ORDER BY country_name")
    countries = [row["country_name"] for row in cursor.fetchall()]
    cursor.close()
    return countries

def get_country_coords(country_name):
    cursor = db.cursor(dictionary=True)
    cursor.execute("SELECT latitude, longitude FROM country_coords WHERE country_name = %s", (country_name,))
    row = cursor.fetchone()
    cursor.close()
    return (row["latitude"], row["longitude"]) if row else None



# -------------------------
# Open-Meteo helpers
# -------------------------
DAILY_VARS = "precipitation_sum,temperature_2m_mean,et0_fao_evapotranspiration"

def _safe_get(d, *keys, default=None):
    cur = d
    for k in keys:
        if k not in cur:
            return default
        cur = cur[k]
    return cur

def fetch_daily_timeseries(lat: float, lon: float, start_date: str, end_date: str, timezone="auto"):
    """
    Fetch daily time series (history or forecast depending on date range) from Open-Meteo Historical Weather API
    for past dates and Weather Forecast API for future dates. We’ll call separate endpoints for past & future to
    keep the code simple and robust.
    """
    # History (reanalysis) for purely past window
    today = datetime.utcnow().date()

    start = datetime.fromisoformat(start_date).date()
    end = datetime.fromisoformat(end_date).date()

    frames = []

    # If we need any past days (start <= yesterday)
    if start <= today:
        hist_end = min(end, today)  # up to today (UTC day)
        url_hist = (
            "https://archive-api.open-meteo.com/v1/archive"
            f"?latitude={lat}&longitude={lon}"
            f"&start_date={start.isoformat()}&end_date={hist_end.isoformat()}"
            f"&daily={DAILY_VARS}&timezone={timezone}"
        )
        try:
            r = requests.get(url_hist, timeout=15)
            r.raise_for_status()
            data = r.json()
            times = _safe_get(data, "daily", "time", default=[])
            prcp = _safe_get(data, "daily", "precipitation_sum", default=[])
            tmean = _safe_get(data, "daily", "temperature_2m_mean", default=[])
            et0 = _safe_get(data, "daily", "et0_fao_evapotranspiration", default=[])

            if times and (len(times) == len(prcp) == len(tmean) == len(et0)):
                frames.append(pd.DataFrame({
                    "date": pd.to_datetime(times),
                    "precipitation_sum": prcp,
                    "temperature_2m_mean": tmean,
                    "et0_fao_evapotranspiration": et0
                }))
        except Exception:
            pass

    # If we need future days (tomorrow .. end)
    if end > today:
        # Forecast API returns from today forward ~7–10 days
        url_fc = (
            "https://api.open-meteo.com/v1/forecast"
            f"?latitude={lat}&longitude={lon}"
            f"&daily={DAILY_VARS}&timezone={timezone}"
        )
        try:
            r = requests.get(url_fc, timeout=15)
            r.raise_for_status()
            data = r.json()
            times = _safe_get(data, "daily", "time", default=[])
            prcp = _safe_get(data, "daily", "precipitation_sum", default=[])
            tmean = _safe_get(data, "daily", "temperature_2m_mean", default=[])
            et0 = _safe_get(data, "daily", "et0_fao_evapotranspiration", default=[])

            if times and (len(times) == len(prcp) == len(tmean) == len(et0)):
                df_fc = pd.DataFrame({
                    "date": pd.to_datetime(times),
                    "precipitation_sum": prcp,
                    "temperature_2m_mean": tmean,
                    "et0_fao_evapotranspiration": et0
                })
                # Keep only requested future segment
                df_fc = df_fc[(df_fc["date"].dt.date >= max(today, start)) & (df_fc["date"].dt.date <= end)]
                frames.append(df_fc)
        except Exception:
            pass

    if not frames:
        # Fallback: empty frame (caller should handle)
        return pd.DataFrame(columns=["date", "precipitation_sum", "temperature_2m_mean", "et0_fao_evapotranspiration"])

    df = pd.concat(frames, ignore_index=True).drop_duplicates(subset=["date"]).sort_values("date")
    return df.reset_index(drop=True)

# -------------------------
# Feature engineering
# -------------------------
def compute_features_series(dates: pd.Series, precip: pd.Series, tmean: pd.Series, et0: pd.Series):
    """
    Build rolling-window features aligned per day:
      - RainfallLast30Days: rolling sum(precip, 30)
      - TemperatureAvg7: rolling mean(temp, 7)
      - ET0Last30Days: rolling sum(et0, 30)
    Returns a DataFrame with features per day.
    """
    df = pd.DataFrame({
        "date": pd.to_datetime(dates),
        "precipitation_sum": precip.astype(float),
        "temperature_2m_mean": tmean.astype(float),
        "et0_fao_evapotranspiration": et0.astype(float)
    }).sort_values("date").reset_index(drop=True)

    df["RainfallLast30Days"] = df["precipitation_sum"].rolling(window=30, min_periods=1).sum()
    df["TemperatureAvg7"] = df["temperature_2m_mean"].rolling(window=7, min_periods=1).mean()
    df["ET0Last30Days"] = df["et0_fao_evapotranspiration"].rolling(window=30, min_periods=1).sum()

    # If your drought model used different names, map here accordingly:
    feats = df[["date", "RainfallLast30Days", "TemperatureAvg7", "ET0Last30Days"]].copy()
    return feats

def align_to_model_columns(X: pd.DataFrame) -> pd.DataFrame:
    """Ensure X has the exact columns the model expects; fill missing with 0."""
    if hasattr(model, "feature_names_in_"):
        for col in model.feature_names_in_:
            if col not in X.columns:
                X[col] = 0.0
        X = X[model.feature_names_in_]
    return X

def predict_daily_probs(feature_df: pd.DataFrame):
    """Run model per day and attach probability + risk band using dynamic thresholds."""
    X = feature_df.drop(columns=["date"]).copy()
    X = align_to_model_columns(X)

    raw_preds = model.predict(X)

    # Scale regression output into 0–1 range (pseudo-probability)
    probs = 1 / (1 + np.exp(-raw_preds))

  

    feature_df = feature_df.copy()
    feature_df["probability"] = probs

    # Dynamic thresholds using percentiles
    low_thresh = np.percentile(probs, 33)
    high_thresh = np.percentile(probs, 66)

    def band(p):
        if p <= low_thresh:
            return "Low"
        elif p <= high_thresh:
            return "Moderate"
        else:
            return "High"

    feature_df["risk_level"] = feature_df["probability"].apply(band)
    return feature_df


# -------------------------
# API routes
# -------------------------
@app.route("/")
def home():
    return render_template("drought.php", countries=get_all_countries())


@app.route("/predict-country", methods=["POST"])
def predict_country():
    country = request.form.get("country")
    horizon_days = int(request.form.get("horizon_days", "7"))

    coords = get_country_coords(country)
    if not coords:
        return jsonify({"error": "Unknown country"}), 400

    lat, lon = coords
    # (rest of your code stays same)


    # Build a combined window: last 60 days history + next horizon days forecast
    today = datetime.utcnow().date()
    start_hist = (today - timedelta(days=60)).isoformat()
    end_all = (today + timedelta(days=horizon_days)).isoformat()

    df = fetch_daily_timeseries(lat, lon, start_hist, end_all, timezone="auto")

    if df.empty:
        # Conservative fallback: return dummy low-risk rows
        timeline = []
        for i in range(horizon_days + 1):
            d = (today + timedelta(days=i)).isoformat()
            timeline.append({
                "date": d,
                "RainfallLast30Days": 0.0,
                "TemperatureAvg7": 0.0,
                "ET0Last30Days": 0.0,
                "probability": 0.1,
                "risk_level": "Low"
            })
        return jsonify({
            "country": country,
            "lat": lat,
            "lon": lon,
            "timeline": timeline,
            "note": "Fallback used: weather API unavailable."
        })

    feats = compute_features_series(df["date"], df["precipitation_sum"], df["temperature_2m_mean"], df["et0_fao_evapotranspiration"])
    preds = predict_daily_probs(feats)

    # Keep only today .. today+horizon
    mask = (preds["date"].dt.date >= today) & (preds["date"].dt.date <= (today + timedelta(days=horizon_days)))
    out = preds.loc[mask].copy()

    # Build summary for "today"
    today_row = out.iloc[0] if not out.empty else None
    summary = None
    if today_row is not None:
        summary = {
            "date": today_row["date"].date().isoformat(),
            "RainfallLast30Days": round(float(today_row["RainfallLast30Days"]), 2),
            "TemperatureAvg7": round(float(today_row["TemperatureAvg7"]), 2),
            "ET0Last30Days": round(float(today_row["ET0Last30Days"]), 2),
            "probability": round(float(today_row["probability"]), 2),
            "risk_level": str(today_row["risk_level"])
        }

    timeline = []
    for _, r in out.iterrows():
        timeline.append({
            "date": r["date"].date().isoformat(),
            "RainfallLast30Days": round(float(r["RainfallLast30Days"]), 2),
            "TemperatureAvg7": round(float(r["TemperatureAvg7"]), 2),
            "ET0Last30Days": round(float(r["ET0Last30Days"]), 2),
            "probability": round(float(r["probability"]), 2),
            "risk_level": str(r["risk_level"])
        })

    return jsonify({
        "country": country,
        "lat": lat,
        "lon": lon,
        "today": summary,
        "timeline": timeline
    })

@app.route("/save-prediction", methods=["POST"])
def save_prediction():
    data = request.get_json()

    if not data or "today" not in data:
        return jsonify({"success": False, "error": "Invalid data"}), 400

    today = data["today"]
    timeline = data.get("timeline", [])
    country = data.get("country")
    horizon_days = data.get("horizon_days", 7)

    try:
        cursor = db.cursor()

        # Insert main prediction
        cursor.execute("""
            INSERT INTO drought_predictions
            (country, horizon_days, date, rainfall_last_30d, temp_avg_7d, et0_last_30d, probability, risk_level)
            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
        """, (
            country, horizon_days, today["date"],
            today["RainfallLast30Days"], today["TemperatureAvg7"], today["ET0Last30Days"],
            today["probability"], today["risk_level"]
        ))
        prediction_id = cursor.lastrowid

        # Insert timeline
        for row in timeline:
            cursor.execute("""
                INSERT INTO drought_prediction_timeline
                (prediction_id, date, rainfall_last_30d, temp_avg_7d, et0_last_30d, probability, risk_level)
                VALUES (%s,%s,%s,%s,%s,%s,%s)
            """, (
                prediction_id, row["date"], row["RainfallLast30Days"], row["TemperatureAvg7"],
                row["ET0Last30Days"], row["probability"], row["risk_level"]
            ))

        db.commit()
        cursor.close()

        return jsonify({"success": True, "prediction_id": prediction_id})
    except Exception as e:
        return jsonify({"success": False, "error": str(e)})



if __name__ == "__main__":
    app.run(debug=True, port=5001)

