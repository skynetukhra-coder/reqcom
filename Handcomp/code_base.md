# Conclusive Project Codebase

## Directory Structure

```text
Handcomp/
    branch_officer.php
    code_base.md
    db_connect.php
    fetch_serial.php
    insert_complaint.php
    itsc.php
    login.php
    login_authenticate.php
    logout.php
    README.md
    section_officer.php
    addupdate/
        add_hardware.php
        add_update_hardware.php
        duplicate_device.php
        finalize_update.php
        generate_disposal.php
        move_to_store.php
        replace_hardware.php
        update_hardware.php
    assets/
        css/
            style.css
        js/
            script.js
    complaint/
        complaint_register.php
        resolved_complaints.php
        update_status.php
    config/
        db_connect.php
```

## File Contents

### `branch_officer.php`

```php
<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$officer_name = $_SESSION['full_name'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Branch Officer</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/script.js"></script>

<style>
.popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #16a34a;
    color: white;
    padding: 12px 18px;
    border-radius: 6px;
    display: none;
}
</style>

</head>
<body>

<div class="container">

<h2>Welcome, <?php echo $officer_name; ?></h2>

<!-- POPUP -->
<div id="popup" class="popup">Complaint Successfully Launched</div>

<form method="POST" action="insert_complaint.php">

<input type="hidden" name="forwarded_by" value="<?php echo $officer_name; ?>">

<label>Officer Name</label>
<input type="text" value="<?php echo $officer_name; ?>" readonly>

<label>Device</label>
<select id="device" name="device" onchange="loadComplaints()" required>
<option value="">Select Device</option>
<?php
$res = $conn->query("SELECT DISTINCT type FROM hw_inventory");
while ($row = $res->fetch_assoc()) {
    echo "<option value='{$row['type']}'>{$row['type']}</option>";
}
?>
</select>

<label>Serial Number</label>
<select id="serial" name="serial" required>
<option value="">Select Serial</option>
</select>

<label>Complaint</label>
<select id="complaint" name="complaint" onchange="toggleRemarks()" required>
<option value="">Select Complaint</option>
</select>

<label>Remarks</label>
<textarea id="remarks" disabled></textarea>

<input type="hidden" name="remarks_hidden" id="remarks_hidden">

<br><br>
<button type="submit">Forward to ITSC</button>

</form>

<br>
<a href="logout.php">Logout</a>

</div>

<script>

/* Fetch serial automatically */
window.onload = function() {
    fetch("fetch_serial.php?section=<?php echo $officer_name; ?>")
    .then(res => res.text())
    .then(data => {
        document.getElementById("serial").innerHTML = data;
    });
};

/* Submit remarks */
document.querySelector("form").addEventListener("submit", function() {
    document.getElementById("remarks_hidden").value =
        document.getElementById("remarks").value;
});

/* Popup */
<?php if(isset($_GET['success'])){ ?>
document.getElementById("popup").style.display = "block";

setTimeout(() => {
    document.getElementById("popup").style.display = "none";
}, 3000);
<?php } ?>

</script>

</body>
</html>
```

### `db_connect.php`

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "handcomp";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### `fetch_serial.php`

```php
<?php
require_once __DIR__ . "/config/db_connect.php";

$section = $_GET['section'] ?? '';
$device  = $_GET['device'] ?? '';

$stmt = $conn->prepare("SELECT hw_number FROM hw_inventory WHERE placed=? AND type=?");
$stmt->bind_param("ss", $section, $device);
$stmt->execute();

$result = $stmt->get_result();

echo "<option value=''>Select Serial</option>";

while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['hw_number']}'>{$row['hw_number']}</option>";
}
?>
```

### `insert_complaint.php`

```php
<?php
require_once __DIR__ . "/config/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $forwarded_by = $_POST['forwarded_by'];
    $device       = $_POST['device'];
    $serial       = $_POST['serial'];
    $complaint    = $_POST['complaint'];

    /* Handle remarks safely */
    $remarks = $_POST['remarks'] ?? $_POST['remarks_hidden'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO complaint 
        (forwarded_by, device, serial_no, complaint, remarks, status) 
        VALUES (?, ?, ?, ?, ?, 'Pending')
    ");

    $stmt->bind_param("sssss", 
        $forwarded_by, 
        $device, 
        $serial, 
        $complaint, 
        $remarks
    );

    if ($stmt->execute()) {

        /* Redirect based on source page */
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'branch_officer.php') !== false) {
            header("Location: branch_officer.php?success=1");
        } else {
            header("Location: section_officer.php?success=1");
        }

        exit();

    } else {
        echo "Error inserting complaint";
    }

} // ✅ THIS WAS MISSING OR BROKEN IN YOUR FILE
?>
```

