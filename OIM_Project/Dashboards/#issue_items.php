<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/OIM_Project/db_connect.php";

/* AUTH CHECK */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "ITSC") {
    header("Location: /OIM_Project/Logins/login.php");
    exit();
}

/* FETCH MODELS FOR DROPDOWN */
$models = $conn->query("SELECT model_id, model_name FROM models ORDER BY model_id");

/* DEFAULT QUERY */
$where = "WHERE r.itsc_approve = 'Y'";

/* HANDLE REPORT FILTER */
if(isset($_POST['generate'])){

    $model_id = $_POST['model_id'];
    $fy       = $_POST['financial_year'];

    if(!empty($model_id) && !empty($fy)){

        /* FY SPLIT */
        $year = explode("-", $fy);
        $start = $year[0] . "-04-01";
        $end   = $year[1] . "-03-31";

        $where .= " AND r.model_id = '$model_id'
                    AND r.approve_date BETWEEN '$start' AND '$end'";
    }
}

/* FINAL QUERY */
$sql = "
SELECT 
    r.req_id,
    COALESCE(s.section_name, u.full_name) AS section_name,
    i.item_name,
    m.model_id,
    m.model_name,
    r.quantity,
    r.approve_date

FROM requisitions r
LEFT JOIN sections s ON r.section_id = s.section_id
LEFT JOIN users u ON r.requested_by = u.user_id
LEFT JOIN items i ON r.item_id = i.item_id
LEFT JOIN models m ON r.model_id = m.model_id

$where

ORDER BY r.req_id DESC
";

$result = $conn->query($sql);

if(!$result){
    die("Query Error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Issued Items Report</title>

<link rel="stylesheet" href="/OIM_Project/css/loginstyle.css">

<style>
.container { width: 95%; margin: 40px auto; }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
th { background: #28a745; color: white; }

.filter-box {
    background: #fff;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
}
</style>
</head>

<body>

<div class="login-container container">

<h2>Issued Items Report</h2>

<p>Logged in as: <b><?php echo $_SESSION['userid']; ?></b></p>

<!-- ✅ FILTER FORM -->
<div class="filter-box">
<form method="POST">

    <label>Model:</label>
    <select name="model_id" required>
        <option value="">Select Model</option>
        <?php while($m = $models->fetch_assoc()){ ?>
            <option value="<?php echo $m['model_id']; ?>">
                <?php echo $m['model_id']." - ".$m['model_name']; ?>
            </option>
        <?php } ?>
    </select>

    <label>Financial Year:</label>
    <select name="financial_year" required>
        <option value="">Select FY</option>
        <option value="2024-2025">2024-2025</option>
        <option value="2025-2026">2025-2026</option>
        <option value="2026-2027">2026-2027</option>
    </select>

    <button type="submit" name="generate">Generate</button>

</form>
</div>

<!-- ✅ TABLE -->

<table>
<thead>
<tr>
    <th>Req ID</th>
    <th>Section</th>
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
?>

<tr>
    <td><?php echo $row['req_id']; ?></td>
    <td><?php echo htmlspecialchars($row['section_name']); ?></td>
    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
    <td><?php echo $row['model_id']; ?></td>
    <td><?php echo htmlspecialchars($row['model_name']); ?></td>
    <td><?php echo $row['quantity']; ?></td>
    <td>
        <?php 
        echo $row['approve_date'] 
        ? date('d-M-Y', strtotime($row['approve_date'])) 
        : 'N/A';
        ?>
    </td>
</tr>

<?php
    }
} else {
    echo "<tr><td colspan='7'>No Data Found</td></tr>";
}
?>

</tbody>
</table>

<br>
<a href="/OIM_Project/dashboards/itsc_dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>