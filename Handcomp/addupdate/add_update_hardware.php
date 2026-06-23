<?php
/**
 * addupdate/add_update_hardware.php — Hardware Inventory Manager
 * Updated: Phase 1 UI Overhaul
 */

session_start();
require_once __DIR__ . "/../config/db_connect.php";

/* ── Access control ───────────────────────────────────────── */
if (!isset($_SESSION['username']) || $_SESSION['designation'] != "ITSC") {
    header("Location: ../login.php");
    exit();
}

/* ── Header include settings ──────────────────────────────── */
$active_nav     = 'hardware';
$logo_base_path = '../';

/* ── Pagination ───────────────────────────────────────────── */
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ── Filters (raw — used only in LIKE/= safely below) ─────── */
$search      = $_GET['search']  ?? '';
$type_filter = $_GET['type']    ?? '';

/* ── WHERE clause (parameterized below — here just for count) */
$where_arr = [];
$bind_types = '';
$bind_vals  = [];

if ($search !== '') {
    $like = '%' . $conn->real_escape_string($search) . '%';
    $where_arr[] = "(hw_number LIKE '$like' OR placed LIKE '$like')";
}
if ($type_filter !== '') {
    $safe_type = $conn->real_escape_string($type_filter);
    $where_arr[] = "type = '$safe_type'";
}

$where = count($where_arr) ? 'WHERE ' . implode(' AND ', $where_arr) : '';

/* Total records (for pagination) */
$total_res     = $conn->query("SELECT COUNT(*) AS total FROM hw_inventory $where");
$total_records = $total_res->fetch_assoc()['total'];
$total_pages   = max(1, ceil($total_records / $limit));

/* Data rows */
$result = $conn->query("SELECT * FROM hw_inventory $where LIMIT $limit OFFSET $offset");

/* Columns from schema */
$columns = [];
$col_res = $conn->query("DESCRIBE hw_inventory");
while ($col = $col_res->fetch_assoc()) {
    $columns[] = $col['Field'];
}