### `itsc.php`

```php
<?php
session_start();

/* Restrict access */
if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ITSC Dashboard</title>
    <link rel="stylesheet" href="/Handcomp/assets/css/style.css">

    <style>
        /* Additional styling for dashboard layout */
        .dashboard {
            text-align: center;
        }

        .dashboard h2 {
            margin-bottom: 25px;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-group a {
            text-decoration: none;
        }

        .btn {
            display: block;
            padding: 14px;
            background-color: #374151;
            color: #fff;
            border-radius: 6px;
            transition: 0.3s ease;
            font-size: 15px;
        }

        .btn:hover {
            background-color: #111827;
        }

        .logout {
            margin-top: 25px;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container dashboard">

    <h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>

    <div class="btn-group">
    <a href="addupdate/add_update_hardware.php">
        <div class="btn">Add / Update Hardware</div>
    </a>

    <a href="complaint/complaint_register.php">
    <div class="btn">Complaint Register</div>
</a>

<a href="complaint/resolved_complaints.php">
    <div class="btn">Resolved Complaints</div>
</a>
</div>
    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

</body>
</html>
```

### `login.php`

```php
<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/Handcomp/assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <form action="login_authenticate.php" method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>

        <button type="submit">Login</button>
    </form>

    <a href="#">Forgot Password?</a>

    <?php
    if (isset($_GET['error'])) {
        echo "<p class='error'>Invalid Credentials</p>";
    }
    ?>
</div>

</body>
</html>
```

### `login_authenticate.php`

```php
<?php
session_start();

/* Correct path */
require_once __DIR__ . "/config/db_connect.php";

if (!$conn) {
    die("Database connection failed.");
}

/* Get form data safely */
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

/* Prepare SQL */
$sql = "SELECT * FROM users WHERE username=? AND password=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();

/* Check login */
if ($result->num_rows === 1) {

    $row = $result->fetch_assoc();

    $_SESSION['username']    = $row['username'];
    $_SESSION['full_name']   = $row['full_name'];
    $_SESSION['designation'] = $row['designation'];

    /* Role-based redirect */
    if ($row['designation'] == "ASSTT. ACCOUNTS OFFICER") {
        header("Location: section_officer.php");
    } elseif ($row['designation'] == "SR. ACCOUNTS OFFICER") {
        header("Location: branch_officer.php");
    } elseif ($row['designation'] == "ITSC") {
        header("Location: itsc.php");
    } else {
        header("Location: login.php?error=1");
    }

    exit();

} else {
    header("Location: login.php?error=1");
    exit();
}
?>
```

### `logout.php`

```php
<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
```

### `README.md`

