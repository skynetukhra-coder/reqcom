<?php

session_start();
require_once __DIR__ . "/../config/db_connect.php";

if(!isset($_SESSION['username'])){
    header("Location: ../login.php");
    exit();
}

$designation = $_SESSION['designation'];
$username = $_SESSION['username'];
$name_section = $_SESSION['name_section'] ?? '';

$search = trim($_GET['search'] ?? '');

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

/* DATE FILTER */
$date_filter = "";

if($from_date && $to_date){
    $date_filter = "AND DATE(received_time) BETWEEN '$from_date' AND '$to_date'";
}
elseif($from_date){
    $date_filter = "AND DATE(received_time) >= '$from_date'";
}
elseif($to_date){
    $date_filter = "AND DATE(received_time) <= '$to_date'";
}

/* SEARCH */
$like = "%$search%";

/* COUNT QUERY */
if($designation == "AMC"){

    $count_sql = "
    SELECT COUNT(*) as total
    FROM complaint
    WHERE assigned_to = ?
    AND resolved_time IS NULL
    $date_filter
    ";

    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $username);
    $count_stmt->execute();
    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];

} else {

    $count_sql = "
    SELECT COUNT(*) as total
    FROM complaint
    WHERE resolved_time IS NULL
    $date_filter
    ";

    $total_records = $conn->query($count_sql)->fetch_assoc()['total'];
}

$total_pages = max(1, ceil($total_records / $limit));

/* MAIN QUERY WITH PAGINATION */

if($designation == "AMC"){

    $sql = "
SELECT *
FROM complaint
WHERE assigned_to = ?
AND resolved_time IS NULL
$date_filter
AND (
    comp_no LIKE ?
    OR forwarded_by LIKE ?
    OR device LIKE ?
    OR serial_no LIKE ?
    OR complaint LIKE ?
    OR remarks LIKE ?
)
ORDER BY received_time DESC
LIMIT $limit OFFSET $offset
";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssss",
        $username,
        $like,
        $like,
        $like,
        $like,
        $like,
        $like
    );

    $stmt->execute();
    $result = $stmt->get_result();

}
else{

    $sql = "
SELECT *
FROM complaint
WHERE resolved_time IS NULL
$date_filter
AND (
    comp_no LIKE ?
    OR forwarded_by LIKE ?
    OR device LIKE ?
    OR serial_no LIKE ?
    OR complaint LIKE ?
    OR remarks LIKE ?
)
ORDER BY received_time DESC
LIMIT $limit OFFSET $offset
";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssss",
        $like,
        $like,
        $like,
        $like,
        $like,
        $like
    );

    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Complaint Register
</title>

<link rel="preconnect"
href="https://fonts.googleapis.com">

<link rel="preconnect"
href="https://fonts.gstatic.com"
crossorigin>

<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
rel="stylesheet">

<style>

body{
    margin:0;
    background:#f5f7fb;
    font-family:'DM Sans',sans-serif;
}

/* PAGE */

.page-body{
    max-width:1500px;
    margin:auto;
    padding:28px;
}

/* TITLE */

.page-title{
    margin-bottom:20px;
}

.page-title h2{
    margin:0;
    color:#0d2c54;
    font-size:30px;
    font-family:'EB Garamond',serif;
}

/* TABLE */

