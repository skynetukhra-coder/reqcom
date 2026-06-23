<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/OIM_Project/db_connect.php";

/* AUTH CHECK */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "ITSC") {
    header("Location: /OIM_Project/Logins/login.php");
    exit();
}

/* HANDLE APPROVE / REJECT */
if (isset($_POST['action'])) {

    $req_id = $_POST['req_id'];
    $action = $_POST['action'];

    $status = ($action == "Approved") ? 'Y' : 'N';

    /* 🔒 BACKEND STOCK CHECK */
    if($status == 'Y'){

        $check_sql = "
            SELECT 
                r.quantity,
                (
                    SELECT IFNULL(SUM(sr.quantity),0)
                    FROM stock_rcpt sr
                    WHERE sr.model_id = r.model_id
                ) AS stock
            FROM requisitions r
            WHERE r.req_id = ?
        ";

        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("i", $req_id);
        $stmt_check->execute();
        $check = $stmt_check->get_result()->fetch_assoc();

        if(($check['stock'] ?? 0) < $check['quantity']){
            echo "<script>alert('Insufficient Stock! Cannot Approve'); window.location='itsc.php';</script>";
            exit();
        }
    }

    /* UPDATE REQUISITION */
    $update = "UPDATE requisitions 
               SET itsc_approve = ?, approve_date = CURDATE() 
               WHERE req_id = ?";

    $stmt = $conn->prepare($update);

    if(!$stmt){
        die("Update Error: " . $conn->error);
    }

    $stmt->bind_param("si", $status, $req_id);
    $stmt->execute();

    /* STOCK DEDUCTION ONLY IF APPROVED */
    if($status == 'Y'){

        // Get requisition details
        $get_req = "SELECT model_id, quantity FROM requisitions WHERE req_id = ?";
        $stmt_req = $conn->prepare($get_req);
        $stmt_req->bind_param("i", $req_id);
        $stmt_req->execute();
        $req = $stmt_req->get_result()->fetch_assoc();

        $model_id = $req['model_id'];
        $deduct_qty = $req['quantity'];

        // Deduct stock safely (single row)
        $update_stock = "
            UPDATE stock_rcpt
            SET quantity = GREATEST(quantity - ?, 0)
            WHERE model_id = ?
            ORDER BY stock_id DESC
            LIMIT 1
        ";

        $stmt2 = $conn->prepare($update_stock);
        $stmt2->bind_param("is", $deduct_qty, $model_id);
        $stmt2->execute();
    }

    echo "<script>alert('Requisition $action Successfully'); window.location='itsc.php';</script>";
    exit();
}

/* FETCH DATA */

$sql = "
SELECT 
    r.req_id,

    COALESCE(s.section_name, u.full_name) AS section_name,

    i.item_name,
    m.model_name,
    r.quantity,

    (
        SELECT IFNULL(SUM(sr.quantity),0)
        FROM stock_rcpt sr
        WHERE sr.model_id = r.model_id
    ) AS stock_quantity,

    CURDATE() AS today_date,

    (
        SELECT MAX(r2.approve_date)
        FROM requisitions r2
        WHERE r2.section_id = r.section_id
        AND r2.model_id = r.model_id
        AND r2.itsc_approve = 'Y'
        AND r2.req_id <> r.req_id
    ) AS last_approve_date

FROM requisitions r

LEFT JOIN sections s ON r.section_id = s.section_id
LEFT JOIN users u ON r.requested_by = u.user_id
LEFT JOIN items i ON r.item_id = i.item_id
LEFT JOIN models m ON r.model_id = m.model_id

WHERE 
    r.section_forward = 'Y'
    AND r.bo_approve = 'F'
    AND (r.itsc_approve IS NULL OR r.itsc_approve = '')

ORDER BY r.req_id DESC
";

$result = $conn->query($sql);

if(!$result){
    die('Query Error: ' . $conn->error);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>ITSC Final Approval</title>

    <link rel="stylesheet" href="/OIM_Project/css/loginstyle.css">

    <style>
        .itsc-container { width: 95%; margin: 40px auto; }

        table { width: 100%; border-collapse: collapse; background: white; }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th { background: #007bff; color: white; }

        button { padding: 6px 12px; margin: 2px; }

        .approve { background: green; color: white; }
        .reject { background: red; color: white; }

        .disabled-btn {
            background: gray !important;
            cursor: not-allowed;
        }

        .low-stock {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="login-container itsc-container">

    <h2>ITSC Final Approval</h2>

    <p>Logged in as: <b><?php echo $_SESSION['userid']; ?></b></p>

    <table>
        <thead>
            <tr>
                <th>Req ID</th>
                <th>Section</th>
                <th>Item</th>
                <th>Model</th>
                <th>Qty</th>
                <th>Available Stock</th>
                <th>Today</th>
                <th>Last Approve Date</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

        <?php if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                $stock = $row['stock_quantity'] ?? 0;
                $qty   = $row['quantity'];
                $disableApprove = ($qty > $stock);
        ?>

        <tr>

            <td><?= $row['req_id']; ?></td>
            <td><?= htmlspecialchars($row['section_name']); ?></td>
            <td><?= htmlspecialchars($row['item_name']); ?></td>
            <td><?= htmlspecialchars($row['model_name']); ?></td>
            <td><?= $row['quantity']; ?></td>

            <td class="<?= ($stock <= 2) ? 'low-stock' : ''; ?>">
                <?= $stock; ?>
                <?php if($stock <= 2) echo " ⚠ Low Stock"; ?>
            </td>

            <td><?= $row['today_date']; ?></td>

            <td>
                <?= $row['last_approve_date'] 
                    ? date('d-M-Y', strtotime($row['last_approve_date'])) 
                    : 'N/A'; ?>
            </td>

            <td>
                <form method="POST">
                    <input type="hidden" name="req_id" value="<?= $row['req_id']; ?>">

                    <button 
                        class="approve <?= $disableApprove ? 'disabled-btn' : ''; ?>" 
                        name="action" 
                        value="Approved"
                        <?= $disableApprove ? "disabled" : ""; ?>
                    >
                        Approve
                    </button>

                    <button class="reject" name="action" value="Rejected">
                        Reject
                    </button>

                    <?php if($disableApprove){ ?>
                        <br><small style="color:red;">Insufficient Stock</small>
                    <?php } ?>

                </form>
            </td>

        </tr>

        <?php 
            }
        } else {
            echo "<tr><td colspan='9'>No Pending Requisitions</td></tr>";
        }
        ?>

        </tbody>
    </table>

    <br>
    <a href="/OIM_Project/dashboards/itsc_dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>