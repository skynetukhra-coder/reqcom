<?php

session_start();

// 1. Initial OTP Verification (from verify_otp.php)
if (isset($_POST['otp'])) {
    if ($_POST['otp'] == $_SESSION['otp'] && time() <= $_SESSION['otp_expiry']) {
        $_SESSION['otp_verified'] = true;
    } else {
        header("Location: verify_otp.php?error=1");
        exit();
    }
}

// 2. Security Check: Block access if OTP isn't verified yet
if (!isset($_SESSION['otp_verified'])) {
    header("Location: forgetpassword.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body>

<div class="login-container">
    <h2>Reset Password</h2>

    <form action="reset_otp.php" method="POST">
        <input type="hidden" name="reset" value="1">

        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit">Update Password</button>
    </form>
</div>
</body>
</html>

<?php
// 3. Handle Password Update (Self-Processing)
if (isset($_POST['reset'])) {
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new === $confirm) {
        // UPDATE DATABASE LOGIC HERE
        session_destroy();
        echo "<script>alert('Password reset successful!');window.location='login.php';</script>";
    } else {
        echo "<script>alert('Passwords do not match');</script>";
    }
}
?>