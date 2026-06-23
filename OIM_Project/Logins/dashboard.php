<?php

session_start();
include "db_connect.php";

// Strict session check
if (!isset($_SESSION['userid'], $_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['role']) {
    case "Section Officer":
        header("Location: section_officer.php");
        break;
    case "Branch Officer":
        header("Location: bo_home.php");
        break;
    case "ITSC":
        header("Location: itsc.php"); // Ensure this file is created next
        break;
    default:
        session_unset();
        session_destroy();
        header("Location: login.php?error=unauthorized");
}
exit();