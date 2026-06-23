<?php

session_start();

require_once __DIR__ . "/config/db_connect.php";

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

<link rel="preconnect"
href="https://fonts.googleapis.com">

<link rel="preconnect"
href="https://fonts.gstatic.com"
crossorigin>

<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>

/* ROOT */

:root{

    --navy:#0d2c54;
    --navy-dark:#071a35;
    --navy-mid:#163d6e;

    --gold:#b8962e;

    --gray-100:#f1f3f5;
    --gray-300:#ced4da;
    --gray-700:#495057;

    --font-serif:'EB Garamond', serif;
    --font-sans:'DM Sans', sans-serif;
}

/* GLOBAL */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family:var(--font-sans);

    min-height:100vh;

    background:
    linear-gradient(
        135deg,
        var(--navy-dark) 0%,
        var(--navy-mid) 60%,
        #1a4a80 100%
    );

    display:flex;

    align-items:center;

    justify-content:center;

    padding:20px;
}

/* MAIN BOX */

.main-wrapper{

    width:100%;

    max-width:500px;
}

/* CARD */

.auth-card{

    background:white;

    border-radius:18px;

    overflow:hidden;

    border-top:4px solid var(--gold);

    box-shadow:
    0 14px 38px rgba(0,0,0,.18);
}

/* HEADER */

.header-top{

    background:
    linear-gradient(
        135deg,
        rgba(255,255,255,.98),
        rgba(245,247,251,.96)
    );

    padding:18px 18px;

    border-bottom:1px solid #e5e7eb;

    display:flex;

    align-items:center;

    justify-content:space-between;

    gap:10px;
}

/* LOGO SIDE */

.logo-side{

    width:58px;

    display:flex;

    align-items:center;

    justify-content:center;
}

/* LOGO */

.logo-box{

    width:50px;

    height:50px;

    border-radius:50%;

    overflow:hidden;

    background:white;

    display:flex;

    align-items:center;

    justify-content:center;

    box-shadow:0 0 0 2px var(--gold);
}

.logo-box img{

    width:42px;

    height:42px;

    object-fit:contain;
}

/* CENTER TITLE */

.header-center{

    flex:1;

    text-align:center;
}

.header-center h1{

    font-family:var(--font-serif);

    font-size:18px;

    color:var(--navy);

    font-weight:700;

    line-height:1.25;
}

.header-center p{

    color:#6b7280;

    font-size:10px;

    letter-spacing:.8px;

    margin-top:4px;
}

/* BODY */

.auth-body{

    padding:42px 32px;

    text-align:center;
}

/* ICON */

.icon-box{

    width:84px;
    height:84px;

    margin:auto auto 20px;

    border-radius:50%;

    background:
    linear-gradient(
        135deg,
        var(--navy),
        #102544
    );

    display:flex;

    align-items:center;

    justify-content:center;

    color:white;

    font-size:40px;

    box-shadow:
    0 10px 25px rgba(26,58,107,.18);
}

/* TITLE */

.page-title{

    font-size:26px;

    font-weight:700;

    color:var(--navy);

    margin-bottom:12px;

    font-family:var(--font-serif);
}

/* TEXT */

.page-sub{

    font-size:13px;

    color:#666;

    line-height:1.8;

    margin-bottom:30px;
}

/* FORM */

.form-group{

    margin-bottom:20px;

    text-align:left;
}

.form-group label{

    display:block;

    margin-bottom:7px;

    font-size:12px;

    font-weight:600;

    color:var(--gray-700);
}

.input-box{

    position:relative;
}

.input-box i{

    position:absolute;

    left:14px;
    top:50%;

    transform:translateY(-50%);

    color:#666;

    font-size:17px;
}

.input-box input{

    width:100%;

    padding:
    12px
    14px
    12px
    42px;

    border:1.5px solid var(--gray-300);

    border-radius:9px;

    background:var(--gray-100);

    outline:none;

    font-size:13px;

    transition:.2s;
}

.input-box input:focus{

    border-color:var(--navy);

    background:white;
}

/* BUTTON */

.submit-btn{

    width:100%;

    border:none;

    background:
    linear-gradient(
        135deg,
        var(--navy),
        #102544
    );

    color:white;

    padding:13px;

    border-radius:10px;

    font-size:13px;

    font-weight:600;

    cursor:pointer;

    transition:.2s;

    display:flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    margin-top:10px;
}

.submit-btn:hover{

    transform:translateY(-2px);

    box-shadow:
    0 10px 20px rgba(26,58,107,.18);
}

/* BACK */

.back-link{

    margin-top:22px;

    text-align:center;
}

.back-link a{

    text-decoration:none;

    color:#6b7280;

    font-size:12px;

    font-weight:500;
}

.back-link a:hover{

    color:var(--navy);
}

/* FOOTER */

.footer-note{

    margin-top:28px;

    text-align:center;

    font-size:11px;

    color:#777;
}

/* MOBILE */

@media(max-width:640px){

    .header-top{

        flex-direction:column;

        gap:12px;
    }

    .header-center h1{

        font-size:17px;
    }

    .auth-body{

        padding:30px 22px;
    }
}

</style>

</head>

<body>

<div class="main-wrapper">

<div class="auth-card">

    <!-- HEADER -->

    <div class="header-top">

        <!-- LEFT LOGO -->

        <div class="logo-side">

            <div class="logo-box">

                <img
                src="assets/images/images.png"
                alt="Ashok Stambh">

            </div>

        </div>

        <!-- CENTER -->

        <div class="header-center">

            <h1>
            O/O The Principal Accountant General
            <br>(A & E), W.B
            </h1>

            <p>
            TREASURY BUILDINGS, KOLKATA - 700001
            </p>

        </div>

        <!-- RIGHT LOGO -->

        <div class="logo-side">

            <div class="logo-box">

                <img
                src="assets/images/IA&AS_Logo.png"
                alt="IA&AD Logo">

            </div>

        </div>

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
            for secure password recovery.

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