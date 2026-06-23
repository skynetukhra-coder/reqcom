<?php

session_start();

require_once __DIR__ . "/../config/db_connect.php";

/* CHECK */

if($_SERVER['REQUEST_METHOD'] != 'POST'){

    header("Location: add_update_hardware.php");
    exit();
}

/* ID */

$id = (int)$_POST['id'];

/*
|--------------------------------------------------------------------------
| BUILD UPDATE QUERY
|--------------------------------------------------------------------------
*/

$updates = [];

foreach($_POST as $key => $value){

    if($key == 'id'){
        continue;
    }

    $safe_key   = $conn->real_escape_string($key);
    $safe_value = $conn->real_escape_string($value);

    $updates[] = "`$safe_key` = '$safe_value'";
}

/* QUERY */

$sql = "
UPDATE hw_inventory
SET " . implode(", ", $updates) . "
WHERE id = $id
";

$conn->query($sql);

/*
|--------------------------------------------------------------------------
| REDIRECT BACK WITH SUCCESS
|--------------------------------------------------------------------------
*/

header("Location: ../addupdate/add_update_hardware.php?updated=1");

exit();
?>