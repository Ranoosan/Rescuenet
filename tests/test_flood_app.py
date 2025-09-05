import pytest
from tips import app  # assuming your Flask file is tips.py

@pytest.fixture
def client():
    app.config["TESTING"] = True
    with app.test_client() as client:
        yield client

def test_predict_valid(client):
    # Example valid input matching your model features
    data = {
        "feature1": 5,
        "feature2": 1,
        "feature3": 0,
        "feature4": 3
    }
    
    response = client.post("/predict", json=data)
    assert response.status_code == 200
    
    json_data = response.get_json()
    assert "severity" in json_data
    assert "probabilities" in json_data
    # Check probabilities sum to ~1
    total_prob = sum(json_data["probabilities"].values())
    assert 0.99 <= total_prob <= 1.01
