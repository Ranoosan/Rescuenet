<?php
session_start();
header('Content-Type: application/json');

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'error'=>'User not logged in']);
    exit;
}
$user_id = $_SESSION['user_id'];

$host = "localhost";
$user = "root";
$pass = "";
$db = "disaster_db";

$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error) {
    echo json_encode(['success'=>false,'error'=>'DB Connection Failed']);
    exit;
}

// Collect POST data
$disaster_type       = $_POST['disaster_type'];
$location_type       = $_POST['location_type'];
$latitude            = $_POST['latitude'];
$longitude           = $_POST['longitude'];
$elevation_m         = $_POST['elevation_m'];
$population_density  = $_POST['population_density'];
$avg_rainfall_mm     = $_POST['avg_rainfall_mm'];
$wind_speed_kmh      = $_POST['wind_speed_kmh'];
$temp_c              = $_POST['temp_c'];
$vulnerability_index = $_POST['vulnerability_index'];

// ✅ Now include user_id in insert
$stmt = $conn->prepare("INSERT INTO user_disaster_inputs 
(user_id, disaster_type, location_type, latitude, longitude, elevation_m, population_density, avg_rainfall_mm, wind_speed_kmh, temp_c, vulnerability_index) 
VALUES (?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param(
    "issddddddds", 
    $user_id, 
    $disaster_type, 
    $location_type, 
    $latitude, 
    $longitude, 
    $elevation_m, 
    $population_density, 
    $avg_rainfall_mm, 
    $wind_speed_kmh, 
    $temp_c, 
    $vulnerability_index
);

if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