```md
# Handcomp - Hardware Complaint Management System

Handcomp is a PHP-based web application designed for organizations to streamline the process of reporting, tracking, and resolving IT hardware complaints. The system features role-based access control, allowing different levels of staff to log complaints and the IT Support Cell (ITSC) to manage and resolve them.

## Features

* **Role-Based Access Control (RBAC):** Customized dashboards and permissions based on user designation.
* **Dynamic Inventory Fetching:** Automatically fetches available devices and serial numbers assigned to specific sections or officers using asynchronous JavaScript (AJAX).
* **Complaint Registration:** Simple and intuitive forms for officers to log hardware issues.
* **ITSC Dashboard:** Centralized view for IT staff to track hardware inventory, view incoming complaints, and manage resolved issues.

## User Roles

The system supports three primary user roles:
1. **ASSTT. ACCOUNTS OFFICER (Section Officer):** Can select their section, device, and serial number to forward complaints to the ITSC.
2. **SR. ACCOUNTS OFFICER (Branch Officer):** Automatically locked to their own assigned hardware inventory to quickly report issues.
3. **ITSC (IT Support Cell):** Responsible for receiving complaints, adding/updating hardware inventory, and marking complaints as resolved.

## Technology Stack

* **Backend:** PHP (Vanilla)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript (Fetch API)
* **Server:** Apache (via XAMPP)

## Project Structure

```text
Handcomp/
├── addupdate/
│   ├── add_update_hardware.php
│   └── finalize_update.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── complaint/
│   ├── complaint_register.php
│   └── resolved_complaints.php
├── config/
│   └── db_connect.php
├── branch_officer.php
├── fetch_serial.php
├── insert_complaint.php
├── itsc.php
├── login.php
├── login_authenticate.php
├── logout.php
├── README.md
└── section_officer.php
```

### Key Files & Directories
* `login.php` / `login_authenticate.php` / `logout.php`: Handles user sessions and authentication.
* `section_officer.php`: Dashboard for Assistant Accounts Officers.
* `branch_officer.php`: Dashboard for Senior Accounts Officers.
* `itsc.php`: Dashboard for the IT Support Cell.
* `insert_complaint.php`: Backend script to process and save new complaints into the database.
* `fetch_serial.php`: API endpoint to fetch hardware serial numbers dynamically based on the selected section and device type.
* `config/db_connect.php`: Database connection configuration.
* `addupdate/finalize_update.php`: Backend script for processing hardware record updates (such as moving items to the store).

## Database Schema (Required Tables)

To run this application, you will need a MySQL database named `handcomp` with the following tables:
* `users`: Stores `username`, `password`, `full_name`, and `designation`.
* `hw_inventory`: Stores `placed` (section/officer), `type` (device type), and `hw_number` (serial number).
* `complaint`: Stores `forwarded_by`, `device`, `serial_no`, `complaint` (issue type), `remarks`, and `status`.

## Installation & Setup

1. Install XAMPP or any standard LAMP/WAMP stack.
2. Clone or extract the project folder (`Handcomp`) into your web server's root directory (e.g., `c:\xampp\htdocs\Handcomp`).
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Create a MySQL database named `handcomp`.
5. Import your database schema and initial data.
6. Verify the database credentials in `config/db_connect.php` (default: root with no password).
7. Navigate to `http://localhost/Handcomp/login.php` in your web browser.
```

### `section_officer.php`

```php
<?php
session_start();
require_once __DIR__ . "/config/db_connect.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Section Officer</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/script.js"></script>

<style>
.popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #16a34a;
    color: white;
    padding: 12px 18px;
    border-radius: 6px;
    display: none;
}
</style>

</head>
<body>

<div class="container">

<h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>

<!-- SUCCESS POPUP -->
<div id="popup" class="popup">Complaint Successfully Launched</div>

<form method="POST" action="insert_complaint.php">

<input type="hidden" name="forwarded_by" value="<?php echo $_SESSION['full_name']; ?>">

<label>Section</label>
<select id="section" name="section" onchange="loadSerials()" required>
<option value="">Select Section</option>
<?php
$res = $conn->query("SELECT DISTINCT placed FROM hw_inventory");
while ($row = $res->fetch_assoc()) {
    echo "<option value='{$row['placed']}'>{$row['placed']}</option>";
}
?>
</select>

<label>Device</label>
<select id="device" name="device" onchange="loadSerials(); loadComplaints();" required>
<option value="">Select Device</option>
<?php
$res = $conn->query("SELECT DISTINCT type FROM hw_inventory");
while ($row = $res->fetch_assoc()) {
    echo "<option value='{$row['type']}'>{$row['type']}</option>";
}
?>
</select>

<label>Serial Number</label>
<select id="serial" name="serial" required>
<option value="">Select Serial</option>
</select>

<label>Complaint</label>
<select id="complaint" name="complaint" onchange="toggleRemarks()" required>
<option value="">Select Complaint</option>
</select>

<label>Remarks</label>
<textarea id="remarks" name="remarks" disabled></textarea>

<input type="hidden" name="remarks_hidden" id="remarks_hidden">

<br><br>
<button type="submit">Forward to ITSC</button>

</form>

<br>
<a href="logout.php">Logout</a>

</div>

<script>

/* Submit remarks */
document.querySelector("form").addEventListener("submit", function() {
    document.getElementById("remarks_hidden").value =
        document.getElementById("remarks").value;
});

/* Show popup if success */
<?php if(isset($_GET['success'])){ ?>
document.getElementById("popup").style.display = "block";

setTimeout(() => {
    document.getElementById("popup").style.display = "none";
}, 3000);
<?php } ?>

</script>

</body>
</html>
```

### `addupdate\add_hardware.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

unset($_POST['id']);

$cols = array_keys($_POST);
$vals = array_values($_POST);

$sql = "INSERT INTO hw_inventory (" . implode(",", $cols) . ")
        VALUES (" . str_repeat("?,", count($cols)-1) . "?)";

$stmt = $conn->prepare($sql);

$types = str_repeat("s", count($vals));
$stmt->bind_param($types, ...$vals);

$stmt->execute();

