<?php

session_start();

require_once dirname(__DIR__) . "/db_connect.php";

/* SESSION CHECK */

if(!isset($_SESSION['reset_user'])){

    header("Location: forgot_password.php");

    exit();
}

$user_id = $_SESSION['reset_user'];

/* RESET PASSWORD */

if(isset($_POST['reset'])){

    $entered_otp = trim($_POST['otp']);

    $new_password = $_POST['password'];

    $sql = "
    SELECT *
    FROM password_otp
    WHERE user_id = ?
    AND otp = ?
    /*AND expiry >= NOW()*/
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ss",
        $user_id,
        $entered_otp
    );

    $stmt->execute();

    $res = $stmt->get_result();

    if($res->num_rows > 0){

        /* UPDATE PASSWORD */

        $hashed = password_hash(
            $new_password,
            PASSWORD_DEFAULT
        );

        $update = "
        UPDATE users
        SET password = ?
        WHERE user_id = ?
        ";

        $stmt2 = $conn->prepare($update);

        $stmt2->bind_param(
            "si",
            $hashed,
            $user_id
        );

        $stmt2->execute();

        /* DELETE OTP */

        $conn->query(
            "DELETE FROM password_otp WHERE user_id = $user_id"
        );

        session_destroy();

        echo "
        <script>

        alert('Password Reset Successful');

        window.location='login.php';

        </script>
        ";

    } else {

        echo "
        <script>

        alert('Invalid or Expired OTP');

        </script>
        ";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Verify OTP</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:Arial,sans-serif;

    min-height:100vh;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:20px;

    background:
    linear-gradient(
        135deg,
        #edf2f7,
        #dfe7f0
    );
}

/* WRAPPER */

.main-wrapper{

    width:100%;
    max-width:900px;
}

/* CARD */

.auth-card{

    background:#fff;

    border-radius:20px;

    overflow:hidden;

    border:1px solid #d7dfe8;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.08);
}

/* HEADER */

.header-top{

    background:
    linear-gradient(
        135deg,
        #f6f2e8,
        #ece7d8
    );

    border-bottom:4px solid #b8860b;

    padding:16px 20px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:15px;
}

.logo{

    width:68px;
    height:68px;

    object-fit:contain;
}

.header-center{

    flex:1;

    text-align:center;
}

.hindi{

    font-size:13px;

    font-weight:600;

    color:#8a6916;
}

.eng{

    margin-top:4px;

    font-size:22px;

    font-weight:700;

    color:#1a3a6b;
}

.sub{

    margin-top:5px;

    font-size:13px;

    color:#666;
}

/* BODY */

.auth-body{

    padding:45px 40px;
}

/* ICON */

.icon-box{

    width:90px;
    height:90px;

    margin:auto auto 20px;

    border-radius:50%;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    display:flex;

    align-items:center;

    justify-content:center;

    color:#fff;

    font-size:42px;

    box-shadow:
    0 10px 25px rgba(26,58,107,0.2);
}

/* TITLE */

.page-title{

    text-align:center;

    font-size:30px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:10px;
}

/* SUB */

.page-sub{

    text-align:center;

    font-size:14px;

    color:#666;

    margin-bottom:35px;

    line-height:1.7;
}

/* FORM */

.form-group{

    margin-bottom:22px;
}

.form-group label{

    display:block;

    margin-bottom:8px;

    font-size:13px;

    font-weight:600;

    color:#1a3a6b;
}

.input-box{

    position:relative;
}

.input-box i{

    position:absolute;

    left:15px;
    top:50%;

    transform:translateY(-50%);

    color:#666;

    font-size:18px;
}

.input-box input{

    width:100%;

    padding:
    14px
    14px
    14px
    48px;

    border:1px solid #ccd6e0;

    border-radius:12px;

    font-size:14px;

    background:#fff;

    transition:0.3s;
}

.input-box input:focus{

    outline:none;

    border-color:#1a3a6b;

    box-shadow:
    0 0 0 3px rgba(26,58,107,0.08);
}

/* BUTTON */

.submit-btn{

    width:100%;

    border:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    padding:15px;

    border-radius:12px;

    font-size:15px;

    font-weight:600;

    cursor:pointer;

    transition:0.3s;

    display:flex;

    align-items:center;

    justify-content:center;

    gap:10px;
}

.submit-btn:hover{

    transform:translateY(-2px);

    box-shadow:
    0 10px 20px rgba(26,58,107,0.18);
}

/* FOOTER */

.footer-note{

    margin-top:30px;

    text-align:center;

    font-size:12px;

    color:#777;
}

/* MOBILE */

@media(max-width:768px){

    .header-top{

        flex-direction:column;
    }

    .auth-body{

        padding:35px 20px;
    }

    .page-title{

        font-size:24px;
    }
}

</style>

</head>

<body>

<div class="main-wrapper">

<div class="auth-card">

    <!-- HEADER -->

    <div class="header-top">

        <img
        src="../../OIM_Project/assets/ashoka.png"
        class="logo">

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

        <img
        src="../../OIM_Project/assets/ag_logo.png"
        class="logo">

    </div>

    <!-- BODY -->

    <div class="auth-body">

        <div class="icon-box">

            <i class="ti ti-shield-lock"></i>

        </div>

        <div class="page-title">

            Verify OTP

        </div>

        <div class="page-sub">

            Enter the One-Time Password (OTP)
            received and create your new password
            securely.

        </div>

        <!-- FORM -->

        <form method="POST">

            <!-- OTP -->

            <div class="form-group">

                <label>

                    Enter OTP

                </label>

                <div class="input-box">

                    <i class="ti ti-key"></i>

                    <input
                    type="text"
                    name="otp"
                    placeholder="Enter OTP"
                    required>

                </div>

            </div>

            <!-- PASSWORD -->

            <div class="form-group">

                <label>

                    New Password

                </label>

                <div class="input-box">

                    <i class="ti ti-lock"></i>

                    <input
                    type="password"
                    name="password"
                    placeholder="Enter New Password"
                    required>

                </div>

            </div>

            <!-- BUTTON -->

            <button
            type="submit"
            name="reset"
            class="submit-btn">

                <i class="ti ti-check"></i>

                Reset Password

            </button>

        </form>

        <!-- FOOTER -->

        <div class="footer-note">

            Government Use Only · Secure OTP Verification

        </div>

    </div>

</div>

</div>

</body>

</html>