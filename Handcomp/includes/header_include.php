<?php
if(!isset($root_path)){
    $root_path = "/project/Handcomp/";
}

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$full_name = $_SESSION['full_name'] ?? 'User';
?>

<style>

/* HEADER */

.top-header{

    width:100%;

    background:#0d2c54;

    padding:14px 28px;

    display:flex;

    align-items:center;

    justify-content:space-between;

    flex-wrap:wrap;

    gap:20px;

    box-shadow:0 2px 10px rgba(0,0,0,.12);

    position:sticky;

    top:0;

    z-index:999;
}

/* BRAND */

.brand{

    display:flex;

    align-items:center;

    gap:14px;
}

.brand img{

    width:56px;
    height:56px;

    object-fit:contain;
}

.brand-text h1{

    margin:0;

    color:#fff;

    font-size:28px;

    font-family:'EB Garamond', serif;

    font-weight:600;
}

.brand-text p{

    margin:3px 0 0;

    color:rgba(255,255,255,.65);

    font-size:11px;

    letter-spacing:.5px;
}

/* RIGHT SECTION */

.header-right{

    display:flex;

    align-items:center;

    gap:16px;

    flex-wrap:wrap;
}

/* WELCOME */

.welcome-text{

    color:white;

    font-size:13px;

    font-weight:500;

    background:rgba(255,255,255,.08);

    padding:9px 14px;

    border-radius:8px;
}

.welcome-text span{

    font-weight:700;

    color:#d4af5a;
}

/* LOGOUT */

.logout-btn{

    background:#dc2626;

    color:white;

    text-decoration:none;

    padding:9px 16px;

    border-radius:8px;

    font-size:13px;

    font-weight:600;

    transition:.2s;
}

.logout-btn:hover{

    background:#b91c1c;
}

/* MOBILE */

@media(max-width:768px){

    .top-header{

        flex-direction:column;

        align-items:flex-start;
    }

    .header-right{

        width:100%;

        justify-content:space-between;
    }
}

</style>

<header class="top-header">

<!-- LEFT -->

<div class="brand">

<img src="<?php echo $root_path; ?>assets/images/IA&AS_Logo.png">

<div class="brand-text">

<h1>ITSC DASHBOARD</h1>

<p>
Hardware & Complaint Management System
</p>

</div>

</div>

<!-- RIGHT -->

<div class="header-right">

<div class="welcome-text">

Welcome,
<span>
<?php echo htmlspecialchars($full_name); ?>
</span>

</div>

<a href="<?php echo $root_path; ?>logout.php" 
class="logout-btn">

Logout

</a>

</div>

</header>