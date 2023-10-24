<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../../config/conexao.php";
$dominio = $_SERVER["HTTP_HOST"];
$token = $token;
$url = "https://painelssh.com.br/api/api.php?token=" . $token . "&dominio=" . $dominio;
$response = ini_get("allow_url_fopen") ? @file_get_contents($url) : curl_exec(curl_init($url));
if ($response === false || ($response = json_decode($response, true)) === NULL || !isset($response["status"]) || $response["status"] !== "success") {
    header("Location: ../../../err.php" . (isset($response) ? "?mensagem=Resposta inválida da API" : ""));
    exit;
}
date_default_timezone_set("America/Sao_Paulo");
define("DB_SERVER", $server);
define("DB_USERNAME", $user);
define("DB_PASSWORD", $pass);
define("DB_NAME", $db);
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->exec("set names utf8");
} catch (PDOException $e) {
    exit("Falha ao conectar no banco de dados.");
}

?>