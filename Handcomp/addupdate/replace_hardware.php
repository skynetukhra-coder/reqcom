<?php
require_once __DIR__ . "/../config/db_connect.php";

$existing_id = $_POST['existing_id'];
$current_id  = $_POST['current_id'];

/* FETCH BOTH */
$e = $conn->query("SELECT * FROM hw_inventory WHERE id=$existing_id")->fetch_assoc();
$c = $conn->query("SELECT * FROM hw_inventory WHERE id=$current_id")->fetch_assoc();

/* SWAP PLACED */
$conn->query("UPDATE hw_inventory SET placed='{$c['placed']}' WHERE id=$existing_id");
$conn->query("UPDATE hw_inventory SET placed='{$e['placed']}' WHERE id=$current_id");

header("Location: add_update_hardware.php");
exit();