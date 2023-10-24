<?php

include "../../config/conexao.php";
session_start();
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    echo "<script> window.location.href='../../logout.php'; </script>";
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '../../logout.php'; </script>";
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    echo "<script> window.location.href = '../../logout.php'; </script>";
    exit;
}
if (isset($_POST["salvar"])) {
    $accesstoken = isset($_POST["accesstoken"]) ? $_POST["accesstoken"] : "1";
    $valorusuario = isset($_POST["valorusuario"]) ? $_POST["valorusuario"] : "1";
    $valorrevenda = isset($_POST["valorrevenda"]) ? $_POST["valorrevenda"] : "1";
    $sql = "UPDATE accounts SET accesstoken = '" . $accesstoken . "' WHERE id = '" . $_SESSION["iduser"] . "'";
    $sql2 = "UPDATE accounts SET valorusuario = '" . $valorusuario . "' WHERE id = '" . $_SESSION["iduser"] . "'";
    $sql3 = "UPDATE accounts SET valorrevenda = '" . $valorrevenda . "' WHERE id = '" . $_SESSION["iduser"] . "'";
    if ($conexao->query($sql) !== true) {
    }
    if ($conexao->query($sql2) !== true) {
    }
    if ($conexao->query($sql3) !== true) {
    }
    $_SESSION["link3"] = "<div>Pagamento configurado com Sucesso!</div>";
    echo "<script> window.location.href='../../nivel.php'; </script>";
}

?>