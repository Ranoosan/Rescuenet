<?php
require_once "db.php";

if(isset($_POST['country'])){
    $country = $_POST['country'];
    $stmt = $pdo->prepare("SELECT city_name FROM asian_locations WHERE country_name=? ORDER BY city_name ASC");
    $stmt->execute([$country]);
    $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo '<option value="">Select city</option>';
    foreach($cities as $city){
        echo '<option value="'.htmlspecialchars($city).'">'.htmlspecialchars($city).'</option>';
    }
}
?>
