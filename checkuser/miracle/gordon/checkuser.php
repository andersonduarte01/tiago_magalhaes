<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header("content-type: application/json; charset=utf-8");
ini_set("error_reporting", 1);
include "../../../config/conexao.php";
session_start();
$dominio = $_SERVER["HTTP_HOST"];
$token = $token;
$url = "https://painelssh.com.br/api/api.php?token=" . $token . "&dominio=" . $dominio;
$response = ini_get("allow_url_fopen") ? @file_get_contents($url) : curl_exec(curl_init($url));
if ($response === false || ($response = json_decode($response, true)) === NULL || !isset($response["status"]) || $response["status"] !== "success") {
    header("Location: ../../../err.php" . (isset($response) ? "?mensagem=Resposta inválida da API" : ""));
    exit;
}
$getRequest = $_GET["request"];
$getRequeset = $_GET["requeset"];
$userid = $_GET["slot1"];
$device = $_GET["slot3"];
$passw = $_GET["slot2"];
try {
    $pdo = new PDO("mysql:host=" . $server . ";dbname=" . $db . ";charset=utf8", $user, $pass, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($getRequeset == "dcuser") {
        exit;
    }
    if (!empty($getRequest)) {
        return NULL;
    }
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["user"];
    $password = $data["password"];
    $deviceId = $data["deviceid"];
    $currentTime = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
    $stmt = $pdo->prepare("SELECT * FROM ssh_accounts WHERE login = ? AND senha = ?");
    $stmt->execute([$username, $password]);
    $sshAccount = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT * FROM miracle_deviceid WHERE userid = ? AND device = ?");
    $stmt->execute([$sshAccount[id], $deviceId]);
    $device = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM miracle_deviceid WHERE userid = ?");
    $stmt->execute([$sshAccount[id]]);
    $deviceCount = $stmt->fetch();
    if ($device[id] == NULL) {
        if ($sshAccount[limite] <= $deviceCount[0] && $sshAccount[id] != NULL) {
            $response = ["Status" => "blockdevice"];
            echo json_encode($response);
            exit;
        }
        if ($sshAccount[id] != NULL) {
            $stmt = $pdo->prepare("INSERT INTO miracle_deviceid (userid, device, byid) VALUES (?, ?, ?)");
            $stmt->execute([$sshAccount[id], $deviceId, $sshAccount[byid]]);
        }
    }
    if ($sshAccount[id] != NULL) {
        $startDate = new DateTime($currentTime);
        $timeRemaining = $startDate->diff(new DateTime($sshAccount[expira]));
        $months = $timeRemaining->m;
        $days = $timeRemaining->d;
        $hours = $timeRemaining->h;
        $minutes = $timeRemaining->i;
        $response = ["Status" => "searched", "Days" => (string) $days, "Hours" => (string) $hours, "Minutes" => (string) $minutes, "Months" => (string) $months, "Limit" => (string) $sshAccount["limite"]];
        echo json_encode($response);
        return NULL;
    }
    $stmt = $pdo->prepare("SELECT * FROM ssh_accounts WHERE login = ? AND senha = ?");
    $stmt->execute([$username, $password]);
    $existingAccount = $stmt->fetch();
    if ($existingAccount[id] != NULL) {
        $startDate = new DateTime($currentTime);
        $timeRemaining = $startDate->diff(new DateTime($existingAccount[expira]));
        $months = $timeRemaining->m;
        $days = $timeRemaining->d;
        $hours = $timeRemaining->h;
        $minutes = $timeRemaining->i;
        $response = ["Status" => "searched", "Days" => (string) $days, "Hours" => (string) $hours, "Minutes" => (string) $minutes, "Months" => (string) $months, "Limit" => (string) $existingAccount["limite"]];
        echo json_encode($response);
        return NULL;
    }
    $response = ["Status" => "notsearched"];
    echo json_encode($response);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>