<?php
session_start();

require_once __DIR__ . "/../config/db_connect.php";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $comp_no = $_POST['comp_no'];

    $stmt = $conn->prepare("
    UPDATE complaint
    SET resolved_time = NOW(),
        status = 'Resolved'
    WHERE comp_no = ?
    ");

    $stmt->bind_param("s", $comp_no);

    $stmt->execute();
}

header("Location: complaint_register.php");
exit();
?>