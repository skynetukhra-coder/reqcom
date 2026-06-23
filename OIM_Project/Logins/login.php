<?php
session_start();

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] == 'ITSC') {
        header("Location: ../Dashboards/itsc_dashboard.php");
    } elseif ($_SESSION['role'] == 'BO') {
        header("Location: ../Dashboards/bo_home.php");
    } elseif ($_SESSION['role'] == 'SO') {
        header("Location: ../Dashboards/section_officer.php");
    }

    exit();
}

$error = "";

if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Office Item Management System</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{

    font-family: Arial, sans-serif;

    min-height:100vh;

    background:
    linear-gradient(
        135deg,
        #edf2f7,
        #dfe7f0
    );

    display:flex;

    align-items:center;

    justify-content:center;

    padding:20px;
}

.login-wrapper{

    width:100%;

    max-width:520px;
}

.main-card{

    background:#fff;

    border-radius:22px;

    overflow:hidden;

    border:1px solid #d8e0ea;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.08);
}

/* HEADER */

.gov-header{

    background:
    linear-gradient(
        135deg,
        #ffffff,
        #f4f7fb
    );

    padding:18px 20px 14px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    border-bottom:4px solid #c89b3c;
}

.logo-box{

    width:72px;

    display:flex;

    align-items:center;

    justify-content:center;
}

.logo-box img{

    width:60px;

    height:60px;

    object-fit:contain;
}

.header-center{

    flex:1;

    text-align:center;

    padding:0 12px;
}

.header-center .hindi{

    font-size:13px;

    color:#8a6b1f;

    font-weight:600;

    line-height:1.4;
}

.header-center .eng{

    font-size:18px;

    color:#1a3a6b;

    font-weight:700;

    margin-top:4px;
}

.header-center .sub{

    margin-top:5px;

    font-size:13px;

    color:#6d7784;
}

/* BODY */

.login-body{

    padding:32px;
}

.login-title{

    display:flex;

    align-items:center;

    gap:10px;

    color:#1a3a6b;

    font-size:24px;

    font-weight:700;

    margin-bottom:25px;
}

.form-group{

    margin-bottom:18px;
}

.form-group label{

    display:block;

    margin-bottom:8px;

    font-size:13px;

    font-weight:600;

    color:#42566d;
}

.form-control{

    width:100%;

    height:48px;

    border-radius:12px;

    border:1px solid #cfd8e3;

    background:#f8fafc;

    padding:0 14px;

    font-size:14px;

    transition:0.2s;
}

.form-control:focus{

    outline:none;

    background:#fff;

    border-color:#1a3a6b;

    box-shadow:
    0 0 0 3px rgba(26,58,107,0.08);
}

.login-row{

    margin-top:24px;

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:15px;

    flex-wrap:wrap;
}

.login-btn{

    border:none;

    background:
    linear-gradient(
        135deg,
        #1a3a6b,
        #102544
    );

    color:white;

    height:46px;

    padding:0 22px;

    border-radius:12px;

    font-size:14px;

    font-weight:600;

    cursor:pointer;

    display:flex;

    align-items:center;

    gap:8px;
}

.login-btn:hover{

    opacity:0.95;
}

.forgot-link{

    text-decoration:none;

    color:#1a3a6b;

    font-size:13px;

    font-weight:600;

    display:flex;

    align-items:center;

    gap:6px;
}

.alert-box{

    background:#fdecea;

    color:#b71c1c;

    padding:12px 14px;

    border-radius:10px;

    margin-bottom:18px;

    border-left:4px solid #b71c1c;

    font-size:13px;
}

.footer-note{

    margin-top:18px;

    text-align:center;

    font-size:12px;

    color:#6c7a89;
}

@media(max-width:600px){

    .gov-header{

        flex-direction:column;

        gap:12px;
    }

    .login-body{

        padding:24px;
    }

    .login-row{

        flex-direction:column;

        align-items:stretch;
    }

    .login-btn{

        width:100%;

        justify-content:center;
    }

    .forgot-link{

        justify-content:center;
    }
}

</style>

</head>

<body>

<div class="login-wrapper">

    <div class="main-card">

        <!-- HEADER -->

        <div class="gov-header">

            <!-- LEFT LOGO -->

            <div class="logo-box">

                <img src="../assets/ashoka.png">

            </div>

            <!-- CENTER -->

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

            <!-- RIGHT LOGO -->

            <div class="logo-box">

                <img src="../assets/ag_logo.png">

            </div>

        </div>

        <!-- BODY -->

        <div class="login-body">

            <div class="login-title">

                <i class="ti ti-user-circle"></i>

                User Login

            </div>

            <?php if (!empty($error)) { ?>

                <div class="alert-box">

                    <?php echo $error; ?>

                </div>

            <?php } ?>

            <form action="login_authenticate.php" method="POST">

                <div class="form-group">

                    <label>User ID</label>

                    <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Enter your User ID"
                    required>

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter Password"
                    required>

                </div>

                <div class="login-row">

                    <button type="submit" class="login-btn">

                        <i class="ti ti-login"></i>

                        Login

                    </button>

                    <a href="forgot_password.php" class="forgot-link">

                        <i class="ti ti-key"></i>

                        Forgot Password?

                    </a>

                </div>

            </form>

            </form>

            <!-- HOME BUTTON -->

            <div style="
                display:flex;
                gap:12px;
                align-items:center;
                justify-content:center;
                margin-top:18px;
            ">

                <a
                href="/project/index.php"
                style="
                    text-decoration:none;
                    background:linear-gradient(135deg,#f7f2e7,#ece3c9);
                    color:#1a3a6b;
                    border:1px solid #c8b273;
                    padding:8px 18px;
                    border-radius:30px;
                    font-size:13px;
                    font-weight:600;
                    display:flex;
                    align-items:center;
                    gap:6px;
                    box-shadow:0 3px 8px rgba(0,0,0,0.08);
                    transition:0.3s;
                "
                onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 5px 12px rgba(0,0,0,0.12)'"
                onmouseout="this.style.transform='translateY(0px)';this.style.boxShadow='0 3px 8px rgba(0,0,0,0.08)'">

                    <i class="ti ti-home"></i>

                    Home

                </a>

            </div>

            <!-- FOOTER NOTE -->

            <div class="footer-note">

                © 2026 · Office Item Management System

            </div>

        </div>

    </div>

</div>

</body>

</html>