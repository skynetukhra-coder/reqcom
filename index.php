<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Department Portal</title>

  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600;700&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet"/>

  <style>

    :root{
      --navy:#0a2140;
      --gold:#c8960c;
      --gold2:#f0c040;
      --cream:#faf6ed;
      --white:#ffffff;
      --red:#8b0000;
      --shadow:0 4px 24px rgba(10,33,64,0.18);
    }

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
    }

    body{
      min-height:100vh;
      background:var(--cream);
      font-family:'Rajdhani',sans-serif;
      display:flex;
      flex-direction:column;
      align-items:center;
    }

    .top-stripe{
      width:100%;
      height:6px;
      background:linear-gradient(
        90deg,
        var(--navy) 0%,
        var(--gold) 50%,
        var(--navy) 100%
      );
    }

    header{
      width:100%;
      background:var(--navy);
      padding:18px 40px;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:32px;
      box-shadow:var(--shadow);
      flex-wrap:wrap;
    }

    /* LOGOS */

    .ashok-chakra,
    .ag-logo{
      width:90px;
      height:90px;
      object-fit:contain;
      flex-shrink:0;
      background:white;
      border-radius:50%;
      padding:4px;
      box-shadow:0 2px 10px rgba(0,0,0,0.18);
    }

    /* DEPARTMENT TEXT */

    .dept-info{
      text-align:center;
      flex:1;
      max-width:640px;
    }

    .govt-line{
      font-family:'EB Garamond',serif;
      font-size:13px;
      letter-spacing:2px;
      color:var(--gold2);
      text-transform:uppercase;
      margin-bottom:4px;
    }

    .dept-name{
      font-family:'Rajdhani',sans-serif;
      font-size:26px;
      font-weight:700;
      color:var(--white);
      line-height:1.15;
      text-transform:uppercase;
      letter-spacing:1px;
    }

    .dept-sub{
      font-family:'EB Garamond',serif;
      font-size:14px;
      color:var(--gold2);
      margin-top:5px;
      letter-spacing:1px;
    }

    .gold-bar{
      width:100%;
      height:4px;
      background:linear-gradient(
        90deg,
        var(--navy),
        var(--gold),
        var(--gold2),
        var(--gold),
        var(--navy)
      );
    }

    main{
      flex:1;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      padding:60px 20px;
      width:100%;
    }

    .welcome-block{
      text-align:center;
      margin-bottom:60px;
    }

    .welcome-block h2{
      font-family:'EB Garamond',serif;
      font-size:34px;
      color:var(--navy);
      font-weight:600;
      margin-bottom:10px;
    }

    .welcome-block p{
      font-size:16px;
      color:#4a5568;
      letter-spacing:0.5px;
    }

    .divider{
      width:80px;
      height:3px;
      background:var(--gold);
      margin:16px auto;
      border-radius:2px;
    }

    /* CARDS */

    .cards{
      display:flex;
      gap:48px;
      flex-wrap:wrap;
      justify-content:center;
    }

    .card{
      width:260px;
      background:var(--white);
      border:1px solid #ddd;
      border-radius:12px;
      box-shadow:var(--shadow);
      overflow:hidden;
      text-decoration:none;
      transition:transform 0.22s ease, box-shadow 0.22s ease;
      display:flex;
      flex-direction:column;
    }

    .card:hover{
      transform:translateY(-6px);
      box-shadow:0 12px 36px rgba(10,33,64,0.22);
    }

    .card-accent{
      height:6px;
    }

    .card.req .card-accent{
      background:linear-gradient(90deg,var(--navy),#1a5276);
    }

    .card.comp .card-accent{
      background:linear-gradient(90deg,var(--red),#c0392b);
    }

    .card-icon{
      font-size:52px;
      text-align:center;
      padding:32px 20px 16px;
      line-height:1;
    }

    .card-body{
      padding:0 24px 28px;
      text-align:center;
      flex:1;
      display:flex;
      flex-direction:column;
      align-items:center;
      gap:10px;
    }

    .card-title{
      font-family:'Rajdhani',sans-serif;
      font-size:22px;
      font-weight:700;
      color:var(--navy);
      letter-spacing:1px;
      text-transform:uppercase;
    }

    .card-desc{
      font-size:14px;
      color:#6b7280;
      line-height:1.5;
    }

    .card-btn{
      margin-top:10px;
      padding:10px 28px;
      border-radius:6px;
      font-family:'Rajdhani',sans-serif;
      font-size:15px;
      font-weight:700;
      letter-spacing:1px;
      text-transform:uppercase;
      border:none;
      cursor:pointer;
      text-decoration:none;
      display:inline-block;
      transition:opacity 0.18s;
    }

    .card-btn:hover{
      opacity:0.85;
    }

    .card.req .card-btn{
      background:var(--navy);
      color:var(--white);
    }

    .card.comp .card-btn{
      background:var(--red);
      color:var(--white);
    }

    footer{
      width:100%;
      background:var(--navy);
      color:var(--gold2);
      text-align:center;
      padding:14px 20px;
      font-size:13px;
      letter-spacing:0.5px;
    }

    /* RESPONSIVE */

    @media(max-width:768px){

      header{
        padding:20px;
        gap:20px;
      }

      .dept-name{
        font-size:20px;
      }

      .ashok-chakra,
      .ag-logo{
        width:70px;
        height:70px;
      }

      .welcome-block h2{
        font-size:28px;
      }

    }

  </style>
</head>

<body>

<div class="top-stripe"></div>

<header>

  <!-- Ashok Chakra -->
  <img
    class="ashok-chakra"
    src="images/images.png"
    alt="Ashok Chakra"
  >

  <!-- Department Info -->
  <div class="dept-info">

    <div class="govt-line">
      भारतीय लेखा तथा लेखा-परीक्षा विभाग
    </div>

    <div class="dept-name">
      Indian Audit And Accounts Department<br>
      O/o The PR.AG (A&E), West Bengal
    </div>

    <div class="dept-sub">
      Integrated ITSC Services Portal
    </div>

  </div>

  <!-- AG Logo -->
  <img
    class="ag-logo"
    src="images/IA&AS_Logo.png"
    alt="AG Logo"
  >

</header>

<div class="gold-bar"></div>

<main>

  <div class="welcome-block">

    <h2>Welcome to the ITSC Portal</h2>

    <div class="divider"></div>

    <p>Please select a module to proceed</p>

  </div>

  <div class="cards">

    <!-- REQUISITION -->

    <a class="card req" href="OIM_Project/Logins/login.php">

      <div class="card-accent"></div>

      <div class="card-icon">📋</div>

      <div class="card-body">

        <div class="card-title">
          Requisitions
        </div>

        <div class="card-desc">
          Raise and track material requisitions,
          approvals and indents
        </div>

        <span class="card-btn">
          Open Module
        </span>

      </div>

    </a>

    <!-- COMPLAINT -->

    <a class="card comp" href="Handcomp/login.php">

      <div class="card-accent"></div>

      <div class="card-icon">📝</div>

      <div class="card-body">

        <div class="card-title">
          Complaints
        </div>

        <div class="card-desc">
          Lodge, view and manage complaints
          and grievances
        </div>

        <span class="card-btn">
          Open Module
        </span>

      </div>

    </a>

  </div>

</main>

<footer>
  &copy;
  <span id="yr"></span>
  |
  Department Portal
  |
  All Rights Reserved
</footer>

<script>
  document.getElementById('yr').textContent =
  new Date().getFullYear();
</script>

</body>
</html>