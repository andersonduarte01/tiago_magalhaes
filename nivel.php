<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href='./logout.php'; </script>";
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    echo "<script> window.location.href='./logout.php'; </script>";
    exit;
}
if ($_SESSION["nivel"] == 3) {
    echo "<script> window.location.href='./home/admin.php'; </script>";
} else {
    if ($_SESSION["nivel"] == 2) {
        echo "<script> window.location.href='./home/admin.php'; </script>";
    } else {
        if ($_SESSION["nivel"] == 1) {
            echo "<script> window.location.href='./home/user.php'; </script>";
        } else {
            echo "<script> window.location.href='index.php'; </script>";
        }
    }
}

?>