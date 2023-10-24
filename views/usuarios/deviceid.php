<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    header("location:../../logout.php");
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../../logout.php");
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    header("location:../../logout.php");
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_GET["id"])) {
    echo "<script> window.location.href='listarrev.php'; </script>";
    exit;
}
$userid = $_GET["id"];
if (isset($userid)) {
    $query = "DELETE FROM miracle_deviceid WHERE userid = " . $userid;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Não foi possível excluir os dados da tabela miracle_deviceid: " . mysqli_error($conexao));
    }
}
$_SESSION["excluidocomsucesso"] = "<div>Deviceid excluída com sucesso!</div>";
header("Location: listarusuarios.php");
exit;

?>