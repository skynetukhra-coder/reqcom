<?php
require_once __DIR__ . "/../config/db_connect.php";

$existing_id = $_POST['existing_id'];
$current_id  = $_POST['current_id'];

/* MOVE OLD DEVICE TO STORE */
$stmt = $conn->prepare("UPDATE hw_inventory SET placed='STORE ROOM' WHERE id=?");
$stmt->bind_param("i", $existing_id);
$stmt->execute();

/* NOW UPDATE CURRENT EDIT */
header("Location: finalize_update.php?id=".$current_id);
exit();