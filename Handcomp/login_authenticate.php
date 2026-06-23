<?php
session_start();

require_once __DIR__ . "/config/db_connect.php";

/* CHECK METHOD */

if($_SERVER['REQUEST_METHOD'] != 'POST'){

    header("Location: login.php");
    exit();
}

/* GET FORM DATA */

$username = trim($_POST['username']);
$password = trim($_POST['password']);

/* EMPTY CHECK */

if(empty($username) || empty($password)){

    header("Location: login.php?error=1");
    exit();
}

/* FETCH USER */

$stmt = $conn->prepare("
SELECT *
FROM users
WHERE username = ?
LIMIT 1
");

$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();

/* USER FOUND */

if($result->num_rows > 0){

    $user = $result->fetch_assoc();

    /*
    |--------------------------------------------------------------------------
    | PASSWORD CHECK
    |--------------------------------------------------------------------------
    */

    /*
    IF USING PLAIN PASSWORDS
    */

    if(password_verify($password, $user['password'])){

        /* SESSION */

        $_SESSION['username']    = $user['username'];
        $_SESSION['full_name']   = $user['full_name'];
        $_SESSION['designation'] = $user['designation'];

        $designation = strtoupper(trim($user['designation']));

        /*
        |--------------------------------------------------------------------------
        | DESIGNATION BASED LOGIN
        |--------------------------------------------------------------------------
        */

        /* ITSC */

        if($designation == 'ITSC'){

            header("Location: itsc.php");
            exit();
        }

        /* AMC */

        else if($designation == 'AMC'){

            header("Location: complaint/complaint_register.php");
            exit();
        }

        /*
        |--------------------------------------------------------------------------
        | SECTION / BRANCH OFFICERS
        |--------------------------------------------------------------------------
        */
/* SECTION OFFICER */

else if($designation == 'ASSTT. ACCOUNTS OFFICER'){

    header("Location: section_officer.php");
    exit();
}

/* BRANCH OFFICER */

else if($designation == 'SR. ACCOUNTS OFFICER'){

    header("Location: branch_officer.php");
    exit();
}

        /*
        |--------------------------------------------------------------------------
        | DEFAULT FALLBACK
        |--------------------------------------------------------------------------
        */

        else{

            header("Location: login.php?error=1");
            exit();
        }
    }
}

/* INVALID LOGIN */

header("Location: login.php?error=1");
exit();
?>