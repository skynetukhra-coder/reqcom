<?php
require_once __DIR__ . "/../config/db_connect.php";

$id = $_POST['id'];
$action = $_POST['action'];

if($action == "ongoing"){
    $conn->query("UPDATE complaint 
                  SET status='Ongoing', ongoing_time=NOW() 
                  WHERE comp_no=$id");
}

if($action == "resolved"){
    $conn->query("UPDATE complaint 
                  SET status='Resolved', resolved_time=NOW() 
                  WHERE comp_no=$id");
}

echo "done";