<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/* DATABASE CONNECTION */
require_once dirname(__DIR__) . "/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

/* FETCH FORM DATA */

$userid   = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if($userid == "" || $password == ""){
    header("Location: login.php?error=empty");
    exit();
}

/* CHECK DATABASE CONNECTION */

if(!$conn){
    die("Database connection failed");
}

/* FETCH USER FROM DATABASE */

$sql = "SELECT username, password, designation, full_name, role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if(!$stmt){
    die("SQL Error: ".$conn->error);
}

$stmt->bind_param("s", $userid);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 1){

    $user = $result->fetch_assoc();

    /* ✅ PASSWORD CHECK (FIXED) */
    if(password_verify($password, $user['password'])){

        /* AUTO ROLE FROM DESIGNATION */

        $designation = strtoupper(trim($user['designation']));
        $user_role   = strtoupper(trim($user['role']));

        if($designation == "ASSTT. ACCOUNTS OFFICER"){
            $role = "Section Officer";
        }
        elseif($designation == "SR. ACCOUNTS OFFICER"){
            $role = "Branch Officer";
        }
        elseif($user_role == "ITSC"){
            $role = "ITSC";
        }
        else{
            $role = "User";
        }

        /* SESSION VARIABLES */

        $_SESSION['userid'] = $user['username'];
        $_SESSION['name']   = $user['full_name'];
        $_SESSION['role']   = $role;

        /* REDIRECT */

        if($role == "Section Officer"){
            header("Location: ../Dashboards/section_officer.php");
        }
        elseif($role == "Branch Officer"){
            header("Location: ../Dashboards/bo_home.php");
        }
        elseif($role == "ITSC"){
            header("Location: ../Dashboards/itsc_dashboard.php");
        }
        else{
            header("Location: ../Dashboards/user_dashboard.php");
        }

                exit();
    }
}

/* INVALID LOGIN */

$_SESSION['login_error'] = "Incorrect Username or Password";

header("Location: login.php");

exit();

?>