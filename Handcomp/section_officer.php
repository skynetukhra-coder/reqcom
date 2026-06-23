<?php
/**
 * section_officer.php — ASSTT. ACCOUNTS OFFICER (Section Officer) Dashboard
 * Updated: Phase 1 UI Overhaul - Full Rectification Version
 */

session_start();
require_once __DIR__ . "/config/db_connect.php";

/* ── Access control ───────────────────────────────────────── */
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

/* ── Header include settings ──────────────────────────────── */
$active_nav     = 'complaint_form';
$logo_base_path = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Complaint — Section Officer — Handcomp</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>

    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
        }
        .form-grid .full-col { grid-column: 1 / -1; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }

        /* History Panel Styling */
        .history-section {
            margin-top: 30px;
            display: none; /* Managed by JS visibility */
        }
        
        .history-table-wrapper {
            background: #ffffff;
            border-radius: 14px;
            overflow: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            margin-top: 12px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th {
            background: #0d2c54;
            color: #ffffff;
            padding: 12px 14px;
            font-size: 11px;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: 0.5px;
        }

        .history-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #edf2f7;
            font-size: 13px;
            color: #333;
        }

        .history-table tr:hover {
            background: #f8fbff;
        }

        .h-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }
        .h-badge-pending { background: #fee2e2; color: #dc2626; }
        .h-badge-assigned { background: #dbeafe; color: #1d4ed8; }
        .h-badge-ongoing { background: #fff3cd; color: #856404; }
        .h-badge-resolved { background: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/includes/header_include.php'; ?>

<div id="popup" class="popup" style="display:none; position:fixed; top:24px; right:24px; background:#dcfce7; color:#166534; padding:14px 20px; border-radius:10px; font-size:13px; font-weight:600; box-shadow:0 8px 20px rgba(0,0,0,.12); z-index:9999;">
    &#9989; Complaint Successfully Forwarded to ITSC
</div>

<main class="page-content narrow">

    <div class="page-title">
        <h2>Forward Hardware Complaint</h2>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>&#128203; New Complaint — Section Officer</h3>
            <p>Select your section and device to load available serial numbers. All fields are required.</p>
        </div>
        <div class="card-body">

            <form method="POST" action="insert_complaint.php" id="complaintForm">
                <input type="hidden" name="forwarded_by" id="forwarded_by">

                <div class="form-grid">

                    <div class="full-col">
                        <label>Officer Name</label>
                        <input type="text"
                               value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>"
                               readonly
                               title="Auto-filled from your login session">
                    </div>

                    <div>
                        <label>Section</label>
                        <select id="section" name="section" onchange="handleSectionChange()" required>
                            <option value="">— Select Section —</option>
                            <?php
                            $res = $conn->query("SELECT DISTINCT placed FROM hw_inventory WHERE placed != '' ORDER BY placed");
                            while ($row = $res->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['placed']) . "'>"
                                   . htmlspecialchars($row['placed']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label>Device Type</label>
                        <select id="device" name="device" onchange="if(typeof loadSerials === 'function'){ loadSerials(); } if(typeof loadComplaints === 'function'){ loadComplaints(); }" required>
                            <option value="">— Select Device —</option>
                            <?php
                            $res = $conn->query("SELECT DISTINCT type FROM hw_inventory WHERE type != '' ORDER BY type");
                            while ($row = $res->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['type']) . "'>"
                                   . htmlspecialchars($row['type']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label>Serial Number</label>
                        <select id="serial" name="serial" required>
                            <option value="">— Select Serial —</option>
                        </select>
                    </div>

                    <div>
                        <label>Nature of Complaint</label>
                        <select id="complaint" name="complaint" onchange="toggleRemarks()" required>
                            <option value="">— Select Complaint —</option>
                        </select>
                    </div>

                    <div class="full-col">
                        <label>Remarks</label>
                        <input type="text"
                               id="remarks"
                               name="remarks"
                               placeholder="Enter details (required only if 'Other' is selected)"
                               disabled>
                        <input type="hidden" name="remarks_hidden" id="remarks_hidden">
                    </div>

                </div><div style="margin-top: 24px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                    <button type="submit" style="width:auto; margin-top:0; padding: 11px 28px;">
                        Forward to ITSC &rarr;
                    </button>
                    <span style="font-size:12px; color:var(--gray-500);">
                        Complaint will be timestamped automatically.
                    </span>
                </div>

            </form>

        </div></div><div class="history-section" id="historySection">
        <div class="page-title" style="margin-top: 30px; margin-bottom: 5px;">
            <h3 style="font-family:'EB Garamond',serif; color:#0d2c54; font-size:22px;">
                📋 Recent Complaint History for: <span id="historySectionName" style="color: #b8962e;"></span>
            </h3>
        </div>
        <div class="history-table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Comp No</th>
                        <th>Device</th>
                        <th>Serial No</th>
                        <th>Complaint</th>
                        <th>Received / Resolved Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    </tbody>
            </table>
        </div>
    </div>

</main><?php require_once __DIR__ . '/includes/footer_include.php'; ?>

<script>
/**
 * Dynamic Controller for Section Changes
 */
function handleSectionChange() {
    var sectionSelect = document.getElementById('section');
    var sectionVal = sectionSelect.value;
    
    // Crucial: Update hidden forwarded_by input so your admin dashboard records it!
    document.getElementById('forwarded_by').value = sectionVal;
    
    // Call original dependency drop list handler if it exists in assets/js/script.js
    if (typeof loadSerials === "function") { 
        loadSerials(); 
    }
    
    var historyBlock = document.getElementById('historySection');
    var labelSpan = document.getElementById('historySectionName');
    var tableBody = document.getElementById('historyTableBody');

    if (!sectionVal) {
        historyBlock.style.display = 'none';
        return;
    }

    // Assign visual identifier text
    labelSpan.textContent = sectionVal;
    
    // Request historical background log via asynchronous fetch API
    fetch('get_section_history.php?section=' + encodeURIComponent(sectionVal))
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = '';
            historyBlock.style.display = 'block';

            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#6b7280; padding:20px;">No historical or current complaints found for this section.</td></tr>`;
                return;
            }

            data.forEach(item => {
                let badgeClass = 'h-badge-pending';
                let statusLabel = 'Pending';

                if (item.resolved_time) {
                    badgeClass = 'h-badge-resolved';
                    statusLabel = 'Resolved';
                } else if (item.ongoing_time) {
                    badgeClass = 'h-badge-ongoing';
                    statusLabel = 'Ongoing';
                } else if (item.assigned_time) {
                    badgeClass = 'h-badge-assigned';
                    statusLabel = 'Assigned';
                }

                let timeDisplay = item.resolved_time 
                    ? `<b>Res:</b> ${item.resolved_time}` 
                    : `<b>Rec:</b> ${item.received_time}`;

                let rowHTML = `
                    <tr>
                        <td><b>${item.comp_no}</b></td>
                        <td>${item.device}</td>
                        <td>${item.serial_no ? item.serial_no : '-'}</td>
                        <td>${item.complaint}</td>
                        <td style="font-size:12px;">${timeDisplay}</td>
                        <td><span class="h-badge ${badgeClass}">${statusLabel}</span></td>
                    </tr>
                `;
                tableBody.innerHTML += rowHTML;
            });
        })
        .catch(error => {
            console.error('History Fetch Engine Exception:', error);
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#dc2626; padding:20px;">Error updating backend data log.</td></tr>`;
        });
}

// Fallback backup validation to update hidden field on direct updates
document.getElementById('section').addEventListener('change', function () {
    document.getElementById('forwarded_by').value = this.value;
});

/* Copy remarks into hidden field on submit */
document.getElementById('complaintForm').addEventListener('submit', function () {
    document.getElementById('remarks_hidden').value = document.getElementById('remarks').value;
});

/* Popup presentation logic verification */
<?php if (isset($_GET['success'])): ?>
(function () {
    var popup = document.getElementById('popup');
    if(popup) {
        popup.style.display = 'block';
        setTimeout(function () {
            popup.style.display = 'none';
        }, 3500);
    }
})();
<?php endif; ?>
</script>

</body>
</html>