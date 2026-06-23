<?php
/**
 * itsc.php — Final Professional Dashboard
 */

session_start();
require_once __DIR__ . "/config/db_connect.php";

/* ACCESS CONTROL */
if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: login.php");
    exit();
}

/* HEADER SETTINGS */
$active_nav     = 'dashboard';
$logo_base_path = '';

/* ───────────────────────────────────────────── */
/* DASHBOARD STATS */
/* ───────────────────────────────────────────── */

/* Total Hardware */
$total_hw = $conn->query("
SELECT COUNT(*) as total 
FROM hw_inventory
")->fetch_assoc()['total'];

/* Pending Complaints */
$pending = $conn->query("
SELECT COUNT(*) as total 
FROM complaint 
WHERE status='Pending'
")->fetch_assoc()['total'];

/* Ongoing */
$ongoing = $conn->query("
SELECT COUNT(*) as total 
FROM complaint 
WHERE status='Ongoing'
")->fetch_assoc()['total'];

/* Resolved This Month */
$resolved = $conn->query("
SELECT COUNT(*) as total
FROM complaint
WHERE status='Resolved'
AND MONTH(resolved_time)=MONTH(CURRENT_DATE())
AND YEAR(resolved_time)=YEAR(CURRENT_DATE())
")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ITSC Dashboard — Handcomp</title>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- Global CSS -->
<link rel="stylesheet" href="assets/css/style.css">

<style>

/* ───────────────────────────────────────────── */
/* PAGE CONTENT */
/* ───────────────────────────────────────────── */

.page-content{
    width:95%;
    max-width:1400px;
    margin:auto;
    padding:30px 0;
}

/* ───────────────────────────────────────────── */
/* WELCOME BANNER */
/* ───────────────────────────────────────────── */

.welcome-banner{
    background:
    linear-gradient(
        120deg,
        var(--navy) 0%,
        var(--navy-mid) 100%
    );

    color:var(--white);

    border-radius:var(--radius-lg);

    padding:24px 30px;

    margin-bottom:28px;

    display:flex;
    align-items:center;
    justify-content:space-between;

    box-shadow:var(--shadow-md);

    border-left:5px solid var(--gold);

    flex-wrap:wrap;

    gap:12px;
}

.welcome-banner h3{
    font-family:var(--font-serif);
    font-size:24px;
    font-weight:600;
    color:var(--white);
}

.welcome-banner p{
    font-size:13px;
    color:rgba(255,255,255,.62);
    margin-top:4px;
}

.badge-role{
    background:var(--gold);

    color:var(--navy-dark);

    padding:6px 16px;

    border-radius:20px;

    font-size:11.5px;

    font-weight:700;

    letter-spacing:.7px;

    text-transform:uppercase;
}

/* ───────────────────────────────────────────── */
/* STATS GRID */
/* ───────────────────────────────────────────── */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:34px;
}

.stat-card{
    background:var(--white);

    border-radius:var(--radius-lg);

    padding:24px;

    box-shadow:var(--shadow-sm);

    border:1.5px solid var(--gray-200);

    position:relative;

    overflow:hidden;
}

.stat-card::after{
    content:'';
    position:absolute;
    bottom:0;
    left:0;
    width:100%;
    height:3px;

    background:
    linear-gradient(
        90deg,
        var(--navy) 0%,
        var(--gold) 100%
    );
}

.stat-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
}

.stat-card h4{
    font-size:12px;

    text-transform:uppercase;

    letter-spacing:1px;

    color:var(--gray-500);

    margin-bottom:12px;
}

.stat-card .number{
    font-size:34px;
    font-weight:700;
    color:var(--navy);
}

/* ICONS */

.stat-icon{

    width:52px;
    height:52px;

    border-radius:14px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:24px;

    flex-shrink:0;
}

/* HARDWARE */
.hardware-icon{
    background:rgba(13,44,84,.08);
    color:var(--navy);
}

/* PENDING */
.pending-icon{
    background:rgba(245,158,11,.12);
    color:#d97706;
}

/* ONGOING */
.ongoing-icon{
    background:rgba(59,130,246,.10);
    color:#2563eb;
}

/* RESOLVED */
.resolved-icon{
    background:rgba(34,197,94,.12);
    color:#16a34a;
}

/* ───────────────────────────────────────────── */
/* SECTION LABEL */
/* ───────────────────────────────────────────── */

.section-label{
    font-family:var(--font-serif);

    font-size:18px;

    color:var(--navy);

    font-weight:600;

    margin-bottom:14px;

    padding-bottom:8px;

    border-bottom:1px solid var(--gray-200);
}

/* ───────────────────────────────────────────── */
/* MODULE GRID */
/* ───────────────────────────────────────────── */

.module-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:18px;
}

.module-card{
    background:var(--white);

    border-radius:var(--radius-lg);

    padding:28px 24px;

    box-shadow:var(--shadow-sm);

    border:1.5px solid var(--gray-200);

    text-decoration:none;

    display:block;

    transition:
    transform .22s,
    box-shadow .22s,
    border-color .22s;

    position:relative;

    overflow:hidden;
}

.module-card::after{
    content:'';

    position:absolute;

    bottom:0;
    left:0;
    right:0;

    height:3px;

    background:
    linear-gradient(
        90deg,
        var(--navy) 0%,
        var(--gold) 100%
    );

    transform:scaleX(0);

    transform-origin:left;

    transition:transform .25s;
}

.module-card:hover{
    transform:translateY(-4px);

    box-shadow:var(--shadow-md);

    border-color:var(--navy);

    text-decoration:none;
}

.module-card:hover::after{
    transform:scaleX(1);
}

.module-card .mc-icon{
    font-size:34px;
    margin-bottom:14px;
    display:block;
}

.module-card h4{
    font-family:var(--font-serif);

    font-size:18px;

    color:var(--navy);

    margin-bottom:8px;

    font-weight:600;
}

.module-card p{
    font-size:13px;

    color:var(--gray-500);

    line-height:1.6;
}

/* ───────────────────────────────────────────── */
/* RESPONSIVE */
/* ───────────────────────────────────────────── */

@media(max-width:992px){

    .stats-grid{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:600px){

    .welcome-banner{
        flex-direction:column;
        align-items:flex-start;
    }

    .stats-grid{
        grid-template-columns:1fr;
    }

    .module-grid{
        grid-template-columns:1fr;
    }
}

</style>

</head>
<body>

<!-- HEADER -->
<?php require_once __DIR__ . '/includes/header_include.php'; ?>

<!-- PAGE CONTENT -->
<main class="page-content">

<!-- WELCOME -->
<div class="welcome-banner">

    <div>
        <h3>
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </h3>

        <p>
            ITSC Officer Portal ·
            <?php echo date('l, d F Y'); ?>
        </p>
    </div>

    <div class="badge-role">
        IT Support Cell
    </div>

</div>

<!-- STATS -->
<div class="stats-grid">

    <!-- HARDWARE -->
    <div class="stat-card">

        <div class="stat-top">

            <div>
                <h4>Total Hardware</h4>
                <div class="number"><?php echo $total_hw; ?></div>
            </div>

            <div class="stat-icon hardware-icon">
                💻
            </div>

        </div>

    </div>

    <!-- PENDING -->
    <div class="stat-card">

        <div class="stat-top">

            <div>
                <h4>Pending Complaints</h4>
                <div class="number"><?php echo $pending; ?></div>
            </div>

            <div class="stat-icon pending-icon">
                ⏳
            </div>

        </div>

    </div>

    <!-- ONGOING -->
    <div class="stat-card">

        <div class="stat-top">

            <div>
                <h4>Ongoing</h4>
                <div class="number"><?php echo $ongoing; ?></div>
            </div>

            <div class="stat-icon ongoing-icon">
                🔧
            </div>

        </div>

    </div>

    <!-- RESOLVED -->
    <div class="stat-card">

        <div class="stat-top">

            <div>
                <h4>Resolved This Month</h4>
                <div class="number"><?php echo $resolved; ?></div>
            </div>

            <div class="stat-icon resolved-icon">
                ✅
            </div>

        </div>

    </div>

</div>

<!-- QUICK ACCESS -->
<p class="section-label">Quick Access</p>

<div class="module-grid">

    <!-- HARDWARE -->
    <a href="addupdate/add_update_hardware.php" class="module-card">

        <span class="mc-icon">&#128421;</span>

        <h4>Add / Update Hardware</h4>

        <p>
            Manage hardware inventory, update allocation,
            replace hardware and maintain section-wise records.
        </p>

    </a>

    <!-- COMPLAINT -->
    <a href="complaint/complaint_register.php" class="module-card">

        <span class="mc-icon">&#128203;</span>

        <h4>Complaint Register</h4>

        <p>
            Monitor pending and ongoing complaints received
            from sections and branch officers.
        </p>

    </a>

    <!-- RESOLVED -->
    <a href="complaint/resolved_complaints.php" class="module-card">

        <span class="mc-icon">&#9989;</span>

        <h4>Resolved Complaints</h4>

        <p>
            Access historical complaint records with
            resolution timeline and closure details.
        </p>

    </a>

    <!-- DISPOSAL -->
    <a href="addupdate/generate_disposal.php" class="module-card">

        <span class="mc-icon">&#128465;</span>

        <h4>Disposal List</h4>

        <p>
            Generate disposal lists for hardware assets
            older than six years.
        </p>

    </a>

</div>

</main>

<!-- FOOTER -->
<?php require_once __DIR__ . '/includes/footer_include.php'; ?>

</body>
</html>