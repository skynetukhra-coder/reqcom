<?php
/**
 * get_section_history.php — Endpoint for Real-Time AJAX Component Fetching
 */
session_start();
require_once __DIR__ . "/config/db_connect.php";

// Access Control Protection
if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(["error" => "Access denied"]);
    exit();
}

$section = trim($_GET['section'] ?? '');
$history = [];

if ($section !== '') {
    // Collect both unresolved and resolved complaints targeting the dynamic user section field safely
    // Note: Since both active and resolved historical datasets live inside the 'complaint' schema, 
    // a single targeted look-up cleanly yields historical performance states.
    $sql = "
        SELECT comp_no, device, serial_no, complaint, received_time, assigned_time, ongoing_time, resolved_time 
        FROM complaint 
        WHERE forwarded_by = ? 
        ORDER BY CASE WHEN resolved_time IS NULL THEN 0 ELSE 1 END, received_time DESC 
        LIMIT 15
    ";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $section);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        $stmt->close();
    }
}

// Return JSON response safely
header('Content-Type: application/json');
echo json_encode($history);
exit();