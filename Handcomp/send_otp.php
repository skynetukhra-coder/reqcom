<?php

session_start();

$userid = $_POST['userid'];

// Generate 6-digit OTP
$otp = rand(100000, 999999);

// Store OTP & expiry (5 minutes)
$_SESSION['otp'] = $otp;
$_SESSION['otp_user'] = $userid;
$_SESSION['otp_expiry'] = time() + 300;

/*
  👉 IN PRODUCTION:
  Send OTP via Email/SMS here
*/

// TEMP: Display OTP (for testing)
echo "<h2 style='text-align:center;'>OTP sent successfully</h2>";
echo "<p style='text-align:center;'>Your OTP is: <b>$otp</b></p>";
echo "<p style='text-align:center;'><a href='verify_otp.php'>Verify OTP</a></p>";
