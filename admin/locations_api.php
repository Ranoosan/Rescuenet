<?php
header('Content-Type: application/json');
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'disaster_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die(json_encode(['error' => $conn->connect_error]));

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $result = $conn->query("SELECT * FROM asian_locations ORDER BY country_name, city_name");
    $locations = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($locations);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$id = $data['id'] ?? null;
$country_name = $data['country_name'] ?? '';
$country_iso = $data['country_iso'] ?? null;
$city_name = $data['city_name'] ?? '';
$is_capital = isset($data['is_capital']) ? 1 : 0;
$notes = $data['notes'] ?? null;

try {
    if ($action === 'add') {
        $stmt = $conn->prepare("INSERT INTO asian_locations (country_name, country_iso, city_name, is_capital, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $country_name, $country_iso, $city_name, $is_capital, $notes);
        $stmt->execute();
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } elseif ($action === 'update' && $id) {
        $stmt = $conn->prepare("UPDATE asian_locations SET country_name=?, country_iso=?, city_name=?, is_capital=?, notes=? WHERE id=?");
        $stmt->bind_param("sssdis", $country_name, $country_iso, $city_name, $is_capital, $notes, $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } elseif ($action === 'delete' && $id) {
        $stmt = $conn->prepare("DELETE FROM asian_locations WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
$conn->close();
?>
