<?php

session_start();

/* CLEAR SESSION */

$_SESSION = array();

/* DESTROY SESSION */

session_destroy();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Logged Out</title>

<meta http-equiv="refresh"
content="3;url=login.php">

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

.logout-wrapper{

    width:100%;

    max-width:500px;
}

/* CARD */

.logout-card{

    background:white;

    border-radius:18px;

    overflow:hidden;

    border-top:4px solid var(--gold);

    box-shadow:
    0 14px 38px rgba(0,0,0,.18);
}

/* HEADER */

.logout-header{

    background:
    linear-gradient(
        135deg,
        rgba(255,255,255,.98),
        rgba(245,247,251,.96)
    );

    padding:18px 18px;

    border-bottom:1px solid #e5e7eb;
}

/* HEADER ROW */

.header-row{

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

.logout-body{

    padding:42px 32px;

    text-align:center;
}

/* SUCCESS ICON */

.success-icon{

    width:84px;
    height:84px;

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

    color:white;

    font-size:40px;

    box-shadow:
    0 10px 25px rgba(46,125,50,.20);
}

/* TITLE */

.logout-title{

    font-size:26px;

    font-weight:700;

    color:var(--navy);

    margin-bottom:12px;

    font-family:var(--font-serif);
}

/* TEXT */

.logout-text{

    font-size:13px;

    color:#666;

    line-height:1.8;

    margin-bottom:28px;
}

/* LOADER */

.loader{

    width:52px;
    height:52px;

    margin:0 auto 24px;

    border:5px solid #d8dee8;

    border-top:5px solid var(--navy);

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

    font-size:12px;

    color:#777;

    margin-bottom:24px;
}

/* LOGIN BUTTON */

.login-btn{

    display:inline-flex;

    align-items:center;

    justify-content:center;

    gap:8px;

    text-decoration:none;

    background:
    linear-gradient(
        135deg,
        var(--navy),
        #102544
    );

    color:white;

    padding:12px 24px;

    border-radius:10px;

    font-size:13px;

    font-weight:600;

    transition:.2s;
}

.login-btn:hover{

    transform:translateY(-2px);

    box-shadow:
    0 10px 20px rgba(26,58,107,.18);
}

/* FOOTER */

.footer-note{

    margin-top:28px;

    font-size:11px;

    color:#777;
}

/* MOBILE */

@media(max-width:640px){

    .header-row{

        flex-direction:column;

        gap:12px;
    }

    .header-center h1{

        font-size:17px;
    }

    .logout-body{

        padding:30px 22px;
    }
}

</style>

</head>

<body>

<div class="logout-wrapper">

<div class="logout-card">

    <!-- HEADER -->

    <div class="logout-header">

        <div class="header-row">

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

    </div>

    <!-- BODY -->

    <div class="logout-body">

        <div class="success-icon">

            <i class="ti ti-check"></i>

        </div>

        <div class="logout-title">

            Logged Out Successfully

        </div>

        <div class="logout-text">

            Your session has been securely terminated
            from the Hardware Complaint System.

        </div>

        <!-- LOADER -->

        <div class="loader"></div>

        <div class="redirect-text">

            Redirecting to login portal...

        </div>

        <!-- LOGIN BUTTON -->

        <a href="login.php"
        class="login-btn">

            <i class="ti ti-login"></i>

            Login Again

        </a>

        <!-- FOOTER -->

        <div class="footer-note">

            Government Use Only · Secure Logout Completed

        </div>

    </div>

</div>

</div>

</body>

</html>