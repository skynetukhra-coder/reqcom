<?php
require_once dirname(__DIR__) . "/db_connect.php";

if(!isset($_GET['id'])){
    die("Invalid Request");
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT invoice_dtl FROM stock_rcpt WHERE stock_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    die("File not found");
}

$row = $res->fetch_assoc();

if(!$row['invoice_dtl']){
    die("No file available");
}

/* IMPORTANT HEADERS */
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=invoice.pdf");

echo $row['invoice_dtl'];
exit;
?>