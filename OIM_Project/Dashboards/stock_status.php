<?php
session_start();
require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "ITSC"){
    header("Location: ../Logins/login.php");
    exit();
}

/* ================= DELETE LOGIC ================= */
if(isset($_POST['delete_stock'])){
    $stock_id = $_POST['stock_id'];
    
    // Fetch row metadata before removal to adjust synchronized OB tracking balances
    $chkStmt = $conn->prepare("SELECT item_id, model_id, quantity, remarks FROM stock_rcpt WHERE stock_id = ?");
    $chkStmt->bind_param("i", $stock_id);
    $chkStmt->execute();
    $res = $chkStmt->get_result()->fetch_assoc();
    
    if($res){
        if($res['remarks'] === 'PURCHASE'){
            // Deduct the quantity from the matched inventory tracker row
            $subStmt = $conn->prepare("UPDATE stock_rcpt SET quantity = quantity - ? WHERE item_id = ? AND model_id = ? AND remarks = 'OB'");
            $subStmt->bind_param("iss", $res['quantity'], $res['item_id'], $res['model_id']);
            $subStmt->execute();
            
            // Clean up: purge any OB inventory entries that drop to 0 or negative
            $conn->query("DELETE FROM stock_rcpt WHERE remarks = 'OB' AND quantity <= 0");
        }
        
        $delStmt = $conn->prepare("DELETE FROM stock_rcpt WHERE stock_id = ?");
        $delStmt->bind_param("i", $stock_id);
        $delStmt->execute();
        
        echo "<script>alert('Record Deleted Successfully');window.location='stock_status.php';</script>";
        exit();
    }
}

