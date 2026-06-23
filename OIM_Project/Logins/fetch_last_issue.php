<?php
include "db_connect.php";
 
  /*🔜 LATER SQL LOGIC:*/

  $sql = "SELECT last_issued_date FROM issue_history 
  WHERE section_name = ? AND item_type = ?";


$item = $_GET['item'] ?? '';

$dummyData = [
    "Cartridge" => "15-Dec-2025",
    "Battery"   => "05-Jan-2026"
];

if (isset($dummyData[$item])) {
    echo "Last issued on: " . $dummyData[$item];
} else {
    echo "No previous issue record found";
}
