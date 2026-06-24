<?php

session_start();

$userid = $_POST['userid'];

/* GENERATE 6-DIGIT OTP */

$otp = rand(100000, 999999);

/* STORE OTP & EXPIRY (5 MINUTES) */

$_SESSION['otp'] = $otp;

$_SESSION['otp_user'] = $userid;

$_SESSION['otp_expiry'] = time() + 300;

/*
|--------------------------------------------------------------------------
| IN PRODUCTION
|--------------------------------------------------------------------------
|
| Send OTP via Email/SMS here
|
*/

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>OTP Sent Successfully</title>

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

    max-width:850px;
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

    text-align:center;
}

/* SUCCESS ICON */

.icon-box{

    width:90px;
    height:90px;

    margin:auto auto 20px;

    border-radius:50%;

    background:
    linear-gradient(
        135deg,
        #2e7d32,
        #1b5e20
    );

    display:flex;

    align-items:center;

    justify-content:center;

    color:#fff;

    font-size:42px;

    box-shadow:
    0 10px 25px rgba(46,125,50,0.22);
}

/* TITLE */

.page-title{

    font-size:30px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:10px;
}

/* SUBTEXT */

.page-sub{

    font-size:15px;

    color:#555;

    line-height:1.7;

    margin-bottom:28px;
}

/* OTP BOX */

.otp-box{

    display:inline-block;

    background:
    linear-gradient(
        135deg,
        #f6f8fb,
        #eef2f7
    );

    border:1px solid #d6dfea;

    border-radius:16px;

    padding:18px 30px;

    margin-bottom:28px;

    box-shadow:
    inset 0 1px 2px rgba(255,255,255,0.6);
}

.otp-label{

    font-size:13px;

    color:#666;

    margin-bottom:8px;
}

.otp-number{

    font-size:34px;

    letter-spacing:6px;

    font-weight:700;

    color:#1a3a6b;
}

/* BUTTON */

.verify-btn{

    display:inline-flex;

    align-items:center;

    gap:10px;

    text-decoration:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    padding:14px 28px;

    border-radius:12px;

    font-size:14px;

    font-weight:600;

    transition:0.3s;
}

.verify-btn:hover{

    transform:translateY(-2px);

    box-shadow:
    0 10px 20px rgba(26,58,107,0.18);
}

/* FOOTER */

.footer-note{

    margin-top:30px;

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

    .otp-number{

        font-size:28px;

        letter-spacing:4px;
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
        src="../assets/ashoka.png"
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
        src="../assets/ag_logo.png"
        class="logo">

    </div>

    <!-- BODY -->

    <div class="auth-body">

        <div class="icon-box">

            <i class="ti ti-check"></i>

        </div>

        <div class="page-title">

            OTP Sent Successfully

        </div>

        <div class="page-sub">

            A One-Time Password has been generated
            for secure identity verification.

        </div>

        <!-- OTP DISPLAY -->

        <div class="otp-box">

            <div class="otp-label">

                Your OTP Code

            </div>

            <div class="otp-number">

                <?php echo $otp; ?>

            </div>

        </div>

        <!-- VERIFY BUTTON -->

        <div>

            <a
            href="verify_otp.php"
            class="verify-btn">

                <i class="ti ti-shield-check"></i>

                Verify OTP

            </a>

        </div>

        <!-- FOOTER -->

        <div class="footer-note">

            Government Use Only · Secure OTP Authentication

        </div>

    </div>

</div>

</div>

</body>

</html>