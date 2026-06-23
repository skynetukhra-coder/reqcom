<?php
require_once __DIR__ . "/../config/db_connect.php";

$id = $_GET['id'];

/* UPDATE CURRENT RECORD (AFTER STORE MOVE) */
/* You can expand this if needed */

header("Location: add_update_hardware.php");
exit();