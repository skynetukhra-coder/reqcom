<?php
session_start();

require_once __DIR__ . "/../config/db_connect.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $comp_no     = $_POST['comp_no'];
    $assigned_to = $_POST['assigned_to'];

    $stmt = $conn->prepare("
    UPDATE complaint
    SET assigned_to = ?,
        assigned_time = NOW()
    WHERE comp_no = ?
    ");

    $stmt->bind_param("ss", $assigned_to, $comp_no);

    $stmt->execute();
}

header("Location: complaint_register.php");
exit();
?>