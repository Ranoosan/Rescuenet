<?php
header('Content-Type: application/json');

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=disaster_db", "root", "");

// Get ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch main prediction
$stmt = $pdo->prepare("SELECT * FROM drought_predictions WHERE id = ?");
$stmt->execute([$id]);
$prediction = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$prediction){
    echo json_encode(["error"=>"Prediction not found"]);
    exit;
}

// Fetch timeline
$stmt = $pdo->prepare("SELECT date, rainfall_last_30d, temp_avg_7d, et0_last_30d, probability, risk_level 
                       FROM drought_prediction_timeline WHERE prediction_id = ? ORDER BY date ASC");
$stmt->execute([$id]);
$timeline = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Response
echo json_encode([
    "prediction" => $prediction,
    "timeline" => $timeline
]);
