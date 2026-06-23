<?php

session_start();

/* CLEAR SESSION */
$_SESSION = array();

/* DESTROY COOKIE */
if (isset($_COOKIE[session_name()])) {

    setcookie(
        session_name(),
        '',
        time() - 42000,
        '/'
    );
}

/* DESTROY SESSION */
session_destroy();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Logged Out</title>

<meta http-equiv="refresh" content="3;url=login.php">

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

/* MAIN BOX */

.logout-wrapper{

    width:100%;
    max-width:850px;
}

/* LETTERHEAD */

.logout-card{

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

.logout-body{

    padding:45px 35px;

    text-align:center;
}

/* ICON */

.success-icon{

    width:95px;
    height:95px;

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

    font-size:46px;

    box-shadow:
    0 10px 25px rgba(46,125,50,0.25);
}

/* TITLE */

.logout-title{

    font-size:30px;

    font-weight:700;

    color:#1a3a6b;

    margin-bottom:12px;
}

/* TEXT */

.logout-text{

    font-size:15px;

    color:#555;

    margin-bottom:28px;

    line-height:1.7;
}

/* LOADER */

.loader{

    width:52px;
    height:52px;

    margin:0 auto 25px;

    border:5px solid #d8dee8;

    border-top:5px solid #1a3a6b;

    border-radius:50%;

    animation:spin 1s linear infinite;
}

@keyframes spin{

    0%{
        transform:rotate(0deg);
    }

    100%{
        transform:rotate(360deg);
    }
}

/* REDIRECT TEXT */

.redirect-text{

    font-size:13px;

    color:#777;

    margin-bottom:28px;
}

/* BUTTON */

.login-btn{

    display:inline-flex;

    align-items:center;

    gap:8px;

    text-decoration:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:#fff;

    padding:13px 26px;

    border-radius:12px;

    font-size:14px;

    font-weight:600;

    transition:0.3s;
}

.login-btn:hover{

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

    .logout-body{

        padding:35px 20px;
    }

    .logout-title{

        font-size:24px;
    }
}

</style>

</head>

<body>

<div class="logout-wrapper">

<div class="logout-card">

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

    <div class="logout-body">

        <div class="success-icon">

            <i class="ti ti-check"></i>

        </div>

        <div class="logout-title">

            Signed Out Successfully

        </div>

        <div class="logout-text">

            Your session has been securely terminated from the
            Office Item Management System.

        </div>

        <div class="loader"></div>

        <div class="redirect-text">

            Redirecting to login portal...

        </div>

        <a
        href="login.php"
        class="login-btn">

            <i class="ti ti-login"></i>

            Login Again

        </a>

        <div class="footer-note">

            Government Use Only · Secure Logout Completed

        </div>

    </div>

</div>

</div>

</body>

</html>