/* Fields that use datalist dropdowns */
$dropdown_fields = [
    "type", "sub_type", "category", "make",
    "issued_to", "purpose", "sec_store",
    "amc", "under_warranty", "placed"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardware Inventory — Handcomp</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Global stylesheet -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Table inputs */
        td input, td select {
            width: 120px;
            font-size: 12px;
            padding: 5px 7px;
            border-radius: 5px;
            border: 1px solid var(--gray-300);
            background: var(--gray-100);
        }

        /* Add row highlight */
        tr.add-row td { background: #f0f6ff; }
        tr.add-row td input,
        tr.add-row td select {
            border-color: #93c5fd;
            background: var(--white);
        }

        /* Edit mode active input */
        td input.editable {
            border-color: var(--gold) !important;
            background: var(--gold-pale) !important;
        }

        /* Filter bar */
        .filter-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            background: var(--white);
            padding: 14px 18px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 16px;
        }

        .filter-bar input,
        .filter-bar select {
            width: auto;
            padding: 8px 12px;
            font-size: 13px;
            border-radius: var(--radius);
        }

        .filter-bar .spacer { flex: 1; }

        /* Compact table buttons */
        .tbl-btn {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.15s;
            margin-top: 0;
            box-shadow: none;
            width: auto;
        }
        .tbl-btn-edit { background: #dbeafe; color: #1e40af; }
        .tbl-btn-edit:hover { background: #93c5fd; }
        .tbl-btn-save { background: #dcfce7; color: #166534; display: none; }
        .tbl-btn-save:hover { background: #86efac; }
        .tbl-btn-add { background: var(--navy); color: var(--white); }
        .tbl-btn-add:hover { background: var(--navy-dark); }
    </style>
</head>
<body>

<!-- ── Site Header ─────────────────────────────────────────── -->
<?php require_once __DIR__ . '/../includes/header_include.php'; ?>

<!-- ── Page Content ───────────────────────────────────────── -->
<main class="page-content">

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
animation:fadeIn .25s ease;
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
    <div class="page-title">
        <h2>Hardware Inventory</h2>
        <a href="generate_disposal.php" class="btn btn-gold btn-sm" style="margin-top:0;">
            &#128465; Generate Disposal List
        </a>
    </div>

    <!-- Filter bar -->
    <form method="GET" class="filter-bar">
        <input type="text"
               name="search"
               placeholder="&#128269;  Search serial, section…"
               value="<?php echo htmlspecialchars($search); ?>"
               style="min-width:200px;">
        <select name="type">
            <option value="">All Devices</option>
            <?php
            $res = $conn->query("SELECT DISTINCT type FROM hw_inventory ORDER BY type");
            while ($row = $res->fetch_assoc()):
                $val = htmlspecialchars($row['type']);
                $sel = ($type_filter === $row['type']) ? 'selected' : '';
                echo "<option value='$val' $sel>$val</option>";
            endwhile;
            ?>
        </select>
        <button type="submit" class="btn btn-sm" style="margin-top:0;">Apply</button>
        <div class="spacer"></div>
        <span style="font-size:12px; color:var(--gray-500);">
            <?php echo number_format($total_records); ?> record(s) found
        </span>
    </form>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="data-table table-sticky">
            <thead>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $col))); ?></th>
                    <?php endforeach; ?>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <!-- ── ADD ROW ──────────────────────────────── -->
                <form action="add_hardware.php" method="POST">
                <tr class="add-row">
                    <?php foreach ($columns as $col): ?>
                        <td>
                        <?php if ($col === 'id'): ?>
                            <input type="hidden" name="id">
                            <span style="font-size:11px;color:var(--gray-500);">auto</span>
                        <?php elseif (in_array($col, $dropdown_fields)): ?>
                            <input list="<?php echo $col; ?>_list"
                                   name="<?php echo $col; ?>"
                                   placeholder="<?php echo ucfirst($col); ?>"
                                   required>
                            <datalist id="<?php echo $col; ?>_list">
                            <?php
                            $r = $conn->query("SELECT DISTINCT `$col` FROM hw_inventory WHERE `$col` != '' ORDER BY `$col`");
                            while ($dr = $r->fetch_assoc()):
                                echo "<option value='" . htmlspecialchars($dr[$col]) . "'>";
                            endwhile;
                            ?>
                            </datalist>
                        <?php else: ?>
                            <input type="text"
                                   name="<?php echo $col; ?>"
                                   placeholder="<?php echo ucfirst(str_replace('_',' ',$col)); ?>"
                                   required>
                        <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="submit" class="tbl-btn tbl-btn-add">Add</button>
                    </td>
                </tr>
                </form>

                <!-- ── DATA ROWS ────────────────────────────── -->
                <?php while ($row = $result->fetch_assoc()):
                    $row_id = (int)$row['id'];
                ?>
                <tr id="row_<?php echo $row_id; ?>">
                    <?php foreach ($columns as $col): ?>
                        <td>
                            <input class="<?php echo $col; ?>"
                                   value="<?php echo htmlspecialchars($row[$col]); ?>"
                                   readonly>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <form method="POST"
                              action="update_hardware.php"
                              id="form_<?php echo $row_id; ?>"
                              style="display:flex;gap:5px;">
                            <?php foreach ($columns as $col): ?>
                                <input type="hidden"
                                       name="<?php echo $col; ?>"
                                       value="<?php echo htmlspecialchars($row[$col]); ?>">
                            <?php endforeach; ?>
                            <button type="button"
                                    class="tbl-btn tbl-btn-edit"
                                    onclick="enableEdit(<?php echo $row_id; ?>)">
                                Edit
                            </button>
                            <button type="submit"
                                    class="tbl-btn tbl-btn-save"
                                    id="save_<?php echo $row_id; ?>">
                                Save
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div><!-- /.table-wrapper -->

    <!-- ── Pagination ─────────────────────────────────────── -->
    <div class="pagination">
        <?php
        $qs     = http_build_query(['search' => $search, 'type' => $type_filter]);
        $vis    = 7;
        $start  = max(1, $page - 3);
        $end    = min($total_pages, $start + $vis - 1);

        if ($start > 1):
            echo "<a href='?page=1&$qs'>1</a>";
            if ($start > 2) echo "<span style='padding:6px 4px;color:var(--gray-500);'>…</span>";
        endif;

        for ($i = $start; $i <= $end; $i++):
            $active = ($i === $page) ? ' active' : '';
            echo "<a class='$active' href='?page=$i&$qs'>$i</a>";
        endfor;

        if ($end < $total_pages):
            if ($end < $total_pages - 1) echo "<span style='padding:6px 4px;color:var(--gray-500);'>…</span>";
            echo "<a href='?page=$total_pages&$qs'>$total_pages</a>";
        endif;
        ?>
    </div>

    <a href="../itsc.php" class="back-link">&#8592; Back to Dashboard</a>

</main><!-- /.page-content -->

<!-- ── Site Footer ─────────────────────────────────────────── -->
<?php require_once __DIR__ . '/../includes/footer_include.php'; ?>

<script>
function enableEdit(id) {

    var row = document.getElementById('row_' + id);

    var inputs = row.querySelectorAll('input');

    inputs.forEach(function(inp){

        var fieldName = inp.className.replace(' editable','');

        /*
        |--------------------------------------------------------------------------
        | PLACED COLUMN → DROPDOWN
        |--------------------------------------------------------------------------
        */

        if(fieldName === 'placed'){

            var currentValue = inp.value;

            var select = document.createElement('select');

            select.className = 'placed editable';

            select.style.width = '120px';
            select.style.fontSize = '12px';
            select.style.padding = '5px 7px';
            select.style.borderRadius = '5px';
            select.style.border = '1px solid var(--gold)';
            select.style.background = 'var(--gold-pale)';

            /*
            |--------------------------------------------------------------------------
            | EXISTING PLACED VALUES
            |--------------------------------------------------------------------------
            */

            var options = [

                <?php
                $r = $conn->query("
                SELECT DISTINCT placed
                FROM hw_inventory
                WHERE placed != ''
                ORDER BY placed
                ");

                $arr = [];

                while($d = $r->fetch_assoc()){

                    $arr[] = '"' . addslashes($d['placed']) . '"';
                }

                echo implode(",", $arr);
                ?>

            ];

            options.forEach(function(opt){

                var option = document.createElement('option');

                option.value = opt;
                option.text  = opt;

                if(opt === currentValue){

                    option.selected = true;
                }

                select.appendChild(option);
            });

            select.addEventListener('change', function(){

                var hidden = document.querySelector(
                    '#form_' + id +
                    ' input[name="placed"]'
                );

                if(hidden){

                    hidden.value = select.value;
                }
            });

            inp.parentNode.replaceChild(select, inp);
        }

        /*
        |--------------------------------------------------------------------------
        | NORMAL INPUTS
        |--------------------------------------------------------------------------
        */

        else{

            inp.removeAttribute('readonly');

            inp.classList.add('editable');

            inp.addEventListener('input', function () {

                var hidden = document.querySelector(
                    '#form_' + id +
                    ' input[name="' + fieldName + '"]'
                );

                if (hidden){

                    hidden.value = inp.value;
                }
            });
        }
    });

    document.getElementById('save_' + id).style.display = 'inline-block';
}
</script>
</body>
</html>
