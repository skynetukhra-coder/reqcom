<?php

session_start();
require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "Branch Officer") {
    header("Location: ../Logins/login.php");
    exit();
}

$boId = $_SESSION['userid'];
$message = "";

/* UPDATE STATUS */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {

    $reqId = $_POST['requisition_id'];
    $decision = $_POST['decision'];

    /* CONVERT TO DB VALUES */
    if($decision == "Approved by BO"){
        $newStatus = 'Y';
    }
    elseif($decision == "Rejected by BO"){
        $newStatus = 'N';
    }
    elseif($decision == "Forwarded to ITSC"){
        $newStatus = 'F';
    }

    $sql_update = "UPDATE requisitions 
                   SET bo_approve = ?, approve_date = CURDATE() 
                   WHERE req_id = ?";

    $stmt = $conn->prepare($sql_update);

    if(!$stmt){
        die("Update Error: " . $conn->error);
    }

    $stmt->bind_param("si", $newStatus, $reqId);

    if($stmt->execute()){
        $message = "Requisition #$reqId updated successfully.";
    }
}

/* GET BO USER_ID FROM USERS TABLE */

$username = $_SESSION['userid'];

$sql_user = "SELECT user_id FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);

$stmt_user->bind_param("s", $username);
$stmt_user->execute();

$res_user = $stmt_user->get_result();
$user = $res_user->fetch_assoc();

if(!$user){
    die("BO user not found");
}

$boId = $user['user_id'];  

/* FETCH REQUISITIONS FOR THIS BO */

$sql = "SELECT 
            r.req_id,
            s.section_name,
            i.item_name,
            m.model_name,
            r.quantity
        FROM requisitions r
        LEFT JOIN sections s ON r.section_id = s.section_id
        LEFT JOIN items i ON r.item_id = i.item_id
        LEFT JOIN models m ON r.model_id = m.model_id
        WHERE r.assigned_bo = ?
        AND r.section_forward = 'Y'
        AND (r.bo_approve IS NULL OR r.bo_approve = '')
        ORDER BY r.req_id DESC";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("Fetch Error: " . $conn->error);
}

$stmt->bind_param("i", $boId);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Forwarded Requisitions</title>

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

    max-width:1400px;

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

    margin-bottom:18px;
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

    gap:6px;
}

/* CARD */

.dashboard-card{

    background:#fff;

    border:1px solid #dbe2ea;

    border-radius:14px;

    padding:22px;
}

.page-title{

    font-size:22px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:20px;

    display:flex;

    align-items:center;

    gap:10px;
}

/* MESSAGE */

.status-msg{

    background:#e8f5e9;

    border:1px solid #b7dfbb;

    color:#1b5e20;

    padding:12px 15px;

    border-radius:10px;

    margin-bottom:18px;

    font-size:14px;

    font-weight:600;
}

/* TABLE */

.table-wrapper{

    overflow-x:auto;
}

.data-table{

    width:100%;

    border-collapse:collapse;

    min-width:900px;
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

    text-align:left;
}

.data-table tbody td{

    padding:14px;

    border-bottom:1px solid #e5ebf2;

    font-size:13px;

    vertical-align:middle;
}

.data-table tbody tr:hover{

    background:#f8fbff;
}

/* SELECT */

.action-select{

    width:100%;

    height:42px;

    border-radius:10px;

    border:1px solid #cfd8e3;

    background:#f8fafc;

    padding:0 12px;

    font-size:13px;
}

.action-select:focus{

    outline:none;

    border-color:#1a3a6b;

    background:#fff;
}

/* BUTTON */

.submit-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    padding:10px 16px;

    border-radius:10px;

    font-size:13px;

    font-weight:600;

    cursor:pointer;

    display:inline-flex;

    align-items:center;

    gap:6px;
}

.submit-btn:hover{

    opacity:0.95;
}

/* NO DATA */

.no-data{

    text-align:center;

    padding:25px;

    color:#666;

    font-size:14px;
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
        src="../assets/ashoka.png"
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
        src="../assets/ag_logo.png"
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
            href="bo_home.php"
            class="back-btn">

                <i class="ti ti-arrow-left"></i>

                Back to Dashboard

            </a>

        </div>

        <!-- CARD -->

        <div class="dashboard-card">

            <div class="page-title">

                <i class="ti ti-checklist"></i>

                Forwarded Requisitions

            </div>

            <!-- MESSAGE -->

            <?php if ($message): ?>

                <div class="status-msg">

                    <?php echo $message; ?>

                </div>

            <?php endif; ?>

            <!-- TABLE -->

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th>Section</th>

                            <th>Item / Model</th>

                            <th>Quantity</th>

                            <th>Decision</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php if ($result->num_rows == 0): ?>

                        <tr>

                            <td colspan="5" class="no-data">

                                No pending requisitions found.

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php while ($req = $result->fetch_assoc()): ?>

                        <tr>

                            <form method="POST">

                                <input
                                type="hidden"
                                name="requisition_id"
                                value="<?php echo $req['req_id']; ?>">

                                <td>

                                    <?php echo htmlspecialchars($req['section_name']); ?>

                                </td>

                                <td>

                                    <strong>

                                        <?php echo htmlspecialchars($req['item_name']); ?>

                                    </strong>

                                    <br>

                                    <span style="color:#666; font-size:12px;">

                                        <?php echo htmlspecialchars($req['model_name']); ?>

                                    </span>

                                </td>

                                <td>

                                    <?php echo $req['quantity']; ?>

                                </td>

                                <td>

                                    <select
                                    name="decision"
                                    class="action-select"
                                    required>

                                        <option value="Forwarded to ITSC">

                                            Forward to ITSC

                                        </option>

                                        <option value="Rejected by BO">

                                            Reject

                                        </option>

                                    </select>

                                </td>

                                <td>

                                    <button
                                    type="submit"
                                    name="update_status"
                                    class="submit-btn">

                                        <i class="ti ti-check"></i>

                                        Submit

                                    </button>

                                </td>

                            </form>

                        </tr>

                        <?php endwhile; ?>

                    <?php endif; ?>

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