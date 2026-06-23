<?php
require_once dirname(__DIR__) . "/db_connect.php";

function getFile($name){
    if(isset($_FILES[$name]) && $_FILES[$name]['error']==0){
        return file_get_contents($_FILES[$name]['tmp_name']);
    }
    return null;
}

$item_id = $_POST['item_id'];
$model_id = $_POST['model_id'];
$quantity = $_POST['quantity'];
$rate = $_POST['rate'];
$invoice_no = $_POST['invoice_no'];
$invoice_dt = $_POST['invoice_dt'];
$remarks = $_POST['remarks'];

$file = getFile('invoice_dtl');

$sql = "INSERT INTO stock_rcpt
(item_id, model_id, quantity, rate, invoice_no, invoice_dt, invoice_dtl, remarks)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssisssss",
    $item_id,
    $model_id,
    $quantity,
    $rate,
    $invoice_no,
    $invoice_dt,
    $file,
    $remarks
);

if($stmt->execute()){
    header("Location: stock_status.php");
    exit();
} else {
    die("Insert Error: ".$stmt->error);
}
?>