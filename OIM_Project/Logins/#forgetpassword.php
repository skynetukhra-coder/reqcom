<?php 
session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body>

<div class="login-container">
    <h2>Forgot Password</h2>

    <form action="send_otp.php" method="POST">
        <div class="input-group">
            <label>User ID</label>
            <input type="text" name="userid" required>
        </div>

        <button type="submit">Send OTP</button>
    </form>

    <div class="forgot">
        <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>
