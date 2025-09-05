<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'disaster_db';
$user = 'root';
$pass = ''; // your MySQL password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => $conn->connect_error]);
    exit();
}

$disaster_type = $_POST['disaster_type'];
$location_type = $_POST['location_type'];

$stmt = $conn->prepare("SELECT police, fire, ambulance, shelters, hotline FROM emergency_contacts WHERE disaster_type=? AND location_type=? LIMIT 1");
$stmt->bind_param("ss", $disaster_type, $location_type);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([
        'police' => 'N/A',
        'fire' => 'N/A',
        'ambulance' => 'N/A',
        'shelters' => 'N/A',
        'hotline' => 'N/A'
    ]);
}

$stmt->close();
$conn->close();
?>
