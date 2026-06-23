<?php

session_start();

require_once dirname(__DIR__) . "/db_connect.php";

/* AUTH CHECK */

if($_SESSION['role'] != "ITSC"){

    header("Location: ../Logins/login.php");

    exit();
}

/* ================= INSERT ================= */

if(isset($_POST['insert'])){

    $model_id   = trim($_POST['model_id']);
    $item_id    = trim($_POST['item_id']);
    $model_name = trim($_POST['model_name']);

    $stmt = $conn->prepare("
    INSERT INTO models
    (
        model_id,
        item_id,
        model_name
    )
    VALUES (?, ?, ?)
    ");

    $stmt->bind_param(
        "sss",
        $model_id,
        $item_id,
        $model_name
    );

    $stmt->execute();

    echo "
    <script>

    alert('Model Added Successfully');

    window.location='model.php';

    </script>
    ";

    exit();
}

/* ================= DELETE ================= */

if(isset($_POST['delete'])){

    $model_id = trim($_POST['delete_model_id']);

    try{

        $stmt = $conn->prepare("
        DELETE FROM models
        WHERE model_id = ?
        ");

        $stmt->bind_param(
            "s",
            $model_id
        );

        $stmt->execute();

        echo "
        <script>

        alert('Model Deleted Successfully');

        window.location='model.php';

        </script>
        ";

    }catch(mysqli_sql_exception $e){

        echo "
        <script>

        alert(
        'Cannot delete this model because it is already used in requisitions.'
        );

        window.location='model.php';

        </script>
        ";
    }

    exit();
}
/* ================= UPDATE ================= */

if(isset($_POST['update'])){

    $old_model_id = $_POST['old_model_id'];

    $model_id   = trim($_POST['model_id']);
    $item_id    = trim($_POST['item_id']);
    $model_name = trim($_POST['model_name']);

    $stmt = $conn->prepare("
    UPDATE models
    SET
        model_id=?,
        item_id=?,
        model_name=?
    WHERE model_id=?
    ");

    $stmt->bind_param(
        "ssss",
        $model_id,
        $item_id,
        $model_name,
        $old_model_id
    );

    $stmt->execute();

    echo "
    <script>

    alert('Model Updated Successfully');

    window.location='model.php';

    </script>
    ";

    exit();
}

/* ================= FETCH ================= */

$where = "";

if(
isset($_GET['search_model'])
&&
$_GET['search_model'] != ""
){

    $search_model = $_GET['search_model'];

    $where = "
    WHERE model_id = '$search_model'
    ";
}

$result = $conn->query("
SELECT *
FROM models
$where
ORDER BY model_id ASC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Models Master</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family: Arial, sans-serif;

    min-height:100vh;

    background:
    linear-gradient(
        135deg,
        #edf2f7,
        #dfe7f0
    );

    padding:20px;
}

.main-wrapper{

    width:100%;

    max-width:1300px;

    margin:auto;
}

.main-card{

    background:#fff;

    border-radius:22px;

    overflow:hidden;

    border:1px solid #d8e0ea;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.08);
}

.gov-header{

    background:
    linear-gradient(
        135deg,
        #ffffff,
        #f4f7fb
    );

    padding:18px 20px 14px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    border-bottom:4px solid #c89b3c;
}

.logo-box{

    width:72px;

    display:flex;

    align-items:center;

    justify-content:center;
}

.logo-box img{

    width:60px;

    height:60px;

    object-fit:contain;
}

.header-center{

    flex:1;

    text-align:center;

    padding:0 12px;
}

.header-center .hindi{

    font-size:13px;

    color:#8a6b1f;

    font-weight:600;
}

.header-center .eng{

    font-size:18px;

    color:#1a3a6b;

    font-weight:700;

    margin-top:4px;
}

.header-center .sub{

    margin-top:5px;

    font-size:13px;

    color:#6d7784;
}

.page-body{

    padding:28px;
}

.session-bar{

    background:#f8fafc;

    border:1px solid #dbe5f0;

    border-radius:14px;

    padding:14px 18px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:24px;
}

.session-user{

    display:flex;

    align-items:center;

    gap:8px;

    color:#42566d;

    font-size:14px;
}

.back-btn{

    text-decoration:none;

    color:#1a3a6b;

    font-size:13px;

    font-weight:600;
}

.dashboard-card{

    background:#fff;

    border:1px solid #dbe5f0;

    border-radius:18px;

    padding:24px;

    box-shadow:
    0 5px 20px rgba(0,0,0,0.03);
}

.page-title{

    display:flex;

    align-items:center;

    gap:10px;

    color:#1a3a6b;

    font-size:24px;

    font-weight:700;

    margin-bottom:24px;
}

.table-wrapper{

    overflow-x:auto;

    border-radius:16px;

    border:1px solid #d9e2ec;
}

.data-table{

    width:100%;

    border-collapse:collapse;

    background:#fff;
}

.data-table thead th{

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    padding:16px 14px;

    font-size:13px;

    text-align:center;
}

.data-table tbody td{

    padding:14px;

    border-bottom:1px solid #edf2f7;

    text-align:center;
}

.data-table input,
.data-table select{

    width:100%;

    min-width:180px;

    height:42px;

    border-radius:12px;

    border:1px solid #cfd8e3;

    background:#f8fafc;

    padding:0 12px;

    font-size:13px;

    transition:0.2s;
}

.data-table select{

    cursor:pointer;
}

.data-table input:focus,
.data-table select:focus{

    outline:none;

    background:#fff;

    border-color:#1a3a6b;

    box-shadow:
    0 0 0 3px rgba(26,58,107,0.08);
}

.readonly-field{

    background:#eef2f7 !important;
}

.add-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #2e7d32,
        #1b5e20
    );

    color:#fff;

    height:42px;

    padding:0 18px;

    border-radius:12px;

    font-size:13px;

    font-weight:600;

    cursor:pointer;
}

.edit-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #ef6c00,
        #d84315
    );

    color:#fff;

    height:40px;

    padding:0 16px;

    border-radius:12px;

    font-size:13px;

    font-weight:600;

    cursor:pointer;
}