/* ================= INSERT LOGIC ================= */
if(isset($_POST['insert'])){
    $item_id    = $_POST['item_id'];
    $remarks    = $_POST['remarks'];
    $model_id   = $_POST['model_id'];
    $quantity   = (int)$_POST['quantity'];

    // Enforce empty financial fields if the entry being initialized is manual OB
    if ($remarks === "OB") {
        $base_price = NULL;
        $amount     = NULL;
        $rate       = NULL;
        $invoice_no = NULL;
        $invoice_dt = NULL;
        $invoice_blob = NULL;
    } else {
        $base_price = !empty($_POST['base_price']) ? (float)$_POST['base_price'] : NULL;
        $amount     = ($base_price !== NULL) ? ($base_price * $quantity) : NULL;
        $rate       = ($amount !== NULL) ? ($amount * 1.18) : NULL;
        $invoice_no = $_POST['invoice_no'] ?: NULL;
        $invoice_dt = $_POST['invoice_dt'] ?: NULL;
        $invoice_blob = NULL;
    }

    if ($remarks === "PURCHASE") {
        if (
            empty($base_price) ||
            empty($invoice_no) ||
            empty($invoice_dt) ||
            empty($_FILES['invoice_file']['name'])
        ) {
            echo "<script>alert('All invoice fields mandatory for PURCHASE');window.history.back();</script>";
            exit();
        }

        /* FILE UPLOAD (AS BINARY BLOB) */
        if (isset($_FILES['invoice_file']) && $_FILES['invoice_file']['error'] == 0) {
            $invoice_blob = file_get_contents($_FILES['invoice_file']['tmp_name']);
        } else {
            die("File Upload Failed or Empty");
        }

        // Save Primary Purchase entry
        $stmt1 = $conn->prepare("
            INSERT INTO stock_rcpt (item_id, remarks, model_id, quantity, base_price, rate, amount, invoice_no, invoice_dt, invoice_dtl)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt1->bind_param("sssidddsss", $item_id, $remarks, $model_id, $quantity, $base_price, $rate, $amount, $invoice_no, $invoice_dt, $invoice_blob);
        
        if(!$stmt1->execute()){
            die("Purchase Insert Failed: ".$conn->error);
        }

        // --- AUTOMATED QUANTITY REPLICATION LEDGER ---
        $checkOB = $conn->prepare("SELECT stock_id FROM stock_rcpt WHERE item_id = ? AND model_id = ? AND remarks = 'OB'");
        $checkOB->bind_param("ss", $item_id, $model_id);
        $checkOB->execute();
        $obResult = $checkOB->get_result()->fetch_assoc();

        if($obResult){
            // Item row configuration found: accumulate quantity directly
            $updateOB = $conn->prepare("UPDATE stock_rcpt SET quantity = quantity + ? WHERE stock_id = ?");
            $updateOB->bind_param("ii", $quantity, $obResult['stock_id']);
            $updateOB->execute();
        } else {
            // New item match configuration: plant a clean OB ledger track row with empty values
            $insertOB = $conn->prepare("INSERT INTO stock_rcpt (item_id, remarks, model_id, quantity, base_price, rate, amount, invoice_no, invoice_dt, invoice_dtl) VALUES (?, 'OB', ?, ?, NULL, NULL, NULL, NULL, NULL, NULL)");
            $insertOB->bind_param("ssi", $item_id, $model_id, $quantity);
            $insertOB->execute();
        }

    } else {
        // Standard manual record storage routine (OB/CB manually added)
        $stmt = $conn->prepare("
            INSERT INTO stock_rcpt (item_id, remarks, model_id, quantity, base_price, rate, amount, invoice_no, invoice_dt, invoice_dtl)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("sssidddsss", $item_id, $remarks, $model_id, $quantity, $base_price, $rate, $amount, $invoice_no, $invoice_dt, $invoice_blob);

        if(!$stmt->execute()){
            die("Insert Failed: ".$conn->error);
        }
    }

    echo "<script>alert('Stock Saved Successfully');window.location='stock_status.php';</script>";
    exit();
}

/* ================= UPDATE LOGIC ================= */
if(isset($_POST['update'])){
    $stock_id   = $_POST['stock_id'];
    $item_id    = $_POST['item_id'];
    $remarks    = $_POST['remarks'];
    $model_id   = $_POST['model_id'];
    $quantity   = (int)$_POST['quantity'];

    // If edited row becomes an OB item, strip financial values programmatically
    if($remarks === "OB") {
        $stmt = $conn->prepare("
            UPDATE stock_rcpt
            SET item_id=?, remarks=?, model_id=?, quantity=?, base_price=NULL, rate=NULL, amount=NULL, invoice_no=NULL, invoice_dt=NULL
            WHERE stock_id=?
        ");
        $stmt->bind_param("sssii", $item_id, $remarks, $model_id, $quantity, $stock_id);
    } else {
        $base_price = !empty($_POST['base_price']) ? (float)$_POST['base_price'] : NULL;
        $amount     = ($base_price !== NULL) ? ($base_price * $quantity) : NULL;
        $rate       = ($amount !== NULL) ? ($amount * 1.18) : NULL;
        $invoice_no = !empty($_POST['invoice_no']) ? $_POST['invoice_no'] : NULL;
        $invoice_dt = !empty($_POST['invoice_dt']) ? $_POST['invoice_dt'] : NULL;

        $stmt = $conn->prepare("
            UPDATE stock_rcpt
            SET item_id=?, remarks=?, model_id=?, quantity=?, base_price=?, rate=?, amount=?, invoice_no=?, invoice_dt=?
            WHERE stock_id=?
        ");
        
        // Mapping is 3 strings, 1 int, 3 doubles, 2 strings, 1 int -> sssidddssi (11 parameters)
        $stmt->bind_param("sssidddssi", $item_id, $remarks, $model_id, $quantity, $base_price, $rate, $amount, $invoice_no, $invoice_dt, $stock_id);
    }

    if(!$stmt->execute()){
        die("Update Failed: " . $conn->error);
    }

    echo "<script>alert('Stock Updated Successfully');window.location='stock_status.php';</script>";
    exit();
}

/* FETCH DATA */
$result = $conn->query("SELECT * FROM stock_rcpt ORDER BY stock_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stock Register</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ font-family: Arial, sans-serif; min-height:100vh; background: linear-gradient(135deg, #edf2f7, #dfe7f0); padding:20px; }
.main-wrapper{ width:100%; max-width:1920px; margin:auto; }
.main-card{ background:#fff; border-radius:22px; overflow:hidden; border:1px solid #d8e0ea; box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
.gov-header{ background: linear-gradient(135deg, #ffffff, #f4f7fb); padding:18px 20px 14px; display:flex; align-items:center; justify-content:space-between; border-bottom:4px solid #c89b3c; }
.logo-box{ width:72px; display:flex; align-items:center; justify-content:center; }
.logo-box img{ width:60px; height:60px; object-fit:contain; }
.header-center{ flex:1; text-align:center; padding:0 12px; }
.header-center .hindi{ font-size:13px; color:#8a6b1f; font-weight:600; line-height:1.4; }
.header-center .eng{ font-size:18px; color:#1a3a6b; font-weight:700; margin-top:4px; }
.header-center .sub{ margin-top:5px; font-size:13px; color:#6d7784; }
.page-body{ padding:28px; }
.session-bar{ background:#f8fafc; border:1px solid #dbe5f0; border-radius:14px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.report-btn{ border:none; background: linear-gradient(135deg, #1a3a6b, #102544); color:#fff; height:42px; padding:0 18px; border-radius:12px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow: 0 6px 18px rgba(26,58,107,0.18); transition:0.2s ease; }
.report-btn:hover{ transform:translateY(-1px); box-shadow: 0 10px 22px rgba(26,58,107,0.22); }
.report-btn i{ font-size:16px; }
.session-user{ display:flex; align-items:center; gap:8px; color:#42566d; font-size:14px; }
.session-user strong{ color:#1a3a6b; }
.back-btn{ text-decoration:none; color:#1a3a6b; font-size:13px; font-weight:600; display:flex; align-items:center; gap:6px; }
.dashboard-card{ background:#fff; border:1px solid #dbe5f0; border-radius:18px; padding:24px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
.page-title{ display:flex; align-items:center; gap:10px; color:#1a3a6b; font-size:24px; font-weight:700; margin-bottom:24px; }
.table-wrapper{ overflow-x:auto; border-radius:16px; border:1px solid #d9e2ec; }
.data-table{ width:100%; border-collapse:collapse; min-width:1900px; background:#fff; }
.data-table thead th{ background: linear-gradient(135deg, #1a3a6b, #102544); color:#fff; padding:16px 14px; font-size:13px; font-weight:600; text-align:center; border-right:1px solid rgba(255,255,255,0.08); }
.data-table tbody td{ padding:14px; border-bottom:1px solid #edf2f7; text-align:center; font-size:13px; vertical-align:middle; }
.data-table tbody tr:hover{ background:#f8fbff; }
.data-table select, .data-table input[type="text"], .data-table input[type="number"], .data-table input[type="date"], .data-table input[type="file"]{ width:100%; min-width:100px; height:42px; border-radius:12px; border:1px solid #cfd8e3; background:#f8fafc; padding:0 12px; font-size:13px; transition:0.2s; }
.data-table input[type="file"]{ padding:8px; }
.data-table select:focus, .data-table input:focus{ outline:none; background:#fff; border-color:#1a3a6b; box-shadow: 0 0 0 3px rgba(26,58,107,0.08); }
.readonly-field{ background:#eef2f7 !important; color:#5b6777; }
.add-btn{ border:none; background: linear-gradient(135deg, #2e7d32, #1b5e20); color:#fff; height:42px; padding:0 18px; border-radius:12px; font-size:13px; font-weight:600; cursor:pointer; }
.edit-btn{ border:none; background: linear-gradient(135deg, #ef6c00, #d84315); color:#fff; height:40px; padding:0 16px; border-radius:12px; font-size:13px; font-weight:600; cursor:pointer; }
.save-btn{ border:none; background: linear-gradient(135deg, #1a3a6b, #102544); color:#fff; height:40px; padding:0 16px; border-radius:12px; font-size:13px; font-weight:600; cursor:pointer; }
.del-btn{ border:none; background: linear-gradient(135deg, #c62828, #b71c1c); color:#fff; height:40px; padding:0 16px; border-radius:12px; font-size:13px; font-weight:600; cursor:pointer; }
.file-link{ color:#1a3a6b; text-decoration:none; font-weight:600; }
.file-link:hover{ text-decoration:underline; }
.locked { color: #c62828; font-weight: 700; }
.open-status { color: #2e7d32; font-weight: 700; }
</style>
<script>
function enableEdit(id){
    let row = document.getElementById("row_"+id);
    let fields = row.querySelectorAll(".editable");
    fields.forEach(field=>{
        field.removeAttribute("readonly");
        field.removeAttribute("disabled");
        field.classList.remove("readonly-field");
    });
    document.getElementById("editBtn_"+id).style.display="none";
    document.getElementById("saveBtn_"+id).style.display="inline-block";
}
</script>
</head>
<body>

<div class="main-wrapper">
<div class="main-card">

<div class="gov-header">
    <div class="logo-box"><img src="/project/OIM_Project/assets/ashoka.png"></div>
    <div class="header-center">
        <div class="hindi">भारतीय लेखा तथा लेखा-परीक्षा विभाग</div>
        <div class="eng">Indian Audit And Accounts Department</div>
        <div class="sub">Office Item Management System</div>
    </div>
    <div class="logo-box"><img src="/project/OIM_Project/assets/ag_logo.png"></div>
</div>

<div class="page-body">
<div class="session-bar">
    <div class="session-user">
        <i class="ti ti-user-circle"></i> Logged in as: <strong><?php echo htmlspecialchars($_SESSION['userid'] ?? 'User'); ?></strong>
    </div>
    <a href="/project/OIM_Project/dashboards/itsc_dashboard.php" class="back-btn">
        <i class="ti ti-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="dashboard-card">
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div class="page-title" style="margin-bottom:0;"><i class="ti ti-database"></i> Stock Register</div>
    <div style="display:flex; gap:12px; align-items:center;">
        <button type="button" class="report-btn" onclick="window.location.href='/project/OIM_Project/Dashboards/stock_report.php'">
            <i class="ti ti-report-analytics"></i> Generate Stock Report
        </button>
        <button type="button" class="report-btn" onclick="window.location.href='/project/OIM_Project/Dashboards/model.php'">
            <i class="ti ti-box"></i> Models
        </button>
    </div>
</div>

<div class="table-wrapper">
<table class="data-table">
<thead>
<tr>
<th>ID</th>
<th>Item</th>
<th>Remarks</th>
<th>Model</th>
<th>Qty</th>
<th>Base Price</th>
<th>Amount (Base × Qty)</th>
<th>Rate (Amount + 18% GST)</th>
<th>Invoice No</th>
<th>Invoice Date</th>
<th>Invoice File</th>
<th>Status</th>
<th>Created</th>
<th>Action</th>
</tr>
</thead>
<tbody>

<tr>
<form method="POST" enctype="multipart/form-data">
<td>New</td>
<td>
    <select name="item_id" required>
        <option value="">Select</option>
        <?php
        $items = $conn->query("SELECT item_id,item_name FROM items");
        while($it=$items->fetch_assoc()){
            echo "<option value='".htmlspecialchars($it['item_id'])."'>".htmlspecialchars($it['item_name'])."</option>";
        }
        ?>
    </select>
</td>
<td>
    <select name="remarks" required>
        <option value="">Select</option>
        <option value="OB">OB</option>
        <option value="PURCHASE">PURCHASE</option>
        <option value="CB">CB</option>
    </select>
</td>
<td>
    <select name="model_id" required>
        <option value="">Select</option>
        <?php
        $models = $conn->query("SELECT model_id,model_name FROM models");
        while($md=$models->fetch_assoc()){
            echo "<option value='".htmlspecialchars($md['model_id'])."'>".htmlspecialchars($md['model_id'])."</option>";
        }
        ?>
    </select>
</td>
<td><input type="number" name="quantity" required></td>
<td><input type="text" name="base_price"></td>
<td><input type="text" placeholder="Auto Calc" class="readonly-field" readonly></td>
<td><input type="text" placeholder="Auto Calc" class="readonly-field" readonly></td>
<td><input type="text" name="invoice_no"></td>
<td><input type="date" name="invoice_dt"></td>
<td><input type="file" name="invoice_file"></td>
<td>-</td>
<td>Auto</td>
<td><button type="submit" name="insert" class="add-btn">Add</button></td>
</form>
</tr>

<?php while($row = $result->fetch_assoc()){ 
    $isPurchase = ($row['remarks'] == "PURCHASE");
    $isOB       = ($row['remarks'] == "OB");
?>
<tr id="row_<?php echo $row['stock_id']; ?>">
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="stock_id" value="<?php echo $row['stock_id']; ?>">
<td><?php echo $row['stock_id']; ?></td>

<td>
    <select name="item_id" disabled class="editable readonly-field">
        <?php
        $itemsEdit = $conn->query("SELECT item_id,item_name FROM items");
        while($it2=$itemsEdit->fetch_assoc()){
            $sel = ($it2['item_id']==$row['item_id']) ? "selected" : "";
            echo "<option value='".htmlspecialchars($it2['item_id'])."' $sel>".htmlspecialchars($it2['item_name'])."</option>";
        }
        ?>
    </select>
</td>

<td>
    <select name="remarks" disabled class="editable readonly-field">
        <option value="OB" <?php if($row['remarks']=="OB") echo "selected"; ?>>OB</option>
        <option value="PURCHASE" <?php if($row['remarks']=="PURCHASE") echo "selected"; ?>>PURCHASE</option>
        <option value="CB" <?php if($row['remarks']=="CB") echo "selected"; ?>>CB</option>
    </select>
</td>

<td>
    <select name="model_id" disabled class="editable readonly-field">
        <?php
        $modelsEdit = $conn->query("SELECT model_id,model_name FROM models");
        while($md2=$modelsEdit->fetch_assoc()){
            $sel = ($md2['model_id']==$row['model_id']) ? "selected" : "";
            echo "<option value='".htmlspecialchars($md2['model_id'])."' $sel>".htmlspecialchars($md2['model_id'])."</option>";
        }
        ?>
    </select>
</td>

<td><input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" readonly class="editable readonly-field"></td>
<td><input type="text" name="base_price" value="<?php echo $isOB ? '' : htmlspecialchars($row['base_price']); ?>" readonly class="<?php echo $isOB ? 'readonly-field' : 'editable readonly-field'; ?>"></td>
<td><input type="text" value="<?php echo $isOB ? '' : htmlspecialchars($row['amount']); ?>" readonly class="readonly-field"></td>
<td><input type="text" value="<?php echo $isOB ? '' : htmlspecialchars($row['rate']); ?>" readonly class="readonly-field"></td>
<td><input type="text" name="invoice_no" value="<?php echo $isOB ? '' : htmlspecialchars($row['invoice_no']); ?>" readonly class="<?php echo $isOB ? 'readonly-field' : 'editable readonly-field'; ?>"></td>
<td><input type="date" name="invoice_dt" value="<?php echo $isOB ? '' : htmlspecialchars($row['invoice_dt']); ?>" readonly class="<?php echo $isOB ? 'readonly-field' : 'editable readonly-field'; ?>"></td>

<td>
    <?php if(!$isOB && !empty($row['invoice_dtl'])){ ?>
        <a class="file-link" href="view_invoice.php?id=<?php echo htmlspecialchars($row['stock_id']); ?>" target="_blank">View</a><br><br>
    <?php } ?>
    <input type="file" name="invoice_file" disabled class="<?php echo $isOB ? 'readonly-field' : 'editable readonly-field'; ?>">
</td>

<td>
    <?php echo $isPurchase ? "<span class='locked'>LOCKED</span>" : "<span class='open-status'>OPEN</span>"; ?>
</td>
<td><?php echo htmlspecialchars($row['created_at']); ?></td>

<td>
    <?php if($isPurchase){ ?>
        <button type="submit" name="delete_stock" class="del-btn" onclick="return confirm('Are you sure you want to delete this PURCHASE record? It will automatically deduct from the corresponding OB inventory balance row.');">
            Delete
        </button>
    <?php } else { ?>
        <button type="button" class="edit-btn" id="editBtn_<?php echo $row['stock_id']; ?>" onclick="enableEdit(<?php echo $row['stock_id']; ?>)">Edit</button>
        <button type="submit" name="update" class="save-btn" id="saveBtn_<?php echo $row['stock_id']; ?>" style="display:none;">Save</button>
    <?php } ?>
</td>
</form>
</tr>
<?php } ?>

</tbody>
</table>
</div>
</div>
</div>
</div>
</div>

</body>
</html>