header("Location: add_update_hardware.php");
exit();
```

### `addupdate\add_update_hardware.php`

```php
<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: ../login.php");
    exit();
}

/* PAGINATION */
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* FILTERS */
$search = $_GET['search'] ?? "";
$type_filter = $_GET['type'] ?? "";

/* WHERE BUILD */
$where_arr = [];

if($search != ""){
    $where_arr[] = "(hw_number LIKE '%$search%' OR placed LIKE '%$search%')";
}

if($type_filter != ""){
    $where_arr[] = "type = '$type_filter'";
}

$where = count($where_arr) ? "WHERE " . implode(" AND ", $where_arr) : "";

/* TOTAL */
$total_res = $conn->query("SELECT COUNT(*) as total FROM hw_inventory $where");
$total_records = $total_res->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

/* DATA */
$query = "SELECT * FROM hw_inventory $where LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

/* COLUMNS */
$columns = [];
$col_res = $conn->query("DESCRIBE hw_inventory");
while($col = $col_res->fetch_assoc()){
    $columns[] = $col['Field'];
}

/* DROPDOWN FIELDS */
$dropdown_fields = [
    "type","sub_type","category","make",
    "issued_to","purpose","sec_store",
    "amc","under_warranty","placed"
];
?>

<!DOCTYPE html>
<html>
<head>
<title>Add / Update Hardware</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.container { width:95%; max-width:1400px; }

.top-bar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
}

.filters {
    display:flex;
    gap:10px;
}

.table-wrapper {
    overflow-x:auto;
    border:1px solid #ddd;
    border-radius:8px;
}

table {
    border-collapse:collapse;
    width:100%;
    font-size:13px;
}

th {
    position:sticky;
    top:0;
    background:#374151;
    color:white;
    padding:8px;
    white-space:nowrap;
}

td { padding:6px; }

td input {
    width:140px;
    font-size:12px;
    padding:4px;
}

button {
    padding:6px 10px;
    font-size:12px;
}

/* PAGINATION */
.pagination {
    display:flex;
    justify-content:center;
    flex-wrap:nowrap;
    gap:5px;
    overflow-x:auto;
    margin-top:15px;
}

.pagination a {
    padding:6px 10px;
    border:1px solid #ccc;
    text-decoration:none;
    color:black;
    white-space:nowrap;
}

.pagination a.active {
    background:#374151;
    color:white;
}
</style>

<script>
function enableEdit(id){

    let row = document.getElementById("row_"+id);
    let inputs = row.querySelectorAll("input");

    inputs.forEach(i => {
        i.removeAttribute("readonly");

        i.addEventListener("input", function(){
            let hidden = document.querySelector(`#form_${id} input[name='${i.className}']`);
            if(hidden) hidden.value = i.value;
        });
    });

    document.getElementById("update_"+id).style.display = "inline-block";
}

/* SHOW ALL PAGES */
function showAllPages(e){
    e.preventDefault();
    document.getElementById("allPages").style.display = "block";
}
</script>

</head>
<body>

<div class="container">

<h2>Add / Update Hardware</h2>

<div class="top-bar">

<form method="GET" class="filters">

<input type="text" name="search" placeholder="Search..." value="<?php echo $search; ?>">

<select name="type">
<option value="">All Devices</option>

<?php
$res = $conn->query("SELECT DISTINCT type FROM hw_inventory");
while($row = $res->fetch_assoc()){
    $val = $row['type'];
    $selected = ($type_filter == $val) ? "selected" : "";
    echo "<option value='$val' $selected>$val</option>";
}
?>

</select>

<button type="submit">Apply</button>

</form>

<a href="generate_disposal.php">
<button type="button">Generate Disposal List</button>
</a>

</div>

<div class="table-wrapper">
<table>

<tr>
<?php foreach($columns as $col){ ?>
<th><?php echo $col; ?></th>
<?php } ?>
<th>Action</th>
</tr>

<!-- ADD -->
<form action="add_hardware.php" method="POST">
<tr>
<?php foreach($columns as $col){ ?>
<td>
<?php if($col == "id"){ ?>
<input type="hidden" name="id">
<?php } elseif(in_array($col, $dropdown_fields)){ ?>
<input list="<?php echo $col; ?>_list" name="<?php echo $col; ?>" required>
<datalist id="<?php echo $col; ?>_list">
<?php
$res = $conn->query("SELECT DISTINCT $col FROM hw_inventory WHERE $col!=''");
while($r = $res->fetch_assoc()){
    echo "<option value='{$r[$col]}'>";
}
?>
</datalist>
<?php } else { ?>
<input type="text" name="<?php echo $col; ?>" required>
<?php } ?>
</td>
<?php } ?>
<td><button type="submit">Add</button></td>
</tr>
</form>

