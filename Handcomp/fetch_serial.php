<?php
require_once __DIR__ . "/config/db_connect.php";

$section = $_GET['section'] ?? '';
$device  = $_GET['device'] ?? '';

$stmt = $conn->prepare("SELECT hw_number FROM hw_inventory WHERE placed=? AND type=?");
$stmt->bind_param("ss", $section, $device);
$stmt->execute();

$result = $stmt->get_result();

echo "<option value=''>Select Serial</option>";

while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['hw_number']}'>{$row['hw_number']}</option>";
}
?>