<?php
header('Content-Type: application/json');
$pdo = new PDO("mysql:host=localhost;dbname=disaster_db", "root", "");

// Floods
$stmt = $pdo->query("SELECT id, country, date, horizon_days, flood_probability AS probability, risk_level 
                     FROM flood_predictions ORDER BY date DESC");
$floods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Droughts
$stmt = $pdo->query("SELECT id, country, date, horizon_days, probability, risk_level 
                     FROM drought_predictions ORDER BY date DESC");
$droughts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "floods" => $floods,
    "droughts" => $droughts
]);