<!-- DATA -->
<?php while($row = $result->fetch_assoc()){ ?>
<tr id="row_<?php echo $row['id']; ?>">

<?php foreach($columns as $col){ ?>
<td>
<input class="<?php echo $col; ?>" value="<?php echo $row[$col]; ?>" readonly>
</td>
<?php } ?>

<td>
<form method="POST" action="update_hardware.php" id="form_<?php echo $row['id']; ?>">

<?php foreach($columns as $col){ ?>
<input type="hidden" name="<?php echo $col; ?>" value="<?php echo $row[$col]; ?>">
<?php } ?>

<button type="button" onclick="enableEdit(<?php echo $row['id']; ?>)">Edit</button>

<button type="submit" id="update_<?php echo $row['id']; ?>" style="display:none;">
Update
</button>

</form>
</td>

</tr>
<?php } ?>

</table>
</div>

<!-- ADVANCED PAGINATION -->
<div class="pagination">

<?php
$visible = 7;
$start = max(1, $page - 3);
$end = min($total_pages, $start + $visible - 1);

if ($start > 1) {
    echo "<a href='?page=1&search=$search&type=$type_filter'>1</a>";
    if ($start > 2) echo "<a href='#' onclick='showAllPages(event)'>...</a>";
}

for ($i = $start; $i <= $end; $i++) {
    $active = ($i == $page) ? "active" : "";
    echo "<a class='$active' href='?page=$i&search=$search&type=$type_filter'>$i</a>";
}

if ($end < $total_pages) {
    if ($end < $total_pages - 1) echo "<a href='#' onclick='showAllPages(event)'>...</a>";
    echo "<a href='?page=$total_pages&search=$search&type=$type_filter'>$total_pages</a>";
}
?>

</div>

<!-- FULL PAGE LIST -->
<div id="allPages" style="display:none; text-align:center; margin-top:10px;">
<?php
for ($i = 1; $i <= $total_pages; $i++) {
    echo "<a href='?page=$i&search=$search&type=$type_filter'>$i</a> ";
}
?>
</div>

<!-- BACK -->
<div style="margin-top:15px;">
<a href="../itsc.php">⬅ Back to Dashboard</a>
</div>

</div>

</body>
</html>
```

### `addupdate\duplicate_device.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

$existing_id = $_GET['existing_id'];
$current_id  = $_GET['current_id'];
$hw          = $_GET['hw'];

