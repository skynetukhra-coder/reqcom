<?php 

session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body>

<div class="login-container">
    <h2>Verify OTP</h2>

    <?php
    if (isset($_GET['error'])) {
        echo "<div class='error'>Invalid or Expired OTP</div>";
    }
    ?>

    <form action="reset_password.php" method="POST">
        <div class="input-group">
            <label>Enter OTP</label>
            <input type="text" name="otp" required>
        </div>

        <button type="submit">Verify OTP</button>
    </form>
</div>

</body>
</html>
