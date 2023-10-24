<?php

session_start();
include "../../config/conexao.php";
require_once "../../lib/vendor/autoload.php";
if (isset($_POST["email"]) && isset($_POST["nome"]) && isset($_POST["sobrenome"]) && isset($_POST["cpf"])) {
    $valor = $_SESSION["valor"];
    $conexao = mysqli_connect($server, $user, $pass, $db);
    if ($conexao->connect_error) {
        exit("Connection failed: " . $conexao->connect_error);
    }
    $sql = "SELECT * FROM accounts WHERE id = '" . $_SESSION["byid"] . "'";
    $result = $conexao->query($sql);
    if (0 < $result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $access_token = $row["accesstoken"];
        }
    }
    MercadoPago\SDK::setAccessToken($access_token);
    $payment = new MercadoPago\Payment();
    $payment->transaction_amount = $valor;
    $payment->description = "Painel de Renovação";
    $payment->payment_method_id = "pix";
    $payment->payer = ["email" => $_POST["email"], "first_name" => $_POST["nome"], "last_name" => $_POST["sobrenome"], "identification" => ["type" => "CPF", "number" => $_POST["cpf"]], "address" => ["zip_code" => "06233200", "street_name" => "Av. das Nações Unidas", "street_number" => "3003", "neighborhood" => "Bonfim", "city" => "Osasco", "federal_unit" => "SP"]];
    $payment->save();
    date_default_timezone_set("America/Sao_Paulo");
    $data_pagamento = date("Y-m-d H:i:s");
    $byid = $_SESSION["byid"];
    $iduser = $_SESSION["iduser"];
    $payment_id = $payment->id;
    $sql = "INSERT INTO pagamentos (login, valor, data_pagamento, byid, iduser, status, payment_id) VALUES ('" . $_SESSION["login"] . "', '" . $valor . "', '" . $data_pagamento . "', '" . $byid . "', '" . $iduser . "', 'Pendente', '" . $payment_id . "')";
    $conexao->query($sql);
    $_SESSION["payment_id"] = $payment->id;
    $_SESSION["qr_code_base64"] = $payment->point_of_interaction->transaction_data->qr_code_base64;
    $_SESSION["qr_code"] = $payment->point_of_interaction->transaction_data->qr_code;
    echo "<script>window.location = ('renovar.php')</script>";
} else {
    echo "Erro: dados do POST incompletos.";
}

?>