/* FETCH EXISTING DEVICE */
$stmt = $conn->prepare("SELECT * FROM hw_inventory WHERE id=?");
$stmt->bind_param("i", $existing_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Duplicate Device Detected</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.container {
    width: 60%;
    margin: auto;
    text-align: center;
}

.box {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    margin-top: 30px;
}

.btn {
    padding: 10px 15px;
    margin: 10px;
    border: none;
    cursor: pointer;
}

.store { background: #f59e0b; color: white; }
.swap  { background: #16a34a; color: white; }

</style>
</head>

<body>

<div class="container">

<h2>Device Already Allocated</h2>

<div class="box">

<p>
<strong>Device (HW Number):</strong> <?php echo $hw; ?>
</p>

<p>
This device is already placed in:
<br><br>
<strong><?php echo $existing['placed']; ?></strong>
</p>

<!-- BUTTONS -->

<form method="POST" action="move_to_store.php" style="display:inline;">
    <input type="hidden" name="existing_id" value="<?php echo $existing_id; ?>">
    <input type="hidden" name="current_id" value="<?php echo $current_id; ?>">
    <input type="hidden" name="hw" value="<?php echo $hw; ?>">
    <button class="btn store">Move to Store</button>
</form>

<form method="POST" action="replace_hardware.php" style="display:inline;">
    <input type="hidden" name="existing_id" value="<?php echo $existing_id; ?>">
    <input type="hidden" name="current_id" value="<?php echo $current_id; ?>">
    <button class="btn swap">Move to Different Section</button>
</form>

</div>

<br>
<a href="add_update_hardware.php">⬅ Back</a>

</div>

</body>
</html>
```

### `addupdate\finalize_update.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

$id = $_GET['id'];

/* UPDATE CURRENT RECORD (AFTER STORE MOVE) */
/* You can expand this if needed */

header("Location: add_update_hardware.php");
exit();
```

### `addupdate\generate_disposal.php`

```php
<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: ../login.php");
    exit();
}

$device = $_GET['device'] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate Disposal List</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.container {
    width: 95%;
    max-width: 1400px;
}

.table-wrapper {
    overflow-x: auto;
    border: 1px solid #ddd;
    margin-top: 20px;
}

table {
    border-collapse: collapse;
    width: 100%;
    font-size: 13px;
}

th {
    background: #374151;
    color: white;
    padding: 8px;
    white-space: nowrap;
}

td {
    padding: 6px;
    white-space: nowrap;
}
</style>

</head>
<body>

<div class="container">

<h2>Generate Disposal List</h2>

<!-- DEVICE SELECT -->
<form method="GET">

<label>Select Device</label>
<select name="device" required>
<option value="">Select Device</option>

<?php
$res = $conn->query("SELECT DISTINCT type FROM hw_inventory");
while($row = $res->fetch_assoc()){
    $selected = ($device == $row['type']) ? "selected" : "";
    echo "<option value='{$row['type']}' $selected>{$row['type']}</option>";
}
?>

</select>

<br><br>
<button type="submit">Generate</button>

</form>

<!-- RESULT -->
<?php if($device != ""){ ?>

<?php
$query = "
SELECT *
FROM hw_inventory
WHERE type = ?
AND date_of_purchase <= DATE_SUB(CURDATE(), INTERVAL 6 YEAR)
ORDER BY date_of_purchase ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $device);
$stmt->execute();
$result = $stmt->get_result();

/* Get full structure */
$fields = $result->fetch_fields();
?>

<div class="table-wrapper">
<table border="1">

<tr>
<?php foreach($fields as $field){ ?>
<th><?php echo $field->name; ?></th>
<?php } ?>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>
<tr>
<?php foreach($fields as $field){ ?>
<td><?php echo $row[$field->name]; ?></td>
<?php } ?>
</tr>
<?php } ?>

</table>
</div>

<?php } ?>

<br>
<a href="add_update_hardware.php">⬅ Back</a>

</div>

</body>
</html>
```

### `addupdate\move_to_store.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

$existing_id = $_POST['existing_id'];
$current_id  = $_POST['current_id'];

/* MOVE OLD DEVICE TO STORE */
$stmt = $conn->prepare("UPDATE hw_inventory SET placed='STORE ROOM' WHERE id=?");
$stmt->bind_param("i", $existing_id);
$stmt->execute();

/* NOW UPDATE CURRENT EDIT */
header("Location: finalize_update.php?id=".$current_id);
exit();
```

### `addupdate\replace_hardware.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

$existing_id = $_POST['existing_id'];
$current_id  = $_POST['current_id'];

/* FETCH BOTH */
$e = $conn->query("SELECT * FROM hw_inventory WHERE id=$existing_id")->fetch_assoc();
$c = $conn->query("SELECT * FROM hw_inventory WHERE id=$current_id")->fetch_assoc();

/* SWAP PLACED */
$conn->query("UPDATE hw_inventory SET placed='{$c['placed']}' WHERE id=$existing_id");
$conn->query("UPDATE hw_inventory SET placed='{$e['placed']}' WHERE id=$current_id");

header("Location: add_update_hardware.php");
exit();
```

### `addupdate\update_hardware.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

$id = $_POST['id'];
unset($_POST['id']);

/* DUPLICATE CHECK */
if(isset($_POST['hw_number'])){
    $stmt = $conn->prepare("SELECT id, placed FROM hw_inventory WHERE hw_number=? AND id!=?");
    $stmt->bind_param("si", $_POST['hw_number'], $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        $row = $res->fetch_assoc();

        /* REDIRECT INSTEAD OF POPUP */
        header("Location: duplicate_device.php?existing_id=".$row['id']."&current_id=".$id."&hw=".$_POST['hw_number']);
        exit();
    }
}

/* UPDATE */
$fields = [];
$values = [];

foreach($_POST as $col => $val){
    $fields[] = "$col=?";
    $values[] = $val;
}

$values[] = $id;

$sql = "UPDATE hw_inventory SET ".implode(",", $fields)." WHERE id=?";
$stmt = $conn->prepare($sql);

$types = str_repeat("s", count($values)-1) . "i";
$stmt->bind_param($types, ...$values);

$stmt->execute();

echo json_encode(["status"=>"success"]);
```

### `assets\css\style.css`

```css
/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body */
body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f5f7;
    color: #2e2e2e;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Container (used for all pages) */
