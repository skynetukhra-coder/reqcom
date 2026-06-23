<?php
require_once __DIR__ . "/config/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $forwarded_by = $_POST['forwarded_by'];
    $device       = $_POST['device'];
    $serial       = $_POST['serial'];
    $complaint    = $_POST['complaint'];

    /* Handle remarks safely */
    $remarks = $_POST['remarks'] ?? $_POST['remarks_hidden'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO complaint 
        (forwarded_by, device, serial_no, complaint, remarks, status) 
        VALUES (?, ?, ?, ?, ?, 'Pending')
    ");

    $stmt->bind_param("sssss", 
        $forwarded_by, 
        $device, 
        $serial, 
        $complaint, 
        $remarks
    );

    if ($stmt->execute()) {

        /* Redirect based on source page */
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'branch_officer.php') !== false) {
            header("Location: branch_officer.php?success=1");
        } else {
            header("Location: section_officer.php?success=1");
        }

        exit();

    } else {
        echo "Error inserting complaint";
    }

} // ✅ THIS WAS MISSING OR BROKEN IN YOUR FILE
?>