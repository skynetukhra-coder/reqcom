<?php
/**
 * branch_officer.php — SR. ACCOUNTS OFFICER (Branch Officer) Dashboard
 * Updated: Phase 1 UI Overhaul
 */

session_start();
require_once __DIR__ . "/config/db_connect.php";

/* ── Access control ───────────────────────────────────────── */
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$officer_name = $_SESSION['full_name'];

/* ── Header include settings ──────────────────────────────── */
$active_nav     = 'complaint_form';
$logo_base_path = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Complaint — Branch Officer — Handcomp</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Global stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>

    <style>
        /* ── Complaint form card ──────────────────────────── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
        }
        .form-grid .full-col { grid-column: 1 / -1; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ── Site Header ─────────────────────────────────────────── -->
<?php require_once __DIR__ . '/includes/header_include.php'; ?>

<!-- Success popup toast -->
<div id="popup" class="popup">&#9989; Complaint Successfully Forwarded to ITSC</div>

<!-- ── Page Content ───────────────────────────────────────── -->
<main class="page-content narrow">

    <div class="page-title">
        <h2>Forward Hardware Complaint</h2>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>&#128203; New Complaint — Branch Officer</h3>
            <p>All fields are required. Serial numbers are auto-loaded from your assigned inventory.</p>
        </div>
        <div class="card-body">

            <form method="POST" action="insert_complaint.php" id="complaintForm">
                <input type="hidden" name="forwarded_by" value="<?php echo htmlspecialchars($officer_name); ?>">

                <div class="form-grid">

                    <!-- Officer Name (readonly) -->
                    <div class="full-col">
                        <label>Officer Name</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($officer_name); ?>"
                               readonly
                               title="Auto-filled from your login session">
                    </div>

                    <!-- Device -->
                    <div>
                        <label>Device Type</label>
                        <select id="device" name="device" onchange="loadComplaints()" required>
                            <option value="">— Select Device —</option>
                            <?php
                            $res = $conn->query("SELECT DISTINCT type FROM hw_inventory ORDER BY type");
                            while ($row = $res->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['type']) . "'>"
                                   . htmlspecialchars($row['type']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label>Serial Number</label>
                        <select id="serial" name="serial" required>
                            <option value="">— Select Serial —</option>
                        </select>
                    </div>

                    <!-- Complaint -->
                    <div>
                        <label>Nature of Complaint</label>
                        <select id="complaint" name="complaint" onchange="toggleRemarks()" required>
                            <option value="">— Select Complaint —</option>
                        </select>
                    </div>

                    <!-- Remarks -->
                    <div>
                        <label>Remarks</label>
                        <input type="text"
                               id="remarks"
                               placeholder="Enter details (required if 'Other')"
                               disabled>
                        <input type="hidden" name="remarks_hidden" id="remarks_hidden">
                    </div>

                </div><!-- /.form-grid -->

                <div style="margin-top: 24px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                    <button type="submit" style="width:auto; margin-top:0; padding: 11px 28px;">
                        Forward to ITSC &rarr;
                    </button>
                    <span style="font-size:12px; color:var(--gray-500);">
                        Complaint will be timestamped automatically.
                    </span>
                </div>

            </form>

        </div><!-- /.card-body -->
    </div><!-- /.card -->

</main><!-- /.page-content -->

<!-- ── Site Footer ─────────────────────────────────────────── -->
<?php require_once __DIR__ . '/includes/footer_include.php'; ?>

<script>
/* Auto-load serials for this officer on page load */
window.addEventListener('DOMContentLoaded', function () {
    fetch("fetch_serial.php?section=<?php echo urlencode($officer_name); ?>")
        .then(res => res.text())
        .then(data => {
            document.getElementById("serial").innerHTML = data;
        });
});

/* Copy remarks into hidden field on submit */
document.getElementById('complaintForm').addEventListener('submit', function () {
    document.getElementById('remarks_hidden').value =
        document.getElementById('remarks').value;
});

/* Show popup if redirected back with ?success=1 */
<?php if (isset($_GET['success'])): ?>
(function () {
    var popup = document.getElementById('popup');
    popup.style.display = 'block';
    setTimeout(function () { popup.style.display = 'none'; }, 3500);
})();
<?php endif; ?>
</script>

</body>
</html>