.table-wrap{
    background:white;
    border-radius:14px;
    overflow:auto;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.data-table{
    width:100%;
    border-collapse:collapse;
}

.data-table thead{
    background:#0d2c54;
}

.data-table th{
    color:white;
    padding:14px;
    font-size:12px;
    text-transform:uppercase;
    text-align:left;
}

.data-table td{
    padding:13px 14px;
    border-bottom:1px solid #edf2f7;
    font-size:13px;
}

.data-table tbody tr:hover{
    background:#f8fbff;
}

/* BADGE */

.badge{
    display:inline-block;
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
}

.badge-assigned{
    background:#dbeafe;
    color:#1d4ed8;
}

/* BUTTONS */

.action-btn{
    border:none;
    border-radius:8px;
    padding:8px 14px;
    font-size:12px;
    font-weight:600;
    cursor:pointer;
}

.assign-btn{
    background:#0d2c54;
    color:white;
}

.ongoing-btn{
    background:#fff3cd;
    color:#856404;
}

.resolve-btn{
    background:#d1e7dd;
    color:#0f5132;
}

/* BACK LINK */

.back-link{
    display:inline-block;
    margin-top:18px;
    color:#0d2c54;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

/* FILTER BAR */

.filter-bar{
    display:flex;
    align-items:center;
    gap:10px;
    background:white;
    padding:14px 18px;
    border-radius:14px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    margin-bottom:18px;
    flex-wrap:wrap;
}

.filter-spacer{
    flex:1;
}

.filter-bar input[type="text"]{
    width:320px;
    padding:9px 12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    font-size:13px;
    outline:none;
}

.filter-bar input[type="date"]{
    padding:9px 12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    font-size:13px;
    outline:none;
}

.date-label{
    font-size:13px;
    font-weight:600;
    color:#6b7280;
}

.filter-bar button{
    background:#0d2c54;
    color:white;
    border:none;
    border-radius:8px;
    padding:9px 14px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
}

.filter-bar button:hover{
    opacity:.9;
}

.pdf-btn{
    background:#0d2c54;
    color:white;
    text-decoration:none;
    border-radius:8px;
    padding:9px 14px;
    font-size:13px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
}

.pdf-btn:hover{
    opacity:.9;
}

/* GLOBAL PAGINATION STYLES */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    margin-top: 24px;
}

.pagination a {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 6px;
    background: white;
    border: 1px solid #d1d5db;
    color: #0d2c54;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.15s ease;
}

.pagination a:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination a.active {
    background: #0d2c54;
    color: white;
    border-color: #0d2c54;
}

.pagination span {
    padding: 6px 4px;
    color: #6b7280;
    font-size: 12px;
}

@media print {
    body{
        background:#fff !important;
        margin:0;
        padding:0;
    }

    /* Hide everything except report table */
    .page-title,
    .filter-bar,
    .back-link,
    #successPopup,
    .action-btn,
    .pagination,
    a,
    button,
    select,
    form{
        display:none !important;
    }

    .page-body{
        max-width:100%;
        padding:0;
        margin:0;
    }

    .table-wrap{
        box-shadow:none !important;
        border:none !important;
        overflow:visible !important;
    }

    .data-table{
        width:100%;
        font-size:10px;
        border-collapse:collapse;
    }

    .data-table th,
    .data-table td{
        border:1px solid #000;
        padding:4px;
        word-break:break-word;
    }

    .data-table thead{
        background:#fff !important;
    }

    .data-table th{
        color:#000 !important;
    }

    @page{
        size:landscape;
        margin:10mm;
    }
}

</style>

</head>

<body>

<?php if(isset($_GET['updated'])){ ?>

<div id="successPopup"
style="
position:fixed;
top:24px;
right:24px;
background:#dcfce7;
color:#166534;
padding:14px 20px;
border-radius:10px;
font-size:13px;
font-weight:600;
box-shadow:0 8px 20px rgba(0,0,0,.12);
z-index:9999;
">

✔ Hardware Updated Successfully

</div>

<script>

setTimeout(function(){
    var popup = document.getElementById('successPopup');
    if(popup){
        popup.style.display = 'none';
    }
}, 2500);

</script>

<?php } ?>

<div class="page-body">

<div class="page-title"
style="
display:flex;
justify-content:space-between;
align-items:center;
gap:20px;
flex-wrap:wrap;
">

<div>

<h2 style="
margin:0;
color:#0d2c54;
font-size:30px;
font-family:'EB Garamond',serif;
">
Complaint Register
</h2>

<p style="
margin-top:4px;
font-size:13px;
color:#6b7280;
">
Welcome,
<strong>
<?php echo htmlspecialchars($_SESSION['name_section'] ?? $_SESSION['full_name']); ?>
</strong>
</p>

</div>

<a href="../logout.php"
style="
background:#dc2626;
color:white;
text-decoration:none;
padding:10px 16px;
border-radius:8px;
font-size:13px;
font-weight:600;
display:inline-flex;
align-items:center;
gap:8px;
">
Logout
</a>

</div>

<form method="GET" class="filter-bar">

    <input
    type="text"
    name="search"
    placeholder="🔍 Search complaint no, device, serial no..."
    value="<?php echo htmlspecialchars($search); ?>">

    <button type="submit">
        Search
    </button>

    <div class="filter-spacer"></div>

    <label class="date-label">
        From
    </label>

    <input
    type="date"
    name="from_date"
    value="<?php echo htmlspecialchars($_GET['from_date'] ?? ''); ?>">

    <label class="date-label">
        To
    </label>

    <input
    type="date"
    name="to_date"
    value="<?php echo htmlspecialchars($_GET['to_date'] ?? ''); ?>">

    <button
type="button"
onclick="
window.open(
'print_complaints.php?from=' +
document.querySelector('[name=from_date]').value +
'&to=' +
document.querySelector('[name=to_date]').value,
'_blank'
);
">
🖨 Print
</button>

</form>

<div class="table-wrap">

<table class="data-table">

<thead>

<tr>
<th>Comp No</th>
<th>Forwarded By</th>
<th>Device</th>
<th>Serial No</th>
<th>Complaint</th>
<th>Remarks</th>
<th>Received Time</th>
<th>Assigned Time</th>
<th>Ongoing Time</th>
<th>Assigned To</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td>
<?php echo htmlspecialchars($row['comp_no']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['forwarded_by']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['device']); ?>
</td>

<td>
<?php
echo htmlspecialchars(
    $row['serial_no']
    ?? '-'
);
?>
</td>

<td>
<?php echo htmlspecialchars($row['complaint']); ?>
</td>

<td>
<?php
echo htmlspecialchars(
    $row['remarks']
    ?? '-'
);
?>
</td>

<td>
<?php echo htmlspecialchars($row['received_time']); ?>
</td>

<td>
<?php
echo !empty($row['assigned_time'])
? htmlspecialchars($row['assigned_time'])
: '-';
?>
</td>

<td>
<?php
echo !empty($row['ongoing_time'])
? htmlspecialchars($row['ongoing_time'])
: '-';
?>
</td>

<td>
<?php if(!empty($row['assigned_to'])){ ?>
    <span class="badge badge-assigned">
    <?php echo htmlspecialchars($row['assigned_to']); ?>
    </span>
<?php } else { ?>
    <span style="color:#6b7280; font-size:12px;">
    Pending
    </span>
<?php } ?>
</td>

<td>
<?php if($designation == "ITSC"){ ?>
    <?php if(empty($row['assigned_to'])){ ?>
        <form method="POST"
        action="assign_complaint.php"
        style="display:flex; align-items:center; gap:8px;">

        <input type="hidden"
        name="comp_no"
        value="<?php echo htmlspecialchars($row['comp_no']); ?>">

        <select name="assigned_to"
        required
        style="
        padding:7px 10px;
        border:1px solid #d1d5db;
        border-radius:8px;
        font-size:12px;
        min-width:180px;
        outline:none;
        ">
           <option value="">Select Engineer</option>
           <option value="sahas">Subhadeep Saha</option>
           <option value="sayanm">Sayan Mondal</option>
           <option value="arijits">Arijit Santra</option>
           <option value="saikats">Saikat Sadhukhan</option>
        </select>

        <button type="submit"
        class="action-btn assign-btn">
        Assign
        </button>

        </form>
    <?php } else { ?>
        <?php if(empty($row['ongoing_time'])){ ?>
            <span style="color:#059669; font-size:12px; font-weight:600;">
            Assigned
            </span>
        <?php } elseif(empty($row['resolved_time'])){ ?>
            <span style="color:#d97706; font-size:12px; font-weight:600;">
            Ongoing
            </span>
        <?php } else { ?>
            <span style="color:#16a34a; font-size:12px; font-weight:600;">
            Resolved
            </span>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php if($designation == "AMC"){ ?>
    <?php if(empty($row['ongoing_time'])){ ?>
        <form method="POST"
        action="mark_ongoing.php">
        <input type="hidden"
        name="comp_no"
        value="<?php echo htmlspecialchars($row['comp_no']); ?>">
        <button type="submit"
        class="action-btn ongoing-btn">
        Ongoing
        </button>
        </form>
    <?php } elseif(empty($row['resolved_time'])){ ?>
        <form method="POST"
        action="mark_resolved.php">
        <input type="hidden"
        name="comp_no"
        value="<?php echo htmlspecialchars($row['comp_no']); ?>">
        <button type="submit"
        class="action-btn resolve-btn">
        Resolved
        </button>
        </form>
    <?php } else { ?>
        <span style="color:#16a34a; font-size:12px; font-weight:600;">
        Resolved
        </span>
    <?php } ?>
<?php } ?>
</td>

</tr>
<?php } ?>

</tbody>
</table>
</div>

<div class="pagination">
<?php
$qs = http_build_query([
    'search' => $search,
    'from_date' => $from_date,
    'to_date' => $to_date
]);

$visible = 7;
$start = max(1, $page - 3);
$end = min($total_pages, $start + $visible - 1);

if ($start > 1) {
    echo "<a href='?page=1&$qs'>1</a>";
    if ($start > 2) echo "<span>…</span>";
}

for ($i = $start; $i <= $end; $i++) {
    $active = ($i == $page) ? "active" : "";
    echo "<a class='$active' href='?page=$i&$qs'>$i</a>";
}

if ($end < $total_pages) {
    if ($end < $total_pages - 1) echo "<span>…</span>";
    echo "<a href='?page=$total_pages&$qs'>$total_pages</a>";
}
?>
</div>

<a href="../itsc.php" class="back-link">⬅ Back to Dashboard</a>

</div>

</body>
</html>