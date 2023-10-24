<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "config/conexao.php";
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    session_unset();
    session_destroy();
    echo "<script> window.location.href='logout.php'; </script>";
    exit;
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (isset($_POST["login"]) && isset($_POST["senha"])) {
    $login = $_POST["login"];
    $senha = $_POST["senha"];
    if (isset($_SESSION["login"])) {
        session_unset();
        session_destroy();
    }
    $stmt = $conexao->prepare("SELECT login, senha, id, byid, mainid, nivel FROM accounts WHERE login = ? AND senha = ? AND (nivel = 2 OR nivel = 3)");
    $stmt->bind_param("ss", $login, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
    if (0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        $_SESSION["login"] = $row["login"];
        $_SESSION["senha"] = $row["senha"];
        $_SESSION["iduser"] = $row["id"];
        $_SESSION["byid"] = $row["byid"];
        $_SESSION["mainid"] = $row["mainid"];
        $_SESSION["nivel"] = $row["nivel"];
        $location = $_SESSION["nivel"] == 3 ? "nivel.php" : "nivel.php";
        header("Location: " . $location);
        $iduser = $_SESSION["iduser"];
    }
    $stmt2 = $conexao->prepare("SELECT login, senha, id, byid, mainid, nivel FROM ssh_accounts WHERE login = ? AND senha = ? AND nivel = 1");
    $stmt2->bind_param("ss", $login, $senha);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if (0 < $result2->num_rows) {
        $row = $result2->fetch_assoc();
        $_SESSION["login"] = $row["login"];
        $_SESSION["senha"] = $row["senha"];
        $_SESSION["iduser"] = $row["id"];
        $_SESSION["byid"] = $row["byid"];
        $_SESSION["mainid"] = $row["mainid"];
        $_SESSION["nivel"] = $row["nivel"];
        header("Location: nivel.php");
    } else {
        $_SESSION["loginerro"] = "<div>Usuário ou senha incorreto!</div>";
        echo "<script> window.location.href='index.php'; </script>";
    }
}

?>