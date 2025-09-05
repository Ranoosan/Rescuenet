from flask import Flask, render_template, request, jsonify
import requests
import pandas as pd
import pickle

app = Flask(__name__)
import mysql.connector

# Database connection
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="disaster_db"
)

# Load trained model
with open('Models/flood/flood_model.pkl', 'rb') as f:
    model = pickle.load(f)

# Country coordinates
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

def fetch_forecast(lat, lon, days=7):
    """
    Fetch 7-day rainfall and humidity forecast
    """
    try:
        url = f"https://api.open-meteo.com/v1/forecast?latitude={lat}&longitude={lon}&daily=rain_sum,relative_humidity_2m_max&forecast_days={days}&timezone=auto"
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        data = response.json()
        return data['daily']
    except:
        return {"time": [], "rain_sum": [], "relative_humidity_2m_max": []}

def predict_risk(rain, humidity):
    # Static environmental features
    river_level = 2.1
    soil_moisture = 0.25
    elevation = 2.5
    slope = 0.02
    urban_index = 0.7
    drainage_index = 0.5

    X_new = pd.DataFrame([{
        'Rainfall_Last_3_Days': rain,
        'Rainfall_Intensity': rain/3,
        'River_Level': river_level,
        'Soil_Moisture': soil_moisture,
        'Elevation': elevation,
        'Slope': slope,
        'Urbanization_Index': urban_index,
        'Drainage_Index': drainage_index,
        'Humidity': humidity
    }])

    # Align features
    for col in model.feature_names_in_:
        if col not in X_new.columns:
            X_new[col] = 0
    X_new = X_new[model.feature_names_in_]

    flood_prob = model.predict_proba(X_new)[:,1][0]
    if flood_prob < 0.4:
        risk = "Low"
    elif flood_prob < 0.7:
        risk = "Moderate"
    else:
        risk = "High"
    return round(flood_prob, 2), risk

@app.route("/")
def home():
    return render_template("flood.html", countries=get_all_countries())


@app.route("/predict-country", methods=["POST"])
def predict_country():
    country = request.form.get("country")
    horizon_days = int(request.form.get("horizon_days", "7"))

    coords = get_country_coords(country)
    if not coords:
        return jsonify({"error": "Unknown country"}), 400

    lat, lon = coords
    forecast = fetch_forecast(lat, lon, days=7)

    results = []
    for i, date in enumerate(forecast['time']):
        rain = forecast['rain_sum'][i]
        humidity = forecast['relative_humidity_2m_max'][i]
        prob, risk = predict_risk(rain, humidity)
        results.append({
            "date": date,
            "rain": rain,
            "humidity": humidity,
            "flood_probability": prob,
            "risk_level": risk
        })

    return jsonify({"country": country, "forecast": results})

@app.route("/save-prediction", methods=["POST"])
def save_prediction():
    data = request.get_json()
    if not data or "forecast" not in data or len(data["forecast"]) == 0:
        return jsonify({"success": False, "error": "Invalid data"}), 400

    country = data.get("country")
    horizon_days = data.get("horizon_days", 7)
    forecast = data["forecast"]

    try:
        cursor = db.cursor()

        # Insert main prediction (today)
        today_row = forecast[0]
        cursor.execute("""
            INSERT INTO flood_predictions
            (country, horizon_days, date, rain, humidity, flood_probability, risk_level)
            VALUES (%s,%s,%s,%s,%s,%s,%s)
        """, (
            country, horizon_days, today_row["date"], today_row["rain"], today_row["humidity"],
            today_row["flood_probability"], today_row["risk_level"]
        ))
        prediction_id = cursor.lastrowid

        # Insert timeline (rest of days, if any)
        for row in forecast[1:]:
            cursor.execute("""
                INSERT INTO flood_prediction_timeline
                (prediction_id, date, rain, humidity, flood_probability, risk_level)
                VALUES (%s,%s,%s,%s,%s,%s)
            """, (
                prediction_id, row["date"], row["rain"], row["humidity"],
                row["flood_probability"], row["risk_level"]
            ))

        db.commit()
        cursor.close()
        return jsonify({"success": True, "prediction_id": prediction_id})
    except Exception as e:
        return jsonify({"success": False, "error": str(e)})



if __name__ == "__main__":
    app.run(debug=True)
