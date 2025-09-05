from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import joblib

# Load saved model and label encoder
model = joblib.load("Models/tips/severity_pipeline.pkl")
le = joblib.load("Models/tips/label_encoder.pkl")

app = Flask(__name__)
CORS(app)  # allow cross-origin requests

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.json  # JSON input from frontend
        df = pd.DataFrame([data])  # convert to DataFrame
        
        pred_num = model.predict(df)[0]
        pred_label = le.inverse_transform([pred_num])[0]
        proba = model.predict_proba(df)[0].tolist()
        
        response = {
            'severity': pred_label,
            'probabilities': dict(zip(le.classes_, proba))
        }
        return jsonify(response)
    
    except Exception as e:
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    app.run(port=5002, debug=True)
