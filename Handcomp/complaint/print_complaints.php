<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . "/../config/db_connect.php";

/* LOGIN CHECK */
if(!isset($_SESSION['username'])){
    header("Location: ../login.php");
    exit();
}

/* DATE INPUTS */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

if($from == '' || $to == ''){
    die("Date range not selected.");
}

/* QUERY */
$sql = "
SELECT *
FROM complaint
WHERE DATE(received_time) BETWEEN ? AND ?
ORDER BY received_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Complaint Report</title>

<style>
body{
    margin:0;
    background:#f5f7fb;
    font-family:'DM Sans',sans-serif;
    color:#0d2c54;
}

/* PAGE WRAPPER */
.page-body{
    max-width:1500px;
    margin:auto;
    padding:28px;
}

/* HEADER CARD */
.report-header{
    background:white;
    padding:18px 20px;
    border-radius:14px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    margin-bottom:18px;
}

.report-header h2{
    margin:0;
    font-family:'EB Garamond',serif;
    font-size:28px;
    color:#0d2c54;
}

.meta{
    margin-top:6px;
    font-size:13px;
    color:#6b7280;
}

/* BUTTON */
.print-btn{
    margin-top:12px;
    background:#0d2c54;
    color:white;
    border:none;
    padding:10px 14px;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}

.print-btn:hover{
    background:#081a33;
}

/* TABLE WRAPPER */
.table-wrap{
    background:white;
    border-radius:14px;
    overflow:auto;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    font-size:12px;
    min-width:1200px;
}

thead{
    background:#0d2c54;
    color:white;
}

th{
    padding:12px;
    text-align:left;
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.5px;
}

td{
    padding:12px;
    border-bottom:1px solid #edf2f7;
    color:#111827;
}

tbody tr:hover{
    background:#f8fbff;
}

/* PRINT SETTINGS */
@page{
    size: landscape;
    margin:10mm;
}

@media print{

    body{
        background:white;
    }

    .print-btn{
        display:none;
    }

    .report-header{
        box-shadow:none;
        border:none;
    }

    .table-wrap{
        box-shadow:none;
        border:none;
    }
}
</style>
</head>

<body>

<div class="page-body">

    <div class="report-header">

        <h2>Complaint Report</h2>

        <div class="meta">
            From: <b><?= htmlspecialchars($from) ?></b>
            &nbsp; | &nbsp;
            To: <b><?= htmlspecialchars($to) ?></b>
        </div>

        <button class="print-btn" onclick="window.print()">
            🖨 Print / Save PDF
        </button>

    </div>

    <div class="table-wrap">

        <table>

            <thead>
            <tr>
                <th>Comp No</th>
                <th>Forwarded By</th>
                <th>Device</th>
                <th>Serial No</th>
                <th>Complaint</th>
                <th>Remarks</th>
                <th>Received Time</th>
                <th>Assigned Time</th>
                <th>Ongoing Time</th>
                <th>Assigned To</th>
            </tr>
            </thead>

            <tbody>

            <?php if($result->num_rows > 0){ ?>

                <?php while($row = $result->fetch_assoc()){ ?>

                    <tr>
                        <td><?= htmlspecialchars($row['comp_no']) ?></td>
                        <td><?= htmlspecialchars($row['forwarded_by']) ?></td>
                        <td><?= htmlspecialchars($row['device']) ?></td>
                        <td><?= htmlspecialchars($row['serial_no'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['complaint']) ?></td>
                        <td><?= htmlspecialchars($row['remarks'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['received_time']) ?></td>
                        <td><?= htmlspecialchars($row['assigned_time'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['ongoing_time'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['assigned_to'] ?? 'Pending') ?></td>
                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="10" style="text-align:center;padding:20px;">
                        No records found for selected date range
                    </td>
                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>