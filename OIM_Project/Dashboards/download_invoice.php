<?php
require_once dirname(__DIR__) . "/db_connect.php";

$id = $_GET['id'];

$sql = "SELECT invoice_dtl FROM stock_rcpt WHERE stock_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

header("Content-Type: application/pdf");
echo $row['invoice_dtl'];
?>