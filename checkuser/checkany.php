<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";
try {
    $conexao = new PDO("mysql:host=" . $server . ";dbname=" . $db . ";charset=utf8", $user, $pass);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    function format_date($date_string)
    {
        $date = DateTime::createFromFormat("Y-m-d H:i:s", $date_string);
        return $date->format("Y-m-d");
    }
    function get_user($conexao, $username)
    {
        $stmt = $conexao->prepare("SELECT * FROM ssh_accounts WHERE login = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    function has_device($conexao, $userid)
    {
        $stmt = $conexao->prepare("SELECT * FROM miracle_deviceid WHERE userid = ?");
        $stmt->execute([$userid]);
        return 0 < $stmt->rowCount();
    }
    function is_device_limit_exceeded($conexao, $userid, $limit)
    {
        $stmt = $conexao->prepare("SELECT COUNT(*) as count FROM miracle_deviceid WHERE userid = ?");
        $stmt->execute([$userid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $limit <= $row["count"];
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $deviceid = $_POST["deviceid"];
        $user = get_user($conexao, $username);
        if (!$user) {
            $response = ["USER_ID" => $username, "DEVICE" => NULL, "is_active" => "false", "Status" => "naoencontrado", "uuid" => "null"];
        } else {
            $userid = $user["id"];
            $limite = $user["limite"];
            $hasDevice = has_device($conexao, $userid);
            $isDeviceLimitExceeded = is_device_limit_exceeded($conexao, $userid, $limite);
            if (!$hasDevice && !$isDeviceLimitExceeded) {
                $conexao->beginTransaction();
                try {
                    $stmt = $conexao->prepare("INSERT INTO miracle_deviceid (userid, byid, device) VALUES (?, ?, ?)");
                    $stmt->execute([$userid, $user["byid"], $deviceid]);
                    $conexao->commit();
                } catch (PDOException $e) {
                    $conexao->rollBack();
                    throw $e;
                }
            }
            $device = $isDeviceLimitExceeded ? NULL : $deviceid;
            $is_active = $isDeviceLimitExceeded ? "false" : "true";
            $response = ["USER_ID" => $username, "DEVICE" => $device, "is_active" => $is_active, "expiration_date" => format_date($user["expira"]), "expiry" => $user["expiry"] . " dias.", "uuid" => "null"];
        }
        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        http_response_code(404);
    }
} catch (PDOException $e) {
    header("Content-Type: application/json");
    echo json_encode(["error" => $e->getMessage()]);
}

?>