.save-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    height:40px;

    padding:0 16px;

    border-radius:12px;

    font-size:13px;

    font-weight:600;

    cursor:pointer;
}

.delete-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #c62828,
        #8e0000
    );

    color:#fff;

    height:40px;

    padding:0 16px;

    border-radius:12px;

    font-size:13px;

    font-weight:600;

    cursor:pointer;
}

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

    document.getElementById("deleteBtn_"+id).style.display="inline-block";
}

</script>

</head>

<body>

<div class="main-wrapper">

<div class="main-card">

<div class="gov-header">

    <div class="logo-box">

        <img src="/project/OIM_Project/assets/ashoka.png">

    </div>

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

    <div class="logo-box">

        <img src="/project/OIM_Project/assets/ag_logo.png">

    </div>

</div>

<div class="page-body">

<div class="session-bar">

    <div class="session-user">

        <i class="ti ti-user-circle"></i>

        Logged in as:

        <strong>

            <?php echo $_SESSION['userid']; ?>

        </strong>

    </div>

    <a
    href="stock_status.php"
    class="back-btn">

        ← Back to Stock Register

    </a>

</div>

<div class="dashboard-card">

<div class="page-title">

    <i class="ti ti-box"></i>

    Models Master

</div>

<!-- SEARCH BAR -->

<div style="
display:flex;
justify-content:flex-end;
margin-bottom:20px;
">

<form method="GET" style="
display:flex;
gap:12px;
align-items:center;
">

<!-- SEARCH DROPDOWN -->

<select
name="search_model"
style="
width:280px;
height:42px;
border-radius:12px;
border:1px solid #cfd8e3;
background:#f8fafc;
padding:0 12px;
font-size:13px;
">

<option value="">

Search Model ID

</option>

<?php

