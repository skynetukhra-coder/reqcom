<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: ../login.php");
    exit();
}

$type = $_GET['type'] ?? "";
$result = null;

/* FETCH OLD DEVICES */
if($type != ""){

    $stmt = $conn->prepare("
        SELECT *
        FROM hw_inventory
        WHERE type=?
        AND date_of_purchase <= DATE_SUB(CURDATE(), INTERVAL 6 YEAR)
        ORDER BY date_of_purchase ASC
    ");

    $stmt->bind_param("s", $type);
    $stmt->execute();

    $result = $stmt->get_result();
}

/* FETCH COLUMNS */
$columns = [];

$col_res = $conn->query("DESCRIBE hw_inventory");

while($c = $col_res->fetch_assoc()){
    $columns[] = $c['Field'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Generate Disposal List</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<!-- Global CSS -->
<link rel="stylesheet" href="../assets/css/style.css">

<style>

/* ───────────────────────────────────────────── */
/* PAGE */
/* ───────────────────────────────────────────── */

.page-content{
    width:95%;
    max-width:1450px;
    margin:auto;
    padding:30px 0;
}

/* ───────────────────────────────────────────── */
/* HERO */
/* ───────────────────────────────────────────── */

.hero-banner{

    background:
    linear-gradient(
        120deg,
        var(--navy) 0%,
        var(--navy-mid) 100%
    );

    color:var(--white);

    border-radius:var(--radius-lg);

    padding:26px 30px;

    margin-bottom:28px;

    box-shadow:var(--shadow-md);

    border-left:5px solid var(--gold);

    position:relative;

    overflow:hidden;
}

.hero-banner::after{

    content:'';

    position:absolute;

    width:260px;
    height:260px;

    border-radius:50%;

    border:1px solid rgba(255,255,255,.08);

    top:-120px;
    right:-80px;
}

.hero-banner h1{

    margin:0;

    font-family:var(--font-serif);

    font-size:30px;

    color:var(--white);

    font-weight:600;
}

.hero-banner p{

    margin-top:8px;

    color:rgba(255,255,255,.68);

    font-size:13px;
}

/* ───────────────────────────────────────────── */
/* FILTER CARD */
/* ───────────────────────────────────────────── */

.filter-card{

    background:var(--white);

    border-radius:var(--radius-lg);

    padding:22px;

    box-shadow:var(--shadow-sm);

    border:1.5px solid var(--gray-200);

    margin-bottom:24px;
}

.filter-row{

    display:flex;

    gap:12px;

    align-items:center;

    flex-wrap:wrap;
}

.filter-row select{

    min-width:240px;

    padding:12px 14px;

    border-radius:10px;

    border:1.5px solid var(--gray-300);

    background:#fff;

    font-size:14px;

    outline:none;
}

.filter-row select:focus{

    border-color:var(--navy);

    box-shadow:0 0 0 3px rgba(13,44,84,.08);
}

.filter-row button{

    padding:12px 18px;

    border:none;

    border-radius:10px;

    background:
    linear-gradient(
        135deg,
        var(--navy) 0%,
        var(--navy-mid) 100%
    );

    color:white;

    font-size:13px;

    font-weight:600;

    cursor:pointer;

    transition:.2s;
}

.filter-row button:hover{

    transform:translateY(-1px);

    box-shadow:0 6px 18px rgba(13,44,84,.18);
}

/* ───────────────────────────────────────────── */
/* TABLE */
/* ───────────────────────────────────────────── */

.table-wrapper{

    overflow-x:auto;

    background:var(--white);

    border-radius:var(--radius-lg);

    box-shadow:var(--shadow-sm);

    border:1.5px solid var(--gray-200);
}

table{

    border-collapse:collapse;

    width:100%;

    font-size:13px;
}

th{

    position:sticky;
    top:0;

    background:var(--navy);

    color:var(--white);

    padding:10px;

    white-space:nowrap;

    font-weight:600;

    letter-spacing:.3px;
}

td{

    padding:9px 10px;

    border-bottom:1px solid #eef2f7;

    white-space:nowrap;

    color:#374151;
}

tr:hover{
    background:#f9fafb;
}

/* ───────────────────────────────────────────── */
/* BACK */
/* ───────────────────────────────────────────── */

.back-link{
    margin-top:20px;
}

.back-link a{

    color:var(--navy);

    text-decoration:none;

    font-weight:600;

    font-size:14px;
}

/* ───────────────────────────────────────────── */
/* RESPONSIVE */
/* ───────────────────────────────────────────── */

@media(max-width:768px){

    .filter-row{
        flex-direction:column;
        align-items:stretch;
    }

    .filter-row select,
    .filter-row button{
        width:100%;
    }
}

</style>

</head>
<body>

<!-- HEADER -->
<?php require_once __DIR__ . '/../includes/header_include.php'; ?>

<!-- PAGE -->
<main class="page-content">

<!-- HERO -->
<div class="hero-banner">

    <h1>Generate Disposal List</h1>

    <p>
        Generate disposal records for hardware assets older than six years.
    </p>

</div>

<!-- FILTER -->
<div class="filter-card">

    <form method="GET">

        <div class="filter-row">

            <select name="type" required>

                <option value="">Select Device Type</option>

                <?php
                $res = $conn->query("
                    SELECT DISTINCT type 
                    FROM hw_inventory 
                    ORDER BY type ASC
                ");

                while($row = $res->fetch_assoc()){

                    $selected = ($type == $row['type']) ? "selected" : "";

                    echo "
                    <option value='{$row['type']}' $selected>
                        {$row['type']}
                    </option>
                    ";
                }
                ?>

            </select>

            <button type="submit">
                Generate Disposal List
            </button>

        </div>

    </form>

</div>

<!-- TABLE -->
<?php if($result && $result->num_rows > 0){ ?>

<div class="table-wrapper">

<table>

<tr>

<?php foreach($columns as $col){ ?>

<th>
<?php echo ucwords(str_replace("_"," ",$col)); ?>
</th>

<?php } ?>

</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<?php foreach($columns as $col){ ?>

<td>
<?php echo htmlspecialchars($row[$col]); ?>
</td>

<?php } ?>

</tr>

<?php } ?>

</table>

</div>

<?php } elseif($type != ""){ ?>

<div style="
background:#fff;
padding:18px;
border-radius:12px;
border:1px solid #e5e7eb;
color:#6b7280;
box-shadow:var(--shadow-sm);
">
No hardware found older than 6 years for selected device type.
</div>

<?php } ?>

<!-- BACK -->
<div class="back-link">

    <a href="../itsc.php">
        ⬅ Back to Dashboard
    </a>

</div>

</main>

<!-- FOOTER -->
<?php require_once __DIR__ . '/../includes/footer_include.php'; ?>

</body>
</html>