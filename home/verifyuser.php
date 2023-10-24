<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../config/conexao.php";
require_once "../lib/vendor/autoload.php";
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '../logout.php'; </script>";
    exit;
}
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$sql_select_accounts_by_id = "SELECT * FROM accounts WHERE id = ?";
$stmt = $conexao->prepare($sql_select_accounts_by_id);
$stmt->bind_param("i", $_SESSION["byid"]);
$stmt->execute();
$result = $stmt->get_result();
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $access_token = $row["accesstoken"];
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
    if (isset($_SESSION["payment_id"])) {
        $sql_check_status = "SELECT status FROM pagamentos WHERE payment_id = ? AND status = 'APROVADO'";
        $stmt_check_status = $conexao->prepare($sql_check_status);
        $stmt_check_status->bind_param("s", $_SESSION["payment_id"]);
        $stmt_check_status->execute();
        $stmt_check_status->store_result();
        if (0 < $stmt_check_status->num_rows) {
            echo "O pagamento já foi aprovado.";
            exit;
        }
        if ($status->status == "approved") {
            echo "Aprovado";
            $sql_pagamentos = "UPDATE pagamentos SET status = 'APROVADO' WHERE payment_id = ?";
            $stmt_pagamentos = $conexao->prepare($sql_pagamentos);
            $stmt_pagamentos->bind_param("s", $_SESSION["payment_id"]);
            $stmt_pagamentos->execute();
            if (0 >= $stmt_pagamentos->affected_rows) {
            }
            $sql_select_ssh_accounts = "SELECT * FROM ssh_accounts WHERE id = ?";
            $stmt = $conexao->prepare($sql_select_ssh_accounts);
            $stmt->bind_param("i", $_SESSION["iduser"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $expira = $row["expira"];
            $agora = time();
            $expira_timestamp = strtotime($expira);
            $tempo_restante = $expira_timestamp - $agora;
            if (0 < $tempo_restante) {
                $expira_days_left = round($tempo_restante / 86400);
                $total = $expira_days_left + 30;
                $data_validade = date("Y-m-d H:i:s", strtotime("+" . $total . " days", $agora));
            } else {
                if ($tempo_restante < 0) {
                    $total = 30;
                    $data_validade = date("Y-m-d H:i:s", strtotime("+" . $total . " days", $agora));
                } else {
                    $total = 30 + $tempo_restante / 86400;
                    $data_validade = date("Y-m-d H:i:s", strtotime("+" . $total . " days", $agora));
                }
            }
            $sql_update = "UPDATE ssh_accounts SET expira = ? WHERE id = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("si", $data_validade, $_SESSION["iduser"]);
            $stmt_update->execute();
            if (0 >= $stmt_update->affected_rows) {
            }
            $limites = $row["limite"];
            if ($_SESSION["byid"] == 1) {
                $conexao->query($sql);
            } else {
                $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["byid"] . "'";
                $result_atribuidos = $conexao->query($sql_atribuidos);
                if (0 < $result_atribuidos->num_rows) {
                    $atribuido = $result_atribuidos->fetch_assoc();
                    if ($atribuido["tipo"] != "Validade") {
                        if ($atribuido["tipo"] == "Credito" && 1 <= $atribuido["limite"]) {
                            $limite_ssh = $atribuido["limite"] - 1;
                            $sql_update = "UPDATE atribuidos SET limite = ? WHERE userid = ?";
                            $stmt_update = $conexao->prepare($sql_update);
                            $stmt_update->bind_param("is", $limite_ssh, $_SESSION["byid"]);
                            $stmt_update->execute();
                            $stmt_update->close();
                        }
                    }
                }
            }
            $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
            $stmt_servidor = $conexao->prepare($sql_servidor);
            $stmt_servidor->execute();
            $result_servidor = $stmt_servidor->get_result();
            $servidores = [];
            while ($row = $result_servidor->fetch_assoc()) {
                $servidores[] = $row;
            }
            foreach ($servidores as $server) {
                $ssh = ssh2_connect($server["ip"], $server["porta"]);
                if ($ssh) {
                    if (ssh2_auth_password($ssh, $server["usuario"], $server["senha"])) {
                        $file = fopen("../home/modulos/CriarUsuarioSsh.sh", "w");
                        if (!$file) {
                            exit("Falha ao criar o arquivo CriarUsuarioSsh.sh");
                        }
                        $login = $_SESSION["login"];
                        $senha = $_SESSION["senha"];
                        $total = $total;
                        $limites = $limites;
                        $command = "./ExcluirExpiradoApi.sh " . $login . "\n./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $total . " " . $limites . "\n";
                        fwrite($file, $command);
                        fclose($file);
                        if (!ssh2_scp_send($ssh, "../home/modulos/CriarUsuarioSsh.sh", "CriarUsuarioSsh.sh", 493)) {
                            exit("Falha ao enviar o arquivo para o servidor");
                        }
                        $exec_command = "./CriarUsuarioSsh.sh >/dev/null 2>&1 &";
                        ssh2_exec($ssh, $exec_command);
                        ssh2_disconnect($ssh);
                    }
                }
            }
        }
    }
}

?>