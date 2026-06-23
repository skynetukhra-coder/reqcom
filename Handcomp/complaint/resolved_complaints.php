<?php
/**
 * complaint/resolved_complaints.php
 * FINAL VERSION WITH SEARCH, PRINT, & PAGINATION
 */

session_start();
require_once __DIR__ . "/../config/db_connect.php";

/* ACCESS CONTROL */
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$search = trim($_GET['search'] ?? '');

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

/* DATE FILTER */
$date_filter = "";
if($from_date && $to_date){
    $date_filter = "AND DATE(resolved_time) BETWEEN '$from_date' AND '$to_date'";
}
elseif($from_date){
    $date_filter = "AND DATE(resolved_time) >= '$from_date'";
}
elseif($to_date){
    $date_filter = "AND DATE(resolved_time) <= '$to_date'";
}

/* SEARCH & COUNT QUERY */
$like = "%$search%";

$count_sql = "
SELECT COUNT(*) as total
FROM complaint
WHERE resolved_time IS NOT NULL
$date_filter
AND (
    comp_no LIKE ?
    OR forwarded_by LIKE ?
    OR device LIKE ?
    OR serial_no LIKE ?
    OR complaint LIKE ?
    OR remarks LIKE ?
)
";

$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("ssssss", $like, $like, $like, $like, $like, $like);
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];

$total_pages = max(1, ceil($total_records / $limit));

/* MAIN PAGINATED DATA QUERY */
$sql = "
SELECT *
FROM complaint
WHERE resolved_time IS NOT NULL
$date_filter
AND (
    comp_no LIKE ?
    OR forwarded_by LIKE ?
    OR device LIKE ?
    OR serial_no LIKE ?
    OR complaint LIKE ?
    OR remarks LIKE ?
)
ORDER BY resolved_time DESC
LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $like, $like, $like, $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resolved Complaints</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root{
    --navy:#0d2c54;
    --navy-dark:#071a35;
    --gold:#b8962e;
    --white:#ffffff;
    --gray:#6b7280;
    --border:#e5e7eb;
    --bg:#f5f7fb;
    --green:#0f5132;
    --green-bg:#d1e7dd;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:var(--bg);
    font-family:'DM Sans',sans-serif;
}

/* PAGE */
.page-body{
    max-width:1550px;
    margin:auto;
    padding:30px;
}

/* TOP BAR */
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:22px;
    flex-wrap:wrap;
    gap:14px;
}

.top-bar h2{
    color:var(--navy);
    font-size:30px;
    font-family:'EB Garamond',serif;
}

/* USER AREA */
.user-area{
    display:flex;
    align-items:center;
    gap:14px;
}

.user-area span{
    font-size:13px;
    color:#374151;
}

.logout-btn{
    background:#dc2626;
    color:white;
    text-decoration:none;
    padding:10px 18px;
    border-radius:8px;
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
    background:var(--navy);
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
    background:var(--navy);
}

.data-table th{
    color:white;
    padding:14px;
    text-align:left;
    font-size:12px;
    text-transform:uppercase;
    white-space:nowrap;
}

.data-table td{
    padding:13px 14px;
    border-bottom:1px solid var(--border);
    font-size:13px;
    white-space:nowrap;
}

.data-table tbody tr:hover{
    background:#f9fbff;
}

/* BADGE */
.badge{
    display:inline-block;
    padding:5px 11px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
}

.badge-resolved{
    background:var(--green-bg);
    color:var(--green);
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
    color: var(--navy);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.15s ease;
}

.pagination a:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination a.active {
    background: var(--navy);
    color: white;
    border-color: var(--navy);
}

.pagination span {
    padding: 6px 4px;
    color: #6b7280;
    font-size: 12px;
}

/* BACK LINK */
.back-link{
    display:inline-block;
    margin-top:18px;
    color:var(--navy);
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

@media print {
    body{
        background:#fff !important;
        margin:0;
        padding:0;
    }

    /* Hide interface layouts */
    .top-bar,
    .filter-bar,
    .back-link,
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
        white-space: normal !important; /* Allow wraps during page printing */
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

<div class="page-body">

    <div class="top-bar">
        <h2>Resolved Complaints</h2>
        <div class="user-area">
            <span>
                Welcome, 
                <strong><?php echo htmlspecialchars($_SESSION['name_section'] ?? $_SESSION['full_name']); ?></strong>
            </span>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <form method="GET" class="filter-bar">
        <input 
            type="text" 
            name="search" 
            placeholder="🔍 Search complaint no, device, serial no..." 
            value="<?php echo htmlspecialchars($search); ?>">
        
        <button type="submit">Search</button>

        <div class="filter-spacer"></div>

        <label class="date-label">From</label>
        <input 
            type="date" 
            name="from_date" 
            value="<?php echo htmlspecialchars($from_date); ?>">

        <label class="date-label">To</label>
        <input 
            type="date" 
            name="to_date" 
            value="<?php echo htmlspecialchars($to_date); ?>">

        <button type="button" onclick="window.open('print_complaints.php?from=' + document.querySelector('[name=from_date]').value + '&to=' + document.querySelector('[name=to_date]').value, '_blank');">
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
                    <th>Resolved Time</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        /* --- COLOR METRICS SETUP --- */
                        $ongoingColor = "#111827";
                        if(!empty($row['assigned_time']) && !empty($row['ongoing_time'])){
                            $assigned = strtotime($row['assigned_time']);
                            $ongoing = strtotime($row['ongoing_time']);
                            $daysDiff1 = floor(($ongoing - $assigned) / (60 * 60 * 24));
                            if($daysDiff1 > 7)  $ongoingColor = "#dc2626";
                            elseif($daysDiff1 > 3) $ongoingColor = "#ea580c";
                        }

                        $resolvedColor = "#111827";
                        if(!empty($row['ongoing_time']) && !empty($row['resolved_time'])){
                            $ongoing2 = strtotime($row['ongoing_time']);
                            $resolved2 = strtotime($row['resolved_time']);
                            $daysDiff2 = floor(($resolved2 - $ongoing2) / (60 * 60 * 24));
                            if($daysDiff2 > 7) $resolvedColor = "#dc2626";
                            elseif($daysDiff2 > 3) $resolvedColor = "#ea580c";
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['comp_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['forwarded_by']); ?></td>
                        <td><?php echo htmlspecialchars($row['device']); ?></td>
                        <td><?php echo htmlspecialchars($row['serial_no'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['complaint']); ?></td>
                        <td><?php echo !empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '—'; ?></td>
                        <td><?php echo htmlspecialchars($row['received_time']); ?></td>
                        <td><?php echo !empty($row['assigned_time']) ? htmlspecialchars($row['assigned_time']) : '—'; ?></td>
                        <td style="font-weight:600; color:<?php echo $ongoingColor; ?>;">
                            <?php echo !empty($row['ongoing_time']) ? htmlspecialchars($row['ongoing_time']) : '—'; ?>
                        </td>
                        <td style="font-weight:600; color:<?php echo $resolvedColor; ?>;">
                            <?php echo htmlspecialchars($row['resolved_time']); ?>
                        </td>
                        <td><?php echo !empty($row['assigned_to']) ? htmlspecialchars($row['assigned_to']) : '—'; ?></td>
                        <td><span class="badge badge-resolved">Resolved</span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center; color: var(--gray); padding: 20px;">No resolved complaints found matching criteria.</td>
                    </tr>
                <?php endif; ?>
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