<?php
session_start();
require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "Section Officer") {
    header("Location: ../Logins/login.php");
    exit();
}

/* INSERT REQUISITION */
if(isset($_POST['forward'])){

    $section_id = $_POST['section_name'];
    $item_id    = $_POST['item_id'];
    $model_id   = $_POST['model_id'];
    $quantity   = $_POST['quantity'];
    $bo_user_id = $_POST['bo_user_id'];
    $username   = $_SESSION['userid'];

    /* GET user_id */
    $sql_user = "SELECT user_id FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();

    $res_user = $stmt_user->get_result();
    $user = $res_user->fetch_assoc();

    if(!$user){
        die("User not found");
    }

    $requested_by = $user['user_id'];

    /* CHECK BO */
    if(empty($bo_user_id)){
        die("Please select Branch Officer");
    }

    /* INSERT */
    $sql = "INSERT INTO requisitions
            (section_id, item_id, model_id, quantity, requested_by, assigned_bo, section_forward, approve_date)
            VALUES (?, ?, ?, ?, ?, ?, 'Y', CURDATE())";

    $stmt = $conn->prepare($sql);

    if(!$stmt){
        die("Insert Error: " . $conn->error);
    }

    $stmt->bind_param("ssssss",
        $section_id,
        $item_id,
        $model_id,
        $quantity,
        $requested_by,
        $bo_user_id
    );

    if($stmt->execute()){
        echo "<script>alert('Requisition submitted successfully');</script>";
    } else {
        die("Insert failed: " . $stmt->error);
    }
}

/* FETCH SECTIONS */
$sql_section = "SELECT section_id, section_name 
                FROM sections
                WHERE section_name NOT LIKE 'BRANCH OFFICER%'
                AND section_name NOT LIKE 'SECRETARY TO PR. AG.%'
                AND section_name NOT LIKE 'INTERNAL AUDIT OFFICER%'
                ORDER BY section_name";

$section_result = $conn->query($sql_section);

/* FETCH ITEMS */
$sql_item = "SELECT item_id, item_name FROM items ORDER BY item_name";
$item_result = $conn->query($sql_item);

/* FETCH BRANCH OFFICERS */
$sql_bo = "SELECT user_id, full_name 
           FROM users 
           WHERE designation = 'SR. ACCOUNTS OFFICER'
           ORDER BY full_name";

