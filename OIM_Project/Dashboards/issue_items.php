<?php

session_start();
require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "ITSC") {
    header("Location: ../Logins/login.php");
    exit();
}

/* FETCH MODELS */
$models = $conn->query("SELECT model_id, model_name FROM models ORDER BY model_id");

/* BASE WHERE */
$where = "WHERE itsc_approve = 'Y'";

/* FILTER */
if(isset($_POST['generate'])){

    $model_id = $_POST['model_id'];
    $fy       = $_POST['financial_year'];

    if(!empty($model_id) && !empty($fy)){

        $year = explode("-", $fy);
        $start = $year[0] . "-04-01";
        $end   = $year[1] . "-03-31";

        $where .= " AND model_id = '$model_id'
                    AND approve_date BETWEEN '$start' AND '$end'";
    }
}

/* LIGHT QUERY (NO JOIN) */
$sql = "
SELECT req_id, section_id, item_id, model_id, quantity, approve_date, requested_by
FROM requisitions
$where
ORDER BY req_id DESC
LIMIT 50
";

$result = $conn->query($sql);

if(!$result){
    die("Query Error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Issued Items Report</title>

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

/* FILTER */

.filter-box{

    background:#f8fbff;

    border:1px solid #dbe5f0;

    border-radius:14px;

    padding:18px;

    margin-bottom:24px;
}

.filter-grid{

    display:grid;

    grid-template-columns:1fr 1fr auto;

    gap:16px;

    align-items:end;
}

.input-group label{

    display:block;

    margin-bottom:8px;

    font-size:13px;

    font-weight:600;

    color:#42566d;
}

.input-group select{

    width:100%;

    height:46px;

    border-radius:12px;

    border:1px solid #cfd8e3;

    background:#fff;

    padding:0 14px;

    font-size:14px;
}

.input-group select:focus{

    outline:none;

    border-color:#1a3a6b;

    box-shadow:
    0 0 0 3px rgba(26,58,107,0.08);
}

/* BUTTON */

.generate-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    height:46px;

    padding:0 24px;

    border-radius:12px;

    cursor:pointer;

    font-size:14px;

    font-weight:600;
}

.generate-btn:hover{

    opacity:0.95;
}

/* TABLE */

.table-wrapper{

    overflow-x:auto;
}

.data-table{

    width:100%;

    border-collapse:collapse;

    min-width:1200px;
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
}

.data-table tbody tr:hover{

    background:#f8fbff;
}

/* NO DATA */

.no-data{

    padding:30px;

    text-align:center;

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

@media(max-width:900px){

    .filter-grid{

        grid-template-columns:1fr;
    }

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
            href="itsc_dashboard.php"
            class="back-btn">

                <i class="ti ti-arrow-left"></i>

                Back to Dashboard

            </a>

        </div>

        <!-- CARD -->

        <div class="dashboard-card">

            <div class="page-title">

                <i class="ti ti-report-analytics"></i>

                Issued Items Report

            </div>

            <!-- FILTER -->

            <div class="filter-box">

                <form method="POST">

                    <div class="filter-grid">

                        <!-- MODEL -->

                        <div class="input-group">

                            <label>Select Model</label>

                            <select name="model_id" required>

                                <option value="">

                                    Select Model

                                </option>

                                <?php while($m = $models->fetch_assoc()){ ?>

                                    <option value="<?= $m['model_id']; ?>">

                                        <?= $m['model_id']." - ".$m['model_name']; ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- FY -->

                        <div class="input-group">

                            <label>Financial Year</label>

                            <select name="financial_year" required>

                                <option value="">

                                    Select FY

                                </option>

                                <option value="2024-2025">

                                    2024-2025

                                </option>

                                <option value="2025-2026">

                                    2025-2026

                                </option>

                                <option value="2026-2027">

                                    2026-2027

                                </option>

                            </select>

                        </div>

                        <!-- BUTTON -->

                        <button
                        type="submit"
                        name="generate"
                        class="generate-btn">

                            Generate Report

                        </button>

                    </div>

                </form>

            </div>

            <!-- TABLE -->

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th>Req ID</th>
                            <th>Section / BO</th>
                            <th>Item</th>
                            <th>Model ID</th>
                            <th>Model Name</th>
                            <th>Quantity</th>
                            <th>Approved Date</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php
                    if($result->num_rows > 0){

                    while($row = $result->fetch_assoc()){

                        if(!empty($row['section_id'])){

                            $res = $conn->query("SELECT section_name FROM sections WHERE section_id='".$row['section_id']."'");
                            $sec = $res->fetch_assoc()['section_name'] ?? '';

                        } else {

                            $res = $conn->query("SELECT full_name FROM users WHERE user_id='".$row['requested_by']."'");
                            $sec = "BO: " . ($res->fetch_assoc()['full_name'] ?? 'Self');
                        }

                        $item = $conn->query("SELECT item_name FROM items WHERE item_id='".$row['item_id']."'")->fetch_assoc()['item_name'] ?? '';

                        $model = $conn->query("SELECT model_name FROM models WHERE model_id='".$row['model_id']."'")->fetch_assoc()['model_name'] ?? '';
                    ?>

                    <tr>

                        <td><?= $row['req_id']; ?></td>

                        <td><?= htmlspecialchars($sec); ?></td>

                        <td><?= htmlspecialchars($item); ?></td>

                        <td><?= $row['model_id']; ?></td>

                        <td><?= htmlspecialchars($model); ?></td>

                        <td><?= $row['quantity']; ?></td>

                        <td>

                            <?= $row['approve_date']
                            ? date('d-M-Y', strtotime($row['approve_date']))
                            : 'N/A'; ?>

                        </td>

                    </tr>

                    <?php
                    }

                    } else {

                        echo "<tr>
                        <td colspan='7' class='no-data'>
                        No Data Found
                        </td>
                        </tr>";
                    }
                    ?>

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