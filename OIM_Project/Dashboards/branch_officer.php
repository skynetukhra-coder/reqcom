<?php

session_start();
include "../db_connect.php";

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== "Branch Officer") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Branch Officer Dashboard</title>
    <link rel="stylesheet" href="/OIM_Project/css/loginstyle.css">

    <script>
        function fetchLastIssuedDate(requisition) {
            if (requisition === "") return;

            fetch("fetch_last_issue.php?item=" + requisition)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("popup-text").innerText = data;
                    document.getElementById("popup").style.display = "block";
                });
        }

        function closePopup() {
            document.getElementById("popup").style.display = "none";
        }
    </script>

    <style>
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
        }

        .popup-box {
            background: #fff;
            width: 350px;
            margin: 150px auto;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
        }

        .popup-box button {
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="login-container">
    <h2>Branch Officer Dashboard</h2>
    <p><b>User:</b> <?php echo $_SESSION['userid']; ?></p>

    <form method="POST">

        <div class="input-group">
            <label>Section Name</label>
            <input type="text" name="section_name" required>
        </div>

        <div class="input-group">
            <label>Requisition</label>
            <select name="requisition"
                    onchange="fetchLastIssuedDate(this.value)"
                    required>
                <option value="">-- Select Requisition --</option>
                <option value="Cartridge">Cartridge</option>
                <option value="Battery">Battery</option>
            </select>
        </div>

        <div class="input-group">
            <label>Decision</label>
            <select name="decision" required>
                <option value="">-- Select Action --</option>
                <option value="Approved">Approve</option>
                <option value="Rejected">Reject</option>
                <option value="Forwarded to ITSC">Forward to ITSC</option>
            </select>
        </div>

        <div class="input-group">
            <label>Remarks</label>
            <textarea name="remarks" rows="3"></textarea>
        </div>

        <button type="submit" name="action_submit">Submit Decision</button>
    </form>

    <br>
    <a href="/OIM_Project/Logins/logout.php">Logout</a>
</div>

<!-- POPUP -->
<div class="popup-overlay" id="popup">
    <div class="popup-box">
        <h3>Last Issued Date</h3>
        <p id="popup-text"></p>
        <button onclick="closePopup()">OK</button>
    </div>
</div>

</body>
</html>

<?php
if (isset($_POST['action_submit'])) {
    echo "<script>alert('Action submitted successfully');</script>";

    /*
      🔜 FUTURE SQL LOGIC:
      UPDATE requisitions
      SET status = ?, remarks = ?
      WHERE id = ?
    */
}
?>