$bo_result = $conn->query($sql_bo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Section Officer Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script>
function loadModels() {
    var item_id = document.getElementById("item").value;
    var modelDropdown = document.getElementById("model");

    modelDropdown.innerHTML = "<option>Loading...</option>";

    fetch("../ajax/fetch_models.php?item_id=" + encodeURIComponent(item_id))
        .then(response => response.text())
        .then(data => {
            modelDropdown.innerHTML = data;
        });
}
</script>
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ font-family:Arial,sans-serif; background: linear-gradient(135deg, #edf2f7, #dfe7f0); padding:18px; }
.main-wrapper{ max-width:1300px; margin:auto; }
.letterhead{ background:#fff; border-radius:18px; overflow:hidden; border:1px solid #d7dfe8; box-shadow: 0 10px 30px rgba(0,0,0,0.06); }
.header-top{ background: linear-gradient(135deg, #f6f2e8, #ece7d8); border-bottom:4px solid #b8860b; padding:14px 18px; display:flex; align-items:center; justify-content:space-between; gap:15px; }
.logo{ width:65px; height:65px; object-fit:contain; }
.header-center{ flex:1; text-align:center; }
.header-center .hindi{ font-size:13px; font-weight:600; color:#8a6916; }
.header-center .eng{ margin-top:4px; font-size:20px; font-weight:700; color:#1a3a6b; }
.header-center .sub{ margin-top:5px; font-size:13px; color:#666; }
.content{ padding:18px; }
.session-bar{ background:#f7f1e3; border:1px solid #dccb98; border-radius:10px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
.session-user{ font-size:13px; color:#333; display:flex; align-items:center; gap:7px; }
.session-user strong{ color:#1a3a6b; }
.logout-btn{ text-decoration:none; color:#8b1a1a; font-size:13px; font-weight:600; display:flex; align-items:center; gap:5px; }
.dashboard-card{ background:#fff; border:1px solid #dbe2ea; border-radius:14px; padding:20px; }
.page-title{ font-size:20px; font-weight:700; color:#1a3a6b; margin-bottom:22px; display:flex; align-items:center; gap:10px; }
.form-grid{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.input-group{ margin-bottom:16px; }
.input-group label{ display:block; margin-bottom:8px; font-size:13px; font-weight:600; color:#42566d; }
.input-group input, .input-group select{ width:100%; height:48px; border-radius:12px; border:1px solid #cfd8e3; background:#f8fafc; padding:0 14px; font-size:14px; }
.input-group input:focus, .input-group select:focus{ outline:none; border-color:#1a3a6b; background:#fff; box-shadow: 0 0 0 3px rgba(26,58,107,0.08); }
.submit-btn{ border:none; background: linear-gradient(135deg, #1a3a6b, #102544); color:#fff; padding:13px 22px; border-radius:12px; font-size:14px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:8px; margin-top:8px; }
.submit-btn:hover{ opacity:0.95; }
.footer-note{ margin-top:18px; text-align:center; font-size:12px; color:#666; }
@media(max-width:768px){ .header-top{ flex-direction:column; } .form-grid{ grid-template-columns:1fr; } .session-bar{ flex-direction:column; gap:10px; align-items:flex-start; } }
</style>
</head>
<body>
<div class="main-wrapper">
<div class="letterhead">
    <div class="header-top">
        <img src="../assets/ashoka.png" class="logo">
        <div class="header-center">
            <div class="hindi">भारतीय लेखा तथा लेखा-परीक्षा विभाग</div>
            <div class="eng">Indian Audit And Accounts Department</div>
            <div class="sub">Office Item Management System</div>
        </div>
        <img src="../assets/ag_logo.png" class="logo">
    </div>

    <div class="content">
        <div class="session-bar">
            <div class="session-user">
                <i class="ti ti-user-circle"></i> Logged in as: <strong><?php echo htmlspecialchars($_SESSION['userid']); ?></strong>
            </div>
            <a href="../Logins/logout.php" class="logout-btn">
                <i class="ti ti-logout"></i> Logout
            </a>
        </div>

        <div class="dashboard-card">
            <div class="page-title">
                <i class="ti ti-file-text"></i> Section Officer Dashboard
            </div>

            <form method="POST">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Section Name</label>
                        <select name="section_name" required>
                            <option value="">-- Select Section --</option>
                            <?php while($sec = $section_result->fetch_assoc()){ ?>
                                <option value="<?php echo htmlspecialchars($sec['section_id']); ?>">
                                    <?php echo htmlspecialchars($sec['section_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Item</label>
                        <select name="item_id" id="item" onchange="loadModels()" required>
                            <option value="">-- Select Item --</option>
                            <?php while($item = $item_result->fetch_assoc()){ ?>
                                <option value="<?php echo htmlspecialchars($item['item_id']); ?>">
                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Model</label>
                        <select name="model_id" id="model" required>
                            <option value="">-- Select Model --</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Select Branch Officer</label>
                    <select name="bo_user_id" required>
                        <option value="">-- Select BO --</option>
                        <?php while($bo = $bo_result->fetch_assoc()){ ?>
                            <option value="<?php echo htmlspecialchars($bo['user_id']); ?>">
                                <?php echo htmlspecialchars($bo['full_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" name="forward" class="submit-btn">
                    <i class="ti ti-send"></i> Forward to BO
                </button>
            </form>
        </div>

        <div class="footer-note">Office Item Management System · Government Use Only</div>
    </div>
</div>
</div>
</body>
</html>