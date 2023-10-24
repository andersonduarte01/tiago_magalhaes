<?php

session_start();
include "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    echo "<script> window.location.href='../logout.php'; </script>";
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '..logout.php'; </script>";
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    echo "<script> window.location.href = '../logout.php'; </script>";
    exit;
}
if (isset($_POST["id"])) {
    $id = $_POST["id"];
    $iduser = $_SESSION["iduser"];
    $sql = "DELETE FROM links WHERE id = " . $id . " AND byid = " . $iduser;
    if ($conexao->query($sql) !== true) {
    }
}

?>