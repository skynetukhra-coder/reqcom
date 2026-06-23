<?php

session_start();

require_once dirname(__DIR__) . "/db_connect.php";

/* SEND OTP */

if(isset($_POST['send_otp'])){

    $username = $_POST['username'];

    $sql = "SELECT user_id FROM users WHERE username = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $res = $stmt->get_result();

    $user = $res->fetch_assoc();

    if($user){

        $user_id = $user['user_id'];

        $otp = rand(100000, 999999);

        $expiry = date(
            "Y-m-d H:i:s",
            strtotime("+5 minutes")
        );

        /* DELETE OLD OTP */

        $conn->query(
            "DELETE FROM password_otp WHERE user_id = $user_id"
        );

        /* INSERT OTP */

        $sql_insert = "
        INSERT INTO password_otp
        (user_id, otp, expiry)
        VALUES (?, ?, ?)
        ";

        $stmt2 = $conn->prepare($sql_insert);

        $stmt2->bind_param(
            "iss",
            $user_id,
            $otp,
            $expiry
        );

        $stmt2->execute();

        $_SESSION['reset_user'] = $user_id;

        /* DEMO OTP */

        echo "
        <script>

        alert('Your OTP is: $otp');

        window.location='verify_otp.php';

        </script>
        ";

    } else {

        echo "
        <script>

        alert('User not found');

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

<title>Forgot Password</title>

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

/* MAIN WRAPPER */

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

/* SUBTEXT */

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

/* BACK */

.back-link{

    margin-top:25px;

    text-align:center;
}

.back-link a{

    text-decoration:none;

    color:#1a3a6b;

    font-size:14px;

    font-weight:600;
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
        src="/project/OIM_Project/assets/ashoka.png"
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
        src="/project/OIM_Project/assets/ag_logo.png"
        class="logo">

    </div>

    <!-- BODY -->

    <div class="auth-body">

        <div class="icon-box">

            <i class="ti ti-lock-question"></i>

        </div>

        <div class="page-title">

            Forgot Password

        </div>

        <div class="page-sub">

            Enter your username to receive
            a One-Time Password (OTP)
            for secure password reset.

        </div>

        <!-- FORM -->

        <form method="POST">

            <div class="form-group">

                <label>

                    Username

                </label>

                <div class="input-box">

                    <i class="ti ti-user"></i>

                    <input
                    type="text"
                    name="username"
                    placeholder="Enter Username"
                    required>

                </div>

            </div>

            <button
            type="submit"
            name="send_otp"
            class="submit-btn">

                <i class="ti ti-send"></i>

                Send OTP

            </button>

        </form>

        <!-- BACK -->

        <div class="back-link">

            <a href="login.php">

                ← Back to Login

            </a>

        </div>

        <!-- FOOTER -->

        <div class="footer-note">

            Government Use Only · Secure Password Recovery

        </div>

    </div>

</div>

</div>

</body>

</html>