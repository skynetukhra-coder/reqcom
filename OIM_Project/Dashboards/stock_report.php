<?php
session_start();
require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if($_SESSION['role'] != "ITSC"){
    header("Location: ../Logins/login.php");
    exit();
}

/* FETCH MODELS */
$models = $conn->query("SELECT DISTINCT model_id FROM stock_rcpt ORDER BY model_id");

/* GENERATE REPORT */
$report = null;

if(isset($_POST['generate'])){

    $model_id = $_POST['model_id'];
    $fy       = $_POST['financial_year'];

    /* FINANCIAL YEAR LOGIC */
    list($start_year, $end_year) = explode('-', $fy);

    $start_date = $start_year . "-04-01";
    $end_date   = $end_year . "-03-31";

    /* ✅ FILTER ONLY PURCHASE + CB */
    $sql = "
    SELECT *
    FROM stock_rcpt
    WHERE model_id = ?
    AND remarks IN ('PURCHASE','CB')
    AND DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $model_id, $start_date, $end_date);
    $stmt->execute();

    $report = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Stock Report</title>

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

    max-width:1500px;

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

    padding:20px;
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

    margin-bottom:22px;
}

.session-user{

    display:flex;

    align-items:center;

    gap:8px;

    font-size:13px;

    color:#333;
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

    border-radius:16px;

    padding:24px;
}

.page-title{

    font-size:24px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:25px;

    display:flex;

    align-items:center;

    gap:10px;
}

/* FILTER */

.filter-box{

    background:#f8fbff;

    border:1px solid #d9e3ef;

    border-radius:14px;

    padding:18px;

    margin-bottom:25px;
}

.filter-form{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));

    gap:16px;
}

.form-group{

    display:flex;

    flex-direction:column;

    gap:7px;
}

.form-group label{

    font-size:13px;

    font-weight:600;

    color:#1a3a6b;
}

.form-group select{

    padding:11px 12px;

    border:1px solid #cfd8e3;

    border-radius:10px;

    background:#fff;

    font-size:13px;
}

.form-group select:focus{

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

    border-radius:12px;

    padding:12px 20px;

    cursor:pointer;

    font-size:14px;

    font-weight:600;

    margin-top:10px;

    width:220px;
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

    margin-top:20px;

    background:#fff3f3;

    border:1px solid #f1c7c7;

    color:#c62828;

    padding:14px;

    border-radius:10px;

    font-size:14px;

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
            href="stock_status.php"
            class="back-btn">

                <i class="ti ti-arrow-left"></i>

                Back to Stock Register

            </a>

        </div>

        <!-- CARD -->

        <div class="dashboard-card">

            <div class="page-title">

                <i class="ti ti-report-search"></i>

                Stock Register Report

            </div>

            <!-- FILTER -->

            <div class="filter-box">

                <form method="POST" class="filter-form">

                    <div class="form-group">

                        <label>Select Model</label>

                        <select name="model_id" required>

                            <option value="">

                                Select Model

                            </option>

                            <?php while($m = $models->fetch_assoc()){ ?>

                            <option value="<?php echo $m['model_id']; ?>">

                                <?php echo $m['model_id']; ?>

                            </option>

                            <?php } ?>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Financial Year</label>

                        <select name="financial_year" required>

                        <?php

                        $currentYear = date('Y');
                        $currentMonth = date('m');

                        if($currentMonth < 4){

                            $currentFYStart = $currentYear - 1;

                        } else {

                            $currentFYStart = $currentYear;
                        }

                        for($i = 0; $i < 5; $i++){

                            $start = $currentFYStart - $i;
                            $end = $start + 1;

                            echo "<option value='$start-$end'>$start - $end</option>";
                        }

                        ?>

                        </select>

                    </div>

                    <div>

                        <button
                        type="submit"
                        name="generate"
                        class="generate-btn">

                            <i class="ti ti-file-analytics"></i>

                            Generate Report

                        </button>

                    </div>

                </form>

            </div>

            <!-- REPORT TABLE -->

            <?php if($report && $report->num_rows > 0){ ?>

            <div class="table-wrapper">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Item</th>
                            <th>Remarks</th>
                            <th>Model</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Invoice No</th>
                            <th>Invoice Date</th>
                            <th>Created</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($row = $report->fetch_assoc()){ ?>

                    <tr>

                        <td>

                            <?php echo $row['stock_id']; ?>

                        </td>

                        <td>

                            <?php echo $row['item_id']; ?>

                        </td>

                        <td>

                            <?php echo $row['remarks']; ?>

                        </td>

                        <td>

                            <?php echo $row['model_id']; ?>

                        </td>

                        <td>

                            <?php echo $row['quantity']; ?>

                        </td>

                        <td>

                            <?php echo $row['rate'] ?? '-'; ?>

                        </td>

                        <td>

                            <?php echo $row['invoice_no'] ?? '-'; ?>

                        </td>

                        <td>

                            <?php
                            echo $row['invoice_dt']
                            ? date('d-M-Y', strtotime($row['invoice_dt']))
                            : '-';
                            ?>

                        </td>

                        <td>

                            <?php echo $row['created_at']; ?>

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

            <?php } elseif(isset($_POST['generate'])){ ?>

                <div class="no-data">

                    No records found for selected filters.

                </div>

            <?php } ?>

        </div>

        <!-- FOOTER -->

         <div style="display:flex; gap:20px; align-items:center;">

        </div>

    </div>

</div>

</div>

</body>

</html>