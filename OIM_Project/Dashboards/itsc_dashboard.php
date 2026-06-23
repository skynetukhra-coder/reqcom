<?php
session_start();

require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "ITSC"){
    header("Location: ../Logins/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ITSC Dashboard</title>

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

.logout-btn{

    text-decoration:none;

    color:#8b1a1a;

    font-size:13px;

    font-weight:600;

    display:flex;

    align-items:center;

    gap:5px;
}

/* DASHBOARD CARD */

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

    margin-bottom:28px;

    display:flex;

    align-items:center;

    gap:10px;
}

/* GRID */

.dashboard-grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(260px,1fr));

    gap:22px;
}

/* TILE */

.dashboard-tile{

    background:
    linear-gradient(
        135deg,
        #ffffff,
        #f5f8fc
    );

    border:1px solid #d8e0ea;

    border-radius:16px;

    padding:28px 24px;

    text-decoration:none;

    transition:0.25s;

    display:block;
}

.dashboard-tile:hover{

    transform:translateY(-4px);

    box-shadow:
    0 10px 25px rgba(0,0,0,0.08);
}

.tile-icon{

    width:60px;
    height:60px;

    border-radius:14px;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:30px;

    margin-bottom:18px;
}

.tile-title{

    font-size:18px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:8px;
}

.tile-sub{

    font-size:13px;

    color:#666;

    line-height:1.6;
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

                Welcome

                <strong>

                    <?php echo $_SESSION['name']; ?>

                </strong>

            </div>

            <a
            href="/project/OIM_Project/Logins/logout.php"
            class="logout-btn">

                <i class="ti ti-logout"></i>

                Logout

            </a>

        </div>

        <!-- DASHBOARD -->

        <div class="dashboard-card">

            <div class="page-title">

                <i class="ti ti-layout-dashboard"></i>

                ITSC Dashboard

            </div>

            <div class="dashboard-grid">

                <!-- REQUISITIONS -->

                <a
                href="/project/OIM_Project/Dashboards/itsc.php"
                class="dashboard-tile">

                    <div class="tile-icon">

                        <i class="ti ti-checklist"></i>

                    </div>

                    <div class="tile-title">

                        Requisitions

                    </div>

                    <div class="tile-sub">

                        Review and process requisitions forwarded by Branch Officers.

                    </div>

                </a>

                <!-- ISSUE -->

                <a
                href="/project/OIM_Project/Dashboards/issue_items.php"
                class="dashboard-tile">

                    <div class="tile-icon">

                        <i class="ti ti-package"></i>

                    </div>

                    <div class="tile-title">

                        Issue Items

                    </div>

                    <div class="tile-sub">

                        Manage issued items and dispatch records.

                    </div>

                </a>

                <!-- STOCK -->

                <a
                href="/project/OIM_Project/Dashboards/stock_status.php"
                class="dashboard-tile">

                    <div class="tile-icon">

                        <i class="ti ti-archive"></i>

                    </div>

                    <div class="tile-title">

                        Add Stock

                    </div>

                    <div class="tile-sub">

                        Maintain stock inventory and add new stock entries.

                    </div>

                </a>

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