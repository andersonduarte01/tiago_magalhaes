<?php

session_start();
include "../../config/conexao.php";
require_once "../../lib/vendor/autoload.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!(isset($_SESSION["login"]) && isset($_SESSION["senha"]))) {
    header("Location: ../../logout.php");
    exit;
}
$_SESSION["LAST_ACTIVITY"] = time();
$addquantidade = $_POST["addquantidade"];
$_SESSION["addquantidade"] = $addquantidade;
$sql4 = "SELECT valorrevenda FROM accounts WHERE id = ?";
$stmt4 = $conexao->prepare($sql4);
$stmt4->bind_param("i", $_SESSION["byid"]);
$stmt4->execute();
$result4 = $stmt4->get_result();
if ($result4->num_rows === 0) {
    echo "<script>alert(\"Seu Revendedor Não está cadastrado em nossa Plataforma\");</script><script>window.location.href = \"../../index.php\";</script>";
    exit;
}
$row4 = $result4->fetch_assoc();
$valor_login = $row4["valorrevenda"];
$valor_final = $valor_login;
if ($valor_final < 0) {
    echo "<script>alert(\"Valor inserido excede o valor do login.\");</script><script>window.location.href = \"../../index.php\";</script>";
    exit;
}
$valor_add = $valor_final * $addquantidade;
$_SESSION["valoradd"] = $valor_add;
$valor_add = $_SESSION["valoradd"];
$sql = "SELECT accesstoken FROM accounts WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $_SESSION["byid"]);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<script>alert(\"Seu access_token não foi encontrado.\");</script><script>window.location.href = \"../../index.php\";</script>";
    exit;
}
$row = $result->fetch_assoc();
$access_token = $row["accesstoken"];
MercadoPago\SDK::setAccessToken($access_token);
$payment = new MercadoPago\Payment();
$payment->transaction_amount = $valor_add;
$payment->description = "Painel de Renoção";
$payment->payment_method_id = "pix";
$payment->payer = ["email" => $_POST["email"], "first_name" => $_POST["nome"], "last_name" => $_POST["sobrenome"], "identification" => ["type" => "CPF", "number" => $_POST["cpf"]], "address" => ["zip_code" => "06233200", "street_name" => "Av. das Nações Unidas", "street_number" => "3003", "neighborhood" => "Bonfim", "city" => "Osasco", "federal_unit" => "SP"]];
$payment->save();
date_default_timezone_set("America/Sao_Paulo");
$data_pagamento = date("Y-m-d H:i:s");
$byid = $_SESSION["byid"];
$iduser = $_SESSION["iduser"];
$payment_id = $payment->id;
$sql = "INSERT INTO pagamentos (login, valor, data_pagamento, byid, iduser, status, payment_id) VALUES ('" . $_SESSION["login"] . "', '" . $valor_add . "', '" . $data_pagamento . "', '" . $byid . "', '" . $iduser . "', 'Pendente', '" . $payment_id . "')";
$conexao->query($sql);
$_SESSION["payment_id"] = $payment->id;
$_SESSION["qr_code_base64"] = $payment->point_of_interaction->transaction_data->qr_code_base64;
$_SESSION["qr_code"] = $payment->point_of_interaction->transaction_data->qr_code;
echo "<script>window.location = 'renovarr.php'</script>";

?>