import sys, os
import pytest

# Make sure Python can find the drought.py file
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "python")))

import drought

# Dummy model to bypass pickle loading
class DummyModel:
    feature_names_in_ = ["RainfallLast30Days", "TemperatureAvg7", "ET0Last30Days"]
    def predict(self, X):
        return [0.7] * len(X)  # fixed prediction for testing

# Replace the real model with dummy before tests run
@pytest.fixture(autouse=True)
def patch_model():
    drought.model = DummyModel()


def test_safe_get_existing_key():
    data = {"a": {"b": 5}}
    assert drought._safe_get(data, "a", "b", default=None) == 5

def test_safe_get_missing_key():
    data = {"a": {}}
    assert drought._safe_get(data, "a", "x", default="fallback") == "fallback"
