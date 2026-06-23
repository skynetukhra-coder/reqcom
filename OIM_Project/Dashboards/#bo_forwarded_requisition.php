<?php

require_once 'app.php'; // Load database functions
session_start();
include "../db_connect.php";

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "Branch Officer") {
    header("Location: login.php");
    exit();
}

$boId = $_SESSION['userid'];
$message = "";

// Handle status update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $reqId = (int)$_POST['requisition_id'];
    $newStatus = $_POST['decision'];
    $remarks = $_POST['remarks'];

    // Update the database using the function in app.php
    if (updateRequisitionStatus($reqId, $newStatus, $remarks)) {
        $message = "Requisition #$reqId updated to $newStatus.";
    }
}

// Fetch real pending requisitions for this BO from the database
$requisitions = fetchRequisitionsForBranchOfficer($boId, ['pending']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forwarded Requisitions</title>
    <link rel="stylesheet" href="/OIM_Project/css/loginstyle.css">
    <style>
        .status-msg { color: green; font-weight: bold; margin-bottom: 10px; text-align:center; }
        table { width: 100%; border-collapse: collapse; background: white; }
    </style>
</head>
<body>

<div class="login-container" style="width:850px;">
    <h2>Forwarded Requisitions</h2>
    
    <?php if ($message): ?>
        <p class="status-msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Section</th>
                <th>Item (Model)</th>
                <th>Qty</th>
                <th>Decision & Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($requisitions)): ?>
                <tr><td colspan="5" style="text-align:center;">No pending requisitions found.</td></tr>
            <?php else: ?>
                <?php foreach ($requisitions as $req): ?>
                <tr>
                    <form method="POST">
                        <input type="hidden" name="requisition_id" value="<?php echo $req['id']; ?>">
                        <td><?php echo htmlspecialchars($req['section_name']); ?></td>
                        <td><?php echo htmlspecialchars($req['item'] . " (" . $req['model'] . ")"); ?></td>
                        <td><?php echo $req['quantity']; ?></td>
                        <td>
                            <select name="decision" required>
                                <option value="Approved">Approve</option>
                                <option value="Rejected">Reject</option>
                                <option value="Forwarded to ITSC">Forward to ITSC</option>
                            </select>
                            <input type="text" name="remarks" placeholder="Add remarks..." style="width:100%; margin-top:5px;">
                        </td>
                        <td>
                            <button type="submit" name="update_status">Submit</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="bo_home.php">⬅ Back to Dashboard</a>
</div>
</body>
</html>