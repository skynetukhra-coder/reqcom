<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Handcomp Login</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

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

.login-wrapper{

    width:100%;

    max-width:500px;

    background:white;

    border-radius:18px;

    overflow:hidden;

    border-top:4px solid var(--gold);

    box-shadow:
    0 14px 38px rgba(0,0,0,.18);
}

/* HEADER */

.login-header{

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

.login-logo{

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

.login-logo img{

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

.login-card{

    padding:38px 32px;
}

.login-card h2{

    text-align:center;

    margin-bottom:24px;

    color:var(--navy);

    font-family:var(--font-serif);

    font-size:22px;
}

/* ERROR */

.error-msg{

    background:#fff0f0;

    color:#8b1a1a;

    border:1px solid #f5c6c6;

    border-radius:8px;

    padding:10px 12px;

    font-size:12px;

    margin-bottom:16px;
}

/* FORM */

.form-group{

    margin-bottom:18px;
}

.form-group label{

    display:block;

    margin-bottom:6px;

    font-size:12px;

    font-weight:600;

    color:var(--gray-700);
}

.form-group input{

    width:100%;

    padding:12px 14px;

    border:1.5px solid var(--gray-300);

    border-radius:9px;

    background:var(--gray-100);

    outline:none;

    font-size:13px;
}

.form-group input:focus{

    border-color:var(--navy);

    background:white;
}

/* BUTTON */

.btn-primary{

    width:100%;

    padding:12px;

    border:none;

    border-radius:9px;

    background:var(--navy);

    color:white;

    font-size:13px;

    font-weight:600;

    cursor:pointer;

    margin-top:8px;

    transition:.2s;
}

.btn-primary:hover{

    background:#163d6e;
}

/* FORGOT PASSWORD */

.forgot-password{

    text-align:center;

    margin-top:15px;
}

.forgot-password a{

    color:#6b7280;

    text-decoration:none;

    font-size:11px;

    font-weight:500;
}

.forgot-password a:hover{

    color:#0d2c54;
}

/* HOME BUTTON */

.home-wrap{

    display:flex;

    justify-content:center;

    margin-top:18px;
}

.home-btn{

    text-decoration:none;

    background:
    linear-gradient(
        135deg,
        #f7f2e7,
        #ece3c9
    );

    color:#1a3a6b;

    border:1px solid #c8b273;

    padding:7px 16px;

    border-radius:24px;

    font-size:12px;

    font-weight:600;

    display:flex;

    align-items:center;

    gap:6px;

    box-shadow:0 3px 8px rgba(0,0,0,0.08);

    transition:.2s;
}

.home-btn:hover{

    transform:translateY(-1px);

    box-shadow:0 5px 12px rgba(0,0,0,0.12);
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

    .login-card{

        padding:30px 22px;
    }
}

</style>

</head>

<body>

<div class="login-wrapper">

<!-- HEADER -->

<div class="login-header">

    <div class="header-row">

        <!-- LEFT LOGO -->

        <div class="logo-side">

            <div class="login-logo">

                <img src="assets/images/images.png"
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

            <div class="login-logo">

                <img src="assets/images/IA&AS_Logo.png"
                alt="IA&AD Logo">

            </div>

        </div>

    </div>

</div>

<!-- BODY -->

<div class="login-card">

<h2>
Hardware Complaint System
</h2>

<?php if(isset($_GET['error'])){ ?>

<div class="error-msg">

Invalid Username or Password

</div>

<?php } ?>

<form method="POST"
action="login_authenticate.php">

<div class="form-group">

<label>Username</label>

<input type="text"
name="username"
required>

</div>

<div class="form-group">

<label>Password</label>

<input type="password"
name="password"
required>

</div>

<button type="submit"
class="btn-primary">

Sign In to Portal

</button>

<div class="forgot-password">

<a href="forgot_password.php" class="forgot-link">

                        <i class="ti ti-key"></i>

                        Forgot Password?

                    </a>

</div>

</form>

<!-- HOME BUTTON -->

<div class="home-wrap">

<a href="/project/index.php"
class="home-btn">

<i class="ti ti-home"></i>

Home

</a>

</div>

</div>

</div>

</body>

</html>