$searchModels = $conn->query("
SELECT DISTINCT model_id
FROM models
ORDER BY model_id ASC
");

while($sm = $searchModels->fetch_assoc()){

?>

<option
value="<?php echo $sm['model_id']; ?>"
<?php
if(
isset($_GET['search_model'])
&&
$_GET['search_model']==$sm['model_id']
)
echo "selected";
?>>

<?php echo $sm['model_id']; ?>

</option>

<?php } ?>

</select>

<!-- SEARCH BUTTON -->

<button
type="submit"
class="save-btn">

<i class="ti ti-search"></i>

Search

</button>

<!-- RESET BUTTON -->

<button
type="button"
class="delete-btn"
onclick="window.location='model.php'">

Reset

</button>

</form>

</div>

<div class="table-wrapper">

<table class="data-table">

<thead>

<tr>

<th>Model ID</th>
<th>Item ID</th>
<th>Model Name</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<tr>

<form method="POST">

<td>

<input
type="text"
name="model_id"
list="model_id_list"
required>

<datalist id="model_id_list">

<?php

$modelQuery = $conn->query("
SELECT DISTINCT model_id
FROM models
ORDER BY model_id ASC
");

while($model = $modelQuery->fetch_assoc()){

?>

<option value="<?php echo $model['model_id']; ?>">

<?php } ?>

</datalist>

</td>

<td>

<select name="item_id" required>

<option value="">Select Item</option>

<?php

$itemQuery = $conn->query("
SELECT item_id,item_name
FROM items
ORDER BY item_name ASC
");

while($item = $itemQuery->fetch_assoc()){

?>

<option value="<?php echo $item['item_id']; ?>">

<?php echo $item['item_id']." - ".$item['item_name']; ?>

</option>

<?php } ?>

</select>

</td>

<td>

<input
type="text"
name="model_name"
list="model_name_list"
required>

<datalist id="model_name_list">

<?php

$modelNameQuery = $conn->query("
SELECT DISTINCT model_name
FROM models
ORDER BY model_name ASC
");

while($modelName = $modelNameQuery->fetch_assoc()){

?>

<option value="<?php echo $modelName['model_name']; ?>">

<?php } ?>

</datalist>

</td>

<td>

<button
type="submit"
name="insert"
class="add-btn">

Add

</button>

</td>

</form>

</tr>

<?php while($row = $result->fetch_assoc()){ ?>

<tr id="row_<?php echo $row['model_id']; ?>">

<form method="POST">

<input
type="hidden"
name="old_model_id"
value="<?php echo $row['model_id']; ?>">

<td>

<input
type="text"
name="model_id"
value="<?php echo $row['model_id']; ?>"
list="model_id_list"
readonly
class="editable readonly-field">

</td>

<td>

<select
name="item_id"
disabled
class="editable readonly-field">

<?php

$itemQuery2 = $conn->query("
SELECT item_id,item_name
FROM items
ORDER BY item_name ASC
");

while($item2 = $itemQuery2->fetch_assoc()){

?>

<option
value="<?php echo $item2['item_id']; ?>"
<?php if($item2['item_id']==$row['item_id']) echo "selected"; ?>>

<?php
echo $item2['item_id']." - ".$item2['item_name'];
?>

</option>

<?php } ?>

</select>

</td>

<td>

<input
type="text"
name="model_name"
value="<?php echo $row['model_name']; ?>"
list="model_name_list"
readonly
class="editable readonly-field">

</td>

<td>

<div style="
display:flex;
gap:8px;
justify-content:center;
">

<button
type="button"
class="edit-btn"
id="editBtn_<?php echo $row['model_id']; ?>"
onclick="enableEdit('<?php echo $row['model_id']; ?>')">

Edit

</button>

<button
type="submit"
name="update"
class="save-btn"
id="saveBtn_<?php echo $row['model_id']; ?>"
style="display:none;">

Save

</button>

<button
type="submit"
name="delete"
value="1"
class="delete-btn"
id="deleteBtn_<?php echo $row['model_id']; ?>"
style="display:none;"
onclick="return confirm(
'Are you sure you want to delete Model ID: <?php echo $row['model_id']; ?> ?'
);">

Delete

</button>

</div>

<input
type="hidden"
name="delete_model_id"
value="<?php echo $row['model_id']; ?>">

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