.container {
    background: #ffffff;
    padding: 30px 40px;
    width: 420px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

/* Headings */
h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
    color: #1f2937;
}

/* Labels */
label {
    display: block;
    margin-top: 12px;
    margin-bottom: 5px;
    font-size: 14px;
    color: #4b5563;
}

/* Inputs & Dropdowns */
input,
select,
textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    background-color: #fafafa;
    font-size: 14px;
    transition: 0.2s ease;
}

/* Focus effect */
input:focus,
select:focus,
textarea:focus {
    outline: none;
    border-color: #6b7280;
    background-color: #ffffff;
}

/* Textarea */
textarea {
    resize: none;
    height: 80px;
}

/* Button */
button {
    width: 100%;
    margin-top: 20px;
    padding: 12px;
    background-color: #374151;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s ease;
}

/* Button hover */
button:hover {
    background-color: #111827;
}

/* Links */
a {
    display: block;
    text-align: center;
    margin-top: 12px;
    font-size: 13px;
    color: #6b7280;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Error Message */
.error {
    margin-top: 10px;
    color: #b91c1c;
    text-align: center;
    font-size: 13px;
}

/* Header Text (Welcome Name) */
.header {
    text-align: center;
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: 500;
    color: #111827;
}

/* Subtle Divider */
hr {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 15px 0;
}
```

### `assets\js\script.js`

```js
function loadSerials() {
    let section = document.getElementById("section").value;
    let device = document.getElementById("device") ? document.getElementById("device").value : "";

    if (section === "") {
        document.getElementById("serial").innerHTML = "<option>Select Serial</option>";
        return;
    }

    let url = "fetch_serial.php?section=" + encodeURIComponent(section);

    if (device !== "") {
        url += "&device=" + encodeURIComponent(device);
    }

    fetch(url)
        .then(res => res.text())
        .then(data => {
            document.getElementById("serial").innerHTML = data;
        });
}


/* 🔥 FIXED COMPLAINT LOADER */
function loadComplaints() {

    let deviceRaw = document.getElementById("device").value;

    /* Normalize */
    let device = deviceRaw.trim().toLowerCase();

    let complaints = {
        "monitor": ["Display issue", "No power", "Flickering"],
        "ups": ["Battery issue", "Not charging"],
        "printer": ["Paper jam", "Ink issue", "Not printing"],
        "desktop": ["Slow", "Not starting"],
        "laptop": ["Battery issue", "Overheating"],
        "scanner": ["Scan error"],
        "projector": ["No display"],
        "server": ["Down", "Network issue"]
    };

    let dropdown = document.getElementById("complaint");

    /* Reset dropdown */
    dropdown.innerHTML = "<option value=''>Select Complaint</option>";

    if (complaints[device]) {
        complaints[device].forEach(c => {
            dropdown.innerHTML += `<option value="${c}">${c}</option>`;
        });
    }

    /* Always add fallback */
    dropdown.innerHTML += `<option value="other">If any, mention</option>`;
}


/* REMARKS TOGGLE */
function toggleRemarks() {
    let val = document.getElementById("complaint").value;
    let remarks = document.getElementById("remarks");

    if (val === "other") {
        remarks.disabled = false;
    } else {
        remarks.disabled = true;
        remarks.value = "";
    }
}
```

### `complaint\complaint_register.php`

```php
<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: ../login.php");
    exit();
}

