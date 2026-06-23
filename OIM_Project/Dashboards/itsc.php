<?php

session_start();
require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "ITSC") {
    header("Location: ../Logins/login.php");
    exit();
}

/* 🔥 IMPORTANT (FIX MAX_JOIN_SIZE ERROR) */
$conn->query("SET SQL_BIG_SELECTS=1");

/* ================= APPROVE / REJECT ================= */

if (isset($_POST['action'])) {

    $req_id = $_POST['req_id'];
    $action = $_POST['action'];

    $status = ($action == "Approved") ? 'Y' : 'N';

    /* GET REQUISITION DATA */
    $getReq = $conn->prepare("
        SELECT model_id, quantity 
        FROM requisitions 
        WHERE req_id = ?
    ");
    $getReq->bind_param("i", $req_id);
    $getReq->execute();
    $reqData = $getReq->get_result()->fetch_assoc();

    $model_id = $reqData['model_id'];
    $req_qty  = $reqData['quantity'];

    /* ================= STOCK CHECK ================= */

    if($status == 'Y'){

        $checkStock = $conn->prepare("
            SELECT quantity 
            FROM stock_rcpt
            WHERE model_id = ?
            AND remarks = 'OB'
            LIMIT 1
        ");
        $checkStock->bind_param("s", $model_id);
        $checkStock->execute();
        $stockData = $checkStock->get_result()->fetch_assoc();

        $available_stock = $stockData['quantity'] ?? 0;

        if($available_stock < $req_qty){
            echo "<script>alert('Insufficient Stock! Cannot Approve'); window.location='itsc.php';</script>";
            exit();
        }
    }

    /* ================= UPDATE REQUISITION ================= */

    $update = $conn->prepare("
        UPDATE requisitions 
        SET itsc_approve = ?, approve_date = CURDATE() 
        WHERE req_id = ?
    ");

    if(!$update){
        die("Update Error: ".$conn->error);
    }

    $update->bind_param("si", $status, $req_id);
    $update->execute();

    /* ================= STOCK DEDUCTION ================= */

    if($status == 'Y'){

        $stock_update = $conn->prepare("
            UPDATE stock_rcpt
            SET quantity = quantity - ?
            WHERE model_id = ?
            AND remarks = 'OB'
            AND quantity >= ?
            LIMIT 1
        ");

        $stock_update->bind_param("isi", $req_qty, $model_id, $req_qty);

        if(!$stock_update->execute()){
            die("Stock Update Failed: ".$conn->error);
        }
    }

    echo "<script>alert('Requisition $action Successfully'); window.location='itsc.php';</script>";
    exit();
}

/* ================= FETCH DATA (OPTIMIZED) ================= */

$sql = "
SELECT 
    r.req_id,
    COALESCE(s.section_name, u.full_name) AS section_name,
    i.item_name,
    m.model_name,
    r.quantity,

    IFNULL(sr.quantity, 0) AS stock_quantity,

    CURDATE() AS today_date,

    last.last_approve_date

FROM requisitions r

LEFT JOIN sections s ON r.section_id = s.section_id
LEFT JOIN users u ON r.requested_by = u.user_id
LEFT JOIN items i ON r.item_id = i.item_id
LEFT JOIN models m ON r.model_id = m.model_id

/* ✅ ONLY OB STOCK */
LEFT JOIN (
    SELECT model_id, quantity
    FROM stock_rcpt
    WHERE remarks = 'OB'
) sr ON sr.model_id = r.model_id

/* ✅ LAST APPROVAL */
LEFT JOIN (
    SELECT 
        section_id,
        model_id,
        MAX(approve_date) AS last_approve_date
    FROM requisitions
    WHERE itsc_approve = 'Y'
    GROUP BY section_id, model_id
) last 
ON last.section_id = r.section_id 
AND last.model_id = r.model_id

WHERE 
    r.section_forward = 'Y'
    AND r.bo_approve = 'F'
    AND (r.itsc_approve IS NULL OR r.itsc_approve = '')

ORDER BY r.req_id DESC
";

$result = $conn->query($sql);

if(!$result){
    die('Query Error: '.$conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ITSC Final Approval</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial,sans-serif;

    background:
    linear-gradient(
        135deg,
        #edf2f7,
        #dfe7f0
    );

    padding:18px;
}

/* MAIN */

.main-wrapper{

    max-width:1600px;

    margin:auto;
}

/* LETTERHEAD */

.letterhead{

    background:#fff;

    border-radius:18px;

    overflow:hidden;

    border:1px solid #d7dfe8;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.06);
}

.header-top{

    background:
    linear-gradient(
        135deg,
        #f6f2e8,
        #ece7d8
    );

    border-bottom:4px solid #b8860b;

    padding:14px 18px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:15px;
}

.logo{

    width:65px;
    height:65px;

    object-fit:contain;
}

.header-center{

    flex:1;

    text-align:center;
}

.header-center .hindi{

    font-size:13px;

    font-weight:600;

    color:#8a6916;
}

.header-center .eng{

    margin-top:4px;

    font-size:20px;

    font-weight:700;

    color:#1a3a6b;
}

.header-center .sub{

    margin-top:5px;

    font-size:13px;

    color:#666;
}

/* CONTENT */

.content{

    padding:18px;
}

/* SESSION BAR */

.session-bar{

    background:#f7f1e3;

    border:1px solid #dccb98;

    border-radius:10px;

    padding:10px 14px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:20px;
}

.session-user{

    font-size:13px;

    color:#333;

    display:flex;

    align-items:center;

    gap:7px;
}

.session-user strong{

    color:#1a3a6b;
}

.back-btn{

    text-decoration:none;

    color:#1a3a6b;

    font-size:13px;

    font-weight:600;

    display:flex;

    align-items:center;

    gap:5px;
}

/* CARD */

.dashboard-card{

    background:#fff;

    border:1px solid #dbe2ea;

    border-radius:14px;

    padding:24px;
}

.page-title{

    font-size:24px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:24px;

    display:flex;

    align-items:center;

    gap:10px;
}

/* TABLE */

.table-wrapper{

    overflow-x:auto;
}

.data-table{

    width:100%;

    border-collapse:collapse;

    min-width:1300px;
}

.data-table thead th{

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    padding:14px;

    font-size:13px;

    text-align:center;
}

.data-table tbody td{

    padding:14px;

    border-bottom:1px solid #e5ebf2;

    font-size:13px;

    text-align:center;

    vertical-align:middle;
}

.data-table tbody tr:hover{

    background:#f8fbff;
}

/* STOCK */

.low-stock{

    color:#c62828;

    font-weight:700;
}

/* BUTTONS */

.action-group{

    display:flex;

    flex-direction:column;

    gap:8px;

    align-items:center;
}

.approve-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #2e7d32,
        #1b5e20
    );

    color:#fff;

    padding:10px 16px;

    border-radius:10px;

    cursor:pointer;

    font-size:13px;

    font-weight:600;

    min-width:110px;
}

.reject-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #c62828,
        #8e0000
    );

    color:#fff;

    padding:10px 16px;

    border-radius:10px;

    cursor:pointer;

    font-size:13px;

    font-weight:600;

    min-width:110px;
}

.disabled-btn{

    background:#9e9e9e !important;

    cursor:not-allowed;
}

/* NO DATA */

.no-data{

    padding:30px;

    text-align:center;

    color:#666;

    font-size:14px;
}

/* WARNING */

.warning{

    color:#c62828;

    font-size:11px;

    font-weight:600;
}

/* FOOTER */

.footer-note{

    margin-top:18px;

    text-align:center;

    font-size:12px;

    color:#666;
}

/* MOBILE */

@media(max-width:768px){

    .header-top{

        flex-direction:column;
    }

    .session-bar{

        flex-direction:column;

        gap:10px;

        align-items:flex-start;
    }
}

</style>

</head>

<body>

<div class="main-wrapper">

<div class="letterhead">

    <!-- HEADER -->

    <div class="header-top">

        <img
        src="/project/OIM_Project/assets/ashoka.png"
        class="logo">

        <div class="header-center">

            <div class="hindi">

                भारतीय लेखा तथा लेखा-परीक्षा विभाग

            </div>

            <div class="eng">

                Indian Audit And Accounts Department

            </div>

            <div class="sub">

                Office Item Management System

            </div>

        </div>

        <img
        src="/project/OIM_Project/assets/ag_logo.png"
        class="logo">

    </div>

    <!-- CONTENT -->

    <div class="content">

        <!-- SESSION -->

        <div class="session-bar">

            <div class="session-user">

                <i class="ti ti-user-circle"></i>

                Logged in as:

                <strong>

                    <?php echo $_SESSION['userid']; ?>

                </strong>

            </div>

            <a
            href="/project/OIM_Project/dashboards/itsc_dashboard.php"
            class="back-btn">

                <i class="ti ti-arrow-left"></i>

                Back to Dashboard

            </a>

        </div>

        <!-- CARD -->

        <div class="dashboard-card">

            <div class="page-title">

                <i class="ti ti-checklist"></i>

                ITSC Final Approval

            </div>

            <!-- TABLE -->

            <div class="table-wrapper">

                <table class="data-table">

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
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if($result && $result->num_rows > 0){ ?>

                        <?php while($row = $result->fetch_assoc()){ 

                        $stock = $row['stock_quantity'] ?? 0;
                        $qty   = $row['quantity'];

                        $disableApprove = ($qty > $stock);

                        ?>

                        <tr>

                            <td>

                                <?php echo $row['req_id']; ?>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($row['section_name']); ?>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($row['item_name']); ?>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($row['model_name']); ?>

                            </td>

                            <td>

                                <?php echo $row['quantity']; ?>

                            </td>

                            <td class="<?php echo ($stock <= 2) ? 'low-stock' : ''; ?>">

                                <?php echo $stock; ?>

                                <?php if($stock <= 2){ ?>

                                    <br>

                                    ⚠ Low Stock

                                <?php } ?>

                            </td>

                            <td>

                                <?php echo $row['today_date']; ?>

                            </td>

                            <td>

                                <?php 

                                echo $row['last_approve_date']

                                ? date('d-M-Y', strtotime($row['last_approve_date']))

                                : 'N/A';

                                ?>

                            </td>

                            <td>

                                <form method="POST">

                                    <input
                                    type="hidden"
                                    name="req_id"
                                    value="<?php echo $row['req_id']; ?>">

                                    <div class="action-group">

                                        <button
                                        class="approve-btn <?php if($disableApprove) echo 'disabled-btn'; ?>"
                                        name="action"
                                        value="Approved"
                                        <?php if($disableApprove) echo "disabled"; ?>>

                                            Approve

                                        </button>

                                        <button
                                        class="reject-btn"
                                        name="action"
                                        value="Rejected">

                                            Reject

                                        </button>

                                        <?php if($disableApprove){ ?>

                                            <div class="warning">

                                                Insufficient Stock

                                            </div>

                                        <?php } ?>

                                    </div>

                                </form>

                            </td>

                        </tr>

                        <?php } ?>

                    <?php } else { ?>

                        <tr>

                            <td colspan="9" class="no-data">

                                No Pending Requisitions

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- FOOTER -->

        <div class="footer-note">

            Office Item Management System · Government Use Only

        </div>

    </div>

</div>

</div>

</body>

</html>