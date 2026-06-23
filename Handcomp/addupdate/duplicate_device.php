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