$result = $conn->query("
    SELECT * FROM complaint 
    WHERE status != 'Resolved' 
    ORDER BY received_time DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Complaint Register</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>

/* Container */
.container {
    width: 95%;
    max-width: 1400px;
}

/* Top Bar */
.top-bar {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    margin-bottom: 10px;
}

/* Center Title */
.center-title {
    grid-column: 2;
    text-align: center;
    margin: 0;
}

/* Table Wrapper */
.table-wrapper {
    overflow-x: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
}

/* Table */
table {
    border-collapse: collapse;
    width: 100%;
    font-size: 13px;
}

/* Header */
th {
    position: sticky;
    top: 0;
    background: #374151;
    color: white;
    padding: 8px;
    white-space: nowrap;
}

/* Cells */
td {
    padding: 6px;
    white-space: nowrap;
}

/* Buttons */
.btn-ongoing {
    background: #f59e0b;
    color: white;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
}

.btn-resolved {
    background: #16a34a;
    color: white;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
}

.btn-ongoing:hover {
    background: #d97706;
}

.btn-resolved:hover {
    background: #15803d;
}

</style>

<script>
function updateStatus(id, action){
    fetch("update_status.php", {
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"id="+id+"&action="+action
    })
    .then(res => res.text())
    .then(() => location.reload());
}
</script>

</head>
<body>

<div class="container">

<!-- CENTERED HEADER -->
<div class="top-bar">
    <h2 class="center-title">Complaint Register</h2>
</div>

<!-- TABLE -->
<div class="table-wrapper">

<table border="1">

<tr>
<th>Comp No</th>
<th>Forwarded By</th>
<th>Device</th>
<th>Serial No</th>
<th>Complaint</th>
<th>Remarks</th>
<th>Received Time</th>
<th>Ongoing Time</th>
<th>Status</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['comp_no']; ?></td>
<td><?php echo $row['forwarded_by']; ?></td>
<td><?php echo $row['device']; ?></td>
<td><?php echo $row['serial_no']; ?></td>
<td><?php echo $row['complaint']; ?></td>
<td><?php echo $row['remarks']; ?></td>
<td><?php echo $row['received_time']; ?></td>
<td><?php echo $row['ongoing_time']; ?></td>

<td>

<?php if($row['status']=="Pending"){ ?>

<button class="btn-ongoing"
onclick="updateStatus(<?php echo $row['comp_no']; ?>,'ongoing')">
Ongoing
</button>

<?php } elseif($row['status']=="Ongoing"){ ?>

<button class="btn-resolved"
onclick="updateStatus(<?php echo $row['comp_no']; ?>,'resolved')">
Resolved
</button>

<?php } ?>

</td>

</tr>

<?php } ?>

</table>

</div>

<!-- BACK -->
<div style="margin-top:15px;">
<a href="../itsc.php">⬅ Back to Dashboard</a>
</div>

</div>

</body>
</html>
```

### `complaint\resolved_complaints.php`

```php
<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: ../login.php");
    exit();
}

$result = $conn->query("
    SELECT * FROM complaint 
    WHERE status = 'Resolved' 
    ORDER BY resolved_time DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Resolved Complaints</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>

/* Container */
.container {
    width: 95%;
    max-width: 1400px;
}

/* Top Bar */
.top-bar {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    margin-bottom: 10px;
}

/* Center Title */
.center-title {
    grid-column: 2;
    text-align: center;
    margin: 0;
}

/* Table Wrapper */
.table-wrapper {
    overflow-x: auto;
    border: 1px solid #ddd;
    border-radius: 8px;
}

/* Table */
table {
    border-collapse: collapse;
    width: 100%;
    font-size: 13px;
}

/* Header */
th {
    position: sticky;
    top: 0;
    background: #374151;
    color: white;
    padding: 8px;
    white-space: nowrap;
}

/* Cells */
td {
    padding: 6px;
    white-space: nowrap;
}

</style>

</head>
<body>

<div class="container">

<!-- CENTERED HEADER -->
<div class="top-bar">
    <h2 class="center-title">Resolved Complaints</h2>
</div>

<!-- TABLE -->
<div class="table-wrapper">

<table border="1">

<tr>
<th>Comp No</th>
<th>Forwarded By</th>
<th>Device</th>
<th>Serial No</th>
<th>Complaint</th>
<th>Remarks</th>
<th>Received Time</th>
<th>Ongoing Time</th> <!-- ✅ Added here -->
<th>Resolved Time</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['comp_no']; ?></td>
<td><?php echo $row['forwarded_by']; ?></td>
<td><?php echo $row['device']; ?></td>
<td><?php echo $row['serial_no']; ?></td>
<td><?php echo $row['complaint']; ?></td>
<td><?php echo $row['remarks']; ?></td>
<td><?php echo $row['received_time']; ?></td>
<td><?php echo $row['ongoing_time']; ?></td> <!-- ✅ Added -->
<td><?php echo $row['resolved_time']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<!-- BACK -->
<div style="margin-top:15px;">
<a href="../itsc.php">⬅ Back to Dashboard</a>
</div>

</div>

</body>
</html>
```

### `complaint\update_status.php`

```php
<?php
require_once __DIR__ . "/../config/db_connect.php";

$id = $_POST['id'];
$action = $_POST['action'];

if($action == "ongoing"){
    $conn->query("UPDATE complaint 
                  SET status='Ongoing', ongoing_time=NOW() 
                  WHERE comp_no=$id");
}

if($action == "resolved"){
    $conn->query("UPDATE complaint 
                  SET status='Resolved', resolved_time=NOW() 
                  WHERE comp_no=$id");
}

echo "done";
```

### `config\db_connect.php`

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hardcomp";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

