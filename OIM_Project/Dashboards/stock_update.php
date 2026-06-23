<?php
require_once dirname(__DIR__) . "/db_connect.php";

function getFile($name){
    if(isset($_FILES[$name]) && $_FILES[$name]['error']==0){
        return file_get_contents($_FILES[$name]['tmp_name']);
    }
    return null;
}

$stock_id = $_POST['stock_id'];
$item_id = $_POST['item_id'];
$model_id = $_POST['model_id'];
$quantity = $_POST['quantity'];
$rate = $_POST['rate'];
$invoice_no = $_POST['invoice_no'];
$invoice_dt = $_POST['invoice_dt'];
$remarks = $_POST['remarks'];

$file = getFile('invoice_dtl');

if($file){

$sql = "UPDATE stock_rcpt SET 
item_id=?, model_id=?, quantity=?, rate=?, invoice_no=?, invoice_dt=?, invoice_dtl=?, remarks=?
WHERE stock_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"ssisssssi",
$item_id,$model_id,$quantity,$rate,$invoice_no,$invoice_dt,$file,$remarks,$stock_id
);

} else {

$sql = "UPDATE stock_rcpt SET 
item_id=?, model_id=?, quantity=?, rate=?, invoice_no=?, invoice_dt=?, remarks=?
WHERE stock_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"ssissssi",
$item_id,$model_id,$quantity,$rate,$invoice_no,$invoice_dt,$remarks,$stock_id
);
}

if($stmt->execute()){
header("Location: stock_status.php");
exit();
} else {
die("Update Error: ".$stmt->error);
}
?>