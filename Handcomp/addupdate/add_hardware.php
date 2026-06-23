<?php
require_once __DIR__ . "/../config/db_connect.php";

unset($_POST['id']);

$cols = array_keys($_POST);
$vals = array_values($_POST);

$sql = "INSERT INTO hw_inventory (" . implode(",", $cols) . ")
        VALUES (" . str_repeat("?,", count($cols)-1) . "?)";

$stmt = $conn->prepare($sql);

$types = str_repeat("s", count($vals));
$stmt->bind_param($types, ...$vals);

$stmt->execute();

header("Location: add_update_hardware.php");
exit();