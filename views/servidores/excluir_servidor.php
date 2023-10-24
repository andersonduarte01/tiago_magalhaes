<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../config/conexao.php";
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
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "DELETE FROM servidores WHERE id = " . $id;
    mysqli_query($conexao, $sql);
    $_SESSION["servdor1"] = "<div>Servidor excluído com sucesso!</div>";
    header("Location: listar.servidor.php");
    exit;
}
header("Location: listar.servidor.php");
exit;

?>