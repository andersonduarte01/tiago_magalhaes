<?php

session_start();
require_once "../../lib/vendor/autoload.php";
require_once "../../config/conexao.php";
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '../../logout.php'; </script>";
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    echo "<script> window.location.href = '../../logout.php'; </script>";
    exit;
}
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$sql4 = "SELECT * FROM accounts WHERE id = '" . $_SESSION["byid"] . "'";
$result4 = $conexao->query($sql4);
if (0 < $result4->num_rows) {
    while ($row4 = $result4->fetch_assoc()) {
        $access_token = $row4["accesstoken"];
    }
}
if (isset($_SESSION["payment_id"])) {
    $url = "https://api.mercadopago.com/v1/payments/" . $_SESSION["payment_id"];
    $token = $access_token;
    $header = ["Authorization: Bearer " . $token, "Content-Type: application/json"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $result = curl_exec($ch);
    curl_close($ch);
    $status = json_decode($result);
    $addquantidade = $_SESSION["addquantidade"];
    $limite = $_SESSION["limite"];
    $soma = $addquantidade + $limite;
    $iduser = $_SESSION["iduser"];
    $status = json_decode($result);
    if ($status->status == "approved") {
        echo "Aprovado";
        if (isset($_SESSION["payment_id"])) {
            $sql_check_status = "SELECT status FROM pagamentos WHERE payment_id = ? AND status = 'APROVADO'";
            $stmt_check_status = $conexao->prepare($sql_check_status);
            $stmt_check_status->bind_param("s", $_SESSION["payment_id"]);
            $stmt_check_status->execute();
            $stmt_check_status->store_result();
            if (0 < $stmt_check_status->num_rows) {
                exit;
            }
            $sql_pagamentos = "UPDATE pagamentos SET status = 'APROVADO' WHERE payment_id = '" . $_SESSION["payment_id"] . "'";
            $conexao->query($sql_pagamentos);
            $sql = "UPDATE atribuidos SET limite = '" . $soma . "' WHERE userid = '" . $iduser . "'";
            if ($conexao->query($sql) !== true) {
                echo "Erro: " . $sql . "<br>" . $conexao->error;
            }
        } else {
            echo "Reprovado";
        }
    }
}

?>