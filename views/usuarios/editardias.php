<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!extension_loaded("ssh2")) {
    $_SESSION["error"] = "A extensão SSH2 não está instalada. Verifique sua configuração do PHP.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    echo "<script> window.location.href='../../logout.php'; </script>";
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '../../logout.php'; </script>";
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    echo "<script> window.location.href = '../../logout.php'; </script>";
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["id"])) {
    $id = $_GET["id"];
    $limite = $_GET["limite"];
    $dias = $_GET["dias"];
    $iduser = $_SESSION["iduser"];
}
$data_expiracao = date("Y-m-d H:i:s", strtotime("+" . $dias . " days"));
$stmt = mysqli_prepare($conexao, "SELECT login, senha, byid, mainid, categoriaid FROM ssh_accounts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $login, $senha, $byid, $mainid, $categoriaid);
do {
} while (!mysqli_stmt_fetch($stmt));
mysqli_stmt_close($stmt);
$resultado_atribuidos = mysqli_query($conexao, "SELECT tipo FROM atribuidos WHERE userid = '" . $iduser . "'");
$atribuido = mysqli_fetch_assoc($resultado_atribuidos);
$resultado = mysqli_query($conexao, "SELECT maxcredit FROM config WHERE byid = '1'");
$minutos_maximos = mysqli_fetch_assoc($resultado)["maxcredit"];
$dias_minimos = 1;
$dias_maximos = $minutos_maximos;
if ($dias === "") {
    $_SESSION["error"] = "Por favor, insira um valor de dias.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
if ($atribuido["tipo"] == "Credito" && ($dias < $dias_minimos || $dias_maximos < $dias)) {
    if ($dias < $dias_minimos) {
        $_SESSION["error"] = "Limite mínimo de dias não é válido. Por favor, insira um valor mínimo de 1 dia.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($dias_maximos < $dias) {
        $_SESSION["error"] = "Limite máximo de dias excedido. Por favor, insira um valor até " . $minutos_maximos . " dias.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
}
if ($iduser != 1) {
    $query = "SELECT categoriaid, suspenso, tipo, expira FROM atribuidos WHERE userid = ?";
    $stmt = mysqli_prepare($conexao, $query);
    mysqli_stmt_bind_param($stmt, "s", $iduser);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $categoriaEncontrada = false;
    while ($atribuido = mysqli_fetch_assoc($resultado)) {
        if ($atribuido["categoriaid"] == $categoriaid) {
            $categoriaEncontrada = true;
            if ($atribuido["suspenso"] == 1) {
                $_SESSION["error"] = "A atribuição está suspensa temporariamente e não é possível editar essa conta. Por favor, entre em contato com o administrador para obter mais informações e resolver essa questão.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if ($atribuido["tipo"] == "Validade" && strtotime($atribuido["expira"]) < time()) {
                $_SESSION["error"] = "A atribuição está vencida. Por favor, entre em contato com o administrador para renová-la.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
        }
    }
    if (!$categoriaEncontrada) {
        $_SESSION["error"] = "A categoria selecionada não corresponde à atribuição atual.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
}
if ($iduser != 1) {
    $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = ? AND categoriaid = ?";
    $stmt_atribuidos = $conexao->prepare($sql_atribuidos);
    $stmt_atribuidos->bind_param("ii", $iduser, $categoriaid);
    $stmt_atribuidos->execute();
    $result_atribuidos = $stmt_atribuidos->get_result();
    if (0 < $result_atribuidos->num_rows) {
        $atribuido = $result_atribuidos->fetch_assoc();
        if ($atribuido["tipo"] == "Validade") {
            $sql_limite = "SELECT limite FROM atribuidos WHERE byid = ?";
            $stmt_limite = $conexao->prepare($sql_limite);
            $stmt_limite->bind_param("i", $iduser);
            $stmt_limite->execute();
            $result_limite = $stmt_limite->get_result();
            if (0 < $result_limite->num_rows) {
                $limite_atribuidos = $result_limite->fetch_assoc()["limite"];
                $sql_usuarios = "SELECT SUM(limite) AS total_limite FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
                $stmt_usuarios = $conexao->prepare($sql_usuarios);
                $stmt_usuarios->bind_param("ii", $iduser, $categoriaid);
                $stmt_usuarios->execute();
                $result_usuarios = $stmt_usuarios->get_result();
                $usuarios_criados = $result_usuarios->fetch_assoc()["total_limite"];
                $total_limite = $usuarios_criados + $limite_atribuidos;
                if ($atribuido["limite"] < $total_limite) {
                    $_SESSION["error"] = "Limite de usuários excedido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($atribuido["limite"] < $total_limite + $limite && $limite != $atribuido["limite"]) {
                    $_SESSION["error"] = "O limite fornecido excede o limite permitido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($limite_atribuidos < $limite) {
                    $_SESSION["error"] = "O limite fornecido excede o limite de crédito disponível para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            } else {
                $sql_usuarios = "SELECT SUM(limite) AS total_limite FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
                $stmt_usuarios = $conexao->prepare($sql_usuarios);
                $stmt_usuarios->bind_param("ii", $iduser, $categoriaid);
                $stmt_usuarios->execute();
                $result_usuarios = $stmt_usuarios->get_result();
                $usuarios_criados = $result_usuarios->fetch_assoc()["total_limite"];
                if ($atribuido["limite"] < $total_limite) {
                    $_SESSION["error"] = "Limite de usuários excedido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($atribuido["limite"] < $total_limite + $limite && $limite != $atribuido["limite"]) {
                    $_SESSION["error"] = "O limite fornecido excede o limite permitido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            }
        } else {
            if ($atribuido["tipo"] == "Credito") {
                if (1 <= $atribuido["limite"]) {
                    $limite_ssh = $atribuido["limite"] - 1;
                    if ($atribuido["limite"] < $limite) {
                        $_SESSION["error"] = "O limite fornecido excede o limite de créditos disponíveis para a categoria selecionada.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                    $sql_update = "UPDATE atribuidos SET limite = ? WHERE userid = ? AND categoriaid = ?";
                    $stmt_update = $conexao->prepare($sql_update);
                    $stmt_update->bind_param("iii", $limite_ssh, $iduser, $categoriaid);
                    $stmt_update->execute();
                    $stmt_update->close();
                } else {
                    $_SESSION["error"] = "Usuário possui apenas " . $atribuido["limite"] . " crédito(s) disponível(is) para criar esse acesso.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            }
        }
    }
}
if ($limite == 0) {
    $sql_limite = "SELECT limite FROM ssh_accounts WHERE login = ?";
    $stmt_limite = $conexao->prepare($sql_limite);
    $stmt_limite->bind_param("s", $login);
    $stmt_limite->execute();
    $result_limite = $stmt_limite->get_result();
    if (0 < $result_limite->num_rows) {
        $limite = $result_limite->fetch_assoc()["limite"];
    }
}
if ($iduser == 1) {
    $dias1 = $dias;
    $dias1 += 1;
    $file = fopen("../../home/modulos/EditarDiasLimite.sh", "w");
    $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias1 . " " . $limite . "\n";
    fwrite($file, $command);
    fclose($file);
    $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
    $resultadoServidores = $conexao->query($sqlServidores);
    if (0 < $resultadoServidores->num_rows) {
        $errors = [];
        while ($row = $resultadoServidores->fetch_assoc()) {
            if ($row["subid"] == $categoriaid) {
                try {
                    $connection = ssh2_connect($row["ip"], $row["porta"]);
                    if (!$connection) {
                        $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                    } else {
                        if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                            $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                            ssh2_disconnect($connection);
                        } else {
                            if (!function_exists("ssh2_scp_send")) {
                                $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                                ssh2_disconnect($connection);
                            } else {
                                if (!ssh2_scp_send($connection, "../../home/modulos/EditarDiasLimite.sh", "EditarDiasLimite.sh", 493)) {
                                    $errors[] = "Falha ao enviar o arquivo para o servidor";
                                    ssh2_disconnect($connection);
                                } else {
                                    $exec_command = "./EditarDiasLimite.sh >/dev/null 2>&1 &";
                                    ssh2_exec($connection, $exec_command);
                                    ssh2_disconnect($connection);
                                }
                            }
                        }
                    }
                } catch (ErrorException $e) {
                    $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
                }
            }
        }
        if (!empty($errors)) {
            $_SESSION["error"] = implode("<br>", $errors);
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    $stmt = $conexao->prepare("UPDATE ssh_accounts SET senha=?, limite=?, expira=?, byid=?, mainid=?, categoriaid=?, login=? WHERE id=?");
    $stmt->bind_param("sisiiisi", $senha, $limite, $data_expiracao, $iduser, $mainid, $categoriaid, $login, $id);
    $stmt->execute();
    $stmt = $conexao->prepare("SELECT * FROM ssh_accounts WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $now = new DateTime();
    $expira = new DateTime($row["expira"]);
    $interval = $now->diff($expira);
    $dias = $interval->format("%a");
    $dias += 1;
    $_SESSION["resposta"] = ["status" => "Updated", "id" => $row["id"], "limite" => $row["limite"], "dias" => $dias, "login" => $row["login"], "senha" => $row["senha"]];
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
$stmt_select_tipo = $conexao->prepare("SELECT tipo FROM atribuidos WHERE userid=?");
$stmt_select_tipo->bind_param("i", $iduser);
$stmt_select_tipo->execute();
$result_tipo = $stmt_select_tipo->get_result();
if (0 < $result_tipo->num_rows) {
    $row_tipo = $result_tipo->fetch_assoc();
    $tipo = $row_tipo["tipo"];
    if ($tipo === "Credito") {
        $stmt_select = $conexao->prepare("SELECT expira FROM ssh_accounts WHERE login=?");
        $stmt_select->bind_param("s", $login);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if (0 < $result->num_rows) {
            $row = $result->fetch_assoc();
            $data_expiracao_atual = $row["expira"];
            $data_expiracao_atual_obj = new DateTime($data_expiracao_atual);
            $data_expiracao_obj = new DateTime($data_expiracao_atual);
            $data_expiracao_obj->add(new DateInterval("P" . $dias . "D"));
            $data_expiracao = $data_expiracao_obj->format("Y-m-d H:i:s");
            $data_atual = new DateTime();
            $interval = $data_atual->diff($data_expiracao_obj);
            $dias = $interval->days;
            $dias1 = $dias;
            $dias1 += 1;
            $file = fopen("../../home/modulos/EditarDiasLimite.sh", "w");
            $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias1 . " " . $limite . "\n";
            fwrite($file, $command);
            fclose($file);
            $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
            $resultadoServidores = $conexao->query($sqlServidores);
            if (0 < $resultadoServidores->num_rows) {
                $errors = [];
                while ($row = $resultadoServidores->fetch_assoc()) {
                    if ($row["subid"] == $categoriaid) {
                        try {
                            $connection = ssh2_connect($row["ip"], $row["porta"]);
                            if (!$connection) {
                                $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                            } else {
                                if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                                    $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                                    ssh2_disconnect($connection);
                                } else {
                                    if (!function_exists("ssh2_scp_send")) {
                                        $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                                        ssh2_disconnect($connection);
                                    } else {
                                        if (!ssh2_scp_send($connection, "../../home/modulos/EditarDiasLimite.sh", "EditarDiasLimite.sh", 493)) {
                                            $errors[] = "Falha ao enviar o arquivo para o servidor";
                                            ssh2_disconnect($connection);
                                        } else {
                                            $exec_command = "./EditarDiasLimite.sh >/dev/null 2>&1 &";
                                            ssh2_exec($connection, $exec_command);
                                            ssh2_disconnect($connection);
                                        }
                                    }
                                }
                            }
                        } catch (ErrorException $e) {
                            $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
                        }
                    }
                }
                if (!empty($errors)) {
                    $_SESSION["error"] = implode("<br>", $errors);
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            }
            $stmt = $conexao->prepare("UPDATE ssh_accounts SET senha=?, limite=?, expira=?, byid=?, mainid=?, categoriaid=?, login=? WHERE id=?");
            $stmt->bind_param("sisiiisi", $senha, $limite, $data_expiracao, $iduser, $mainid, $categoriaid, $login, $id);
            $stmt->execute();
            $stmt = $conexao->prepare("SELECT * FROM ssh_accounts WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $now = new DateTime();
            $expira = new DateTime($row["expira"]);
            $interval = $now->diff($expira);
            $dias = $interval->format("%a");
            $dias += 1;
            $_SESSION["resposta"] = ["status" => "Updated", "id" => $row["id"], "limite" => $row["limite"], "dias" => $dias, "login" => $row["login"], "senha" => $row["senha"]];
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
        echo "Login não encontrado na tabela 'ssh_accounts'.";
        exit;
    }
    if ($tipo === "Validade") {
        $dias1 = $dias;
        $dias1 += 1;
        $file = fopen("../../home/modulos/EditarDiasLimite.sh", "w");
        $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias1 . " " . $limite . "\n";
        fwrite($file, $command);
        fclose($file);
        $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
        $resultadoServidores = $conexao->query($sqlServidores);
        if (0 < $resultadoServidores->num_rows) {
            $errors = [];
            while ($row = $resultadoServidores->fetch_assoc()) {
                if ($row["subid"] == $categoriaid) {
                    try {
                        $connection = ssh2_connect($row["ip"], $row["porta"]);
                        if (!$connection) {
                            $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                        } else {
                            if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                                $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                                ssh2_disconnect($connection);
                            } else {
                                if (!function_exists("ssh2_scp_send")) {
                                    $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                                    ssh2_disconnect($connection);
                                } else {
                                    if (!ssh2_scp_send($connection, "../../home/modulos/EditarDiasLimite.sh", "EditarDiasLimite.sh", 493)) {
                                        $errors[] = "Falha ao enviar o arquivo para o servidor";
                                        ssh2_disconnect($connection);
                                    } else {
                                        $exec_command = "./EditarDiasLimite.sh >/dev/null 2>&1 &";
                                        ssh2_exec($connection, $exec_command);
                                        ssh2_disconnect($connection);
                                    }
                                }
                            }
                        }
                    } catch (ErrorException $e) {
                        $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
                    }
                }
            }
            if (!empty($errors)) {
                $_SESSION["error"] = implode("<br>", $errors);
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
        }
        $stmt = $conexao->prepare("UPDATE ssh_accounts SET senha=?, limite=?, expira=?, byid=?, mainid=?, categoriaid=?, login=? WHERE id=?");
        $stmt->bind_param("sisiiisi", $senha, $limite, $data_expiracao, $iduser, $mainid, $categoriaid, $login, $id);
        $stmt->execute();
        $stmt = $conexao->prepare("SELECT * FROM ssh_accounts WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $now = new DateTime();
        $expira = new DateTime($row["expira"]);
        $interval = $now->diff($expira);
        $dias = $interval->format("%a");
        $dias += 1;
        $_SESSION["resposta"] = ["status" => "Updated", "id" => $row["id"], "limite" => $row["limite"], "dias" => $dias, "login" => $row["login"], "senha" => $row["senha"]];
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    echo "Login não encontrado na tabela 'ssh_accounts'.";
    exit;
}
echo "\r\n";

?>