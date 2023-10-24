<?php

session_start();
require_once "../../lib/vendor/autoload.php";
require_once "../../config/conexao.php";
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../../logout.php");
    exit;
}
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (isset($_SESSION["byid"])) {
    $byid = mysqli_real_escape_string($conexao, $_SESSION["byid"]);
    $sql4 = "SELECT * FROM accounts WHERE id = '" . $byid . "'";
    $result4 = $conexao->query($sql4);
    if (0 < $result4->num_rows) {
        while ($row4 = $result4->fetch_assoc()) {
            $access_token = $row4["accesstoken"];
        }
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
    if ($result === false) {
        echo "Erro na requisição HTTP: " . curl_error($ch);
        exit;
    }
    curl_close($ch);
    $status = json_decode($result);
    if ($status === NULL) {
        echo "Erro na decodificação JSON: " . json_last_error_msg();
        exit;
    }
    if (isset($_SESSION["payment_id"])) {
        $sql_check_status = "SELECT status FROM pagamentos WHERE payment_id = ? AND status = 'APROVADO'";
        $stmt_check_status = $conexao->prepare($sql_check_status);
        $stmt_check_status->bind_param("s", $_SESSION["payment_id"]);
        $stmt_check_status->execute();
        $stmt_check_status->store_result();
        if (0 < $stmt_check_status->num_rows) {
            exit;
        }
        if ($status->status == "approved") {
            echo "Aprovado";
            $query_verifica_suspenso = "SELECT id, suspenso, categoriaid FROM atribuidos WHERE userid = ?";
            $stmt_verifica_suspenso = $conexao->prepare($query_verifica_suspenso);
            $stmt_verifica_suspenso->bind_param("i", $_SESSION["iduser"]);
            $stmt_verifica_suspenso->execute();
            $stmt_verifica_suspenso->store_result();
            if (0 < $stmt_verifica_suspenso->num_rows) {
                $stmt_verifica_suspenso->bind_result($id, $suspenso, $categoriaid);
                $stmt_verifica_suspenso->fetch();
                if ($suspenso == 1) {
                    $query_atualiza_suspenso = "UPDATE atribuidos SET suspenso = 0 WHERE userid = ?";
                    $stmt_atualiza_suspenso = $conexao->prepare($query_atualiza_suspenso);
                    $stmt_atualiza_suspenso->bind_param("i", $_SESSION["iduser"]);
                    $stmt_atualiza_suspenso->execute();
                }
            }
            $sql = "SELECT login, senha, limite, expira FROM ssh_accounts WHERE byid = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("i", $_SESSION["iduser"]);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $user_list = "";
            while ($row = $resultado->fetch_assoc()) {
                $expira = new DateTime($row["expira"]);
                $dataAtual = new DateTime();
                if ($expira >= $dataAtual) {
                    $diasRestantes = $dataAtual->diff($expira)->days;
                    $login = $row["login"];
                    $senha = $row["senha"];
                    $limite = $row["limite"];
                    $user_list .= $login . "|" . $senha . "|" . $limite . "|" . $diasRestantes . " ";
                }
            }
            $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores WHERE subid = ?";
            $stmt_servidor = $conexao->prepare($sql_servidor);
            $stmt_servidor->bind_param("i", $categoriaid);
            $stmt_servidor->execute();
            $result_servidor = $stmt_servidor->get_result();
            $servidores = [];
            while ($row = $result_servidor->fetch_assoc()) {
                $servidores[] = $row;
            }
            $tasks = [];
            foreach ($servidores as $server) {
                if (isset($server["ip"]) && isset($server["porta"]) && isset($server["usuario"]) && isset($server["senha"])) {
                    $tasks[] = function () use($server, $user_list) {
                        $connection = ssh2_connect($server["ip"], $server["porta"]);
                        if (!$connection) {
                            echo "Falha ao conectar ao servidor SSH2: " . $server["ip"];
                        } else {
                            $login_result = ssh2_auth_password($connection, $server["usuario"], $server["senha"]);
                            if (!$login_result) {
                                echo "Falha na autenticação SSH2 para o servidor: " . $server["ip"];
                            } else {
                                $usuarios = explode(" ", $user_list);
                                foreach ($usuarios as $usuario) {
                                    $dados = explode("|", $usuario);
                                    list($login, $senha, $limite, $diasRestantes) = $dados;
                                    $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $diasRestantes . " " . $limite;
                                    $stream = ssh2_exec($connection, $command);
                                    stream_set_blocking($stream, true);
                                    $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                                    $output = stream_get_contents($stream_out);
                                    fclose($stream);
                                    if (strpos($output, "Error:") !== false) {
                                        echo "Erro ao executar o comando SSH no servidor: " . $server["ip"] . " - " . $output;
                                    }
                                }
                                return NULL;
                            }
                        }
                    };
                }
            }
            foreach ($tasks as $task) {
                $task();
            }
            $sql = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["iduser"] . "'";
            $result = $conexao->query($sql);
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
            $conexao->begin_transaction();
            $sql = "UPDATE atribuidos SET expira = ? WHERE userid = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $data_validade, $_SESSION["iduser"]);
            $result = $stmt->execute();
            if ($result === false) {
                echo "Erro na atualização da data de expiração: " . $conexao->error;
                $conexao->rollback();
                exit;
            }
            $sql_pagamentos = "UPDATE pagamentos SET status = 'APROVADO' WHERE payment_id = ?";
            $stmt_pagamentos = $conexao->prepare($sql_pagamentos);
            $stmt_pagamentos->bind_param("s", $_SESSION["payment_id"]);
            $result_pagamentos = $stmt_pagamentos->execute();
            if ($result_pagamentos === false) {
                echo "Erro na atualização do status do pagamento: " . $conexao->error;
                $conexao->rollback();
                exit;
            }
        }
    }
}
$conexao->commit();
$conexao->close();

?>