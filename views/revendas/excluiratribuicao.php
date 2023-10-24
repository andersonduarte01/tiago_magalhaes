<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    header("location:../../logout.php");
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../../logout.php");
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    header("location:../../logout.php");
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_GET["id"]) || !isset($_GET["userid"]) || !isset($_GET["categoriaid"])) {
    header("Location: listarrev.php");
    exit;
}
$userid = $_GET["id"];
$user_id = $_GET["userid"];
$categoriaid1 = $_GET["categoriaid"];
$user_id = $_GET["userid"];
if ($_SESSION["iduser"] == 1) {
    $sql = "SELECT * FROM ssh_accounts WHERE byid = " . $user_id;
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $user_list = "";
    $usersToDelete = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $user_list .= "./ExcluirExpiradoApi.sh " . $row["login"] . PHP_EOL;
        $usersToDelete[] = $row["login"];
    }
    $file = fopen("../../home/modulos/ExcluirAtribuicao.sh", "w");
    $command = $user_list . "\n";
    fwrite($file, $command);
    fclose($file);
    $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
    $stmt_servidor = $conexao->prepare($sql_servidor);
    $stmt_servidor->execute();
    $result_servidor = $stmt_servidor->get_result();
    $servidores = [];
    while ($row = $result_servidor->fetch_assoc()) {
        $servidores[] = $row;
    }
    $tasks = [];
    foreach ($servidores as $server) {
        $tasks[] = function () use($server, $user_list) {
            $connection = ssh2_connect($server["ip"], $server["porta"]);
            if (!$connection) {
                return NULL;
            }
            if (!ssh2_auth_password($connection, $server["usuario"], $server["senha"])) {
                return NULL;
            }
            if (!function_exists("ssh2_scp_send")) {
                $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                ssh2_disconnect($connection);
            } else {
                if (!ssh2_scp_send($connection, "../../home/modulos/ExcluirAtribuicao.sh", "ExcluirAtribuicao.sh", 493)) {
                    $errors[] = "Falha ao enviar o arquivo para o servidor";
                    ssh2_disconnect($connection);
                } else {
                    $exec_command = "./ExcluirAtribuicao.sh >/dev/null 2>&1 &";
                    ssh2_exec($connection, $exec_command);
                }
            }
        };
    }
    foreach ($tasks as $task) {
        $task();
    }
    $queryExclusao = "DELETE FROM atribuidos WHERE id = " . $userid;
    $resultadoExclusao = mysqli_query($conexao, $queryExclusao);
    if (!$resultadoExclusao) {
        exit("Não foi possível excluir o registro de atribuição: " . mysqli_error($conexao));
    }
    $queryExclusaoUsuarios = "DELETE FROM ssh_accounts WHERE byid = " . $user_id . " AND categoriaid = " . $categoriaid1;
    $resultadoExclusaoUsuarios = mysqli_query($conexao, $queryExclusaoUsuarios);
    if (!$resultadoExclusaoUsuarios) {
        exit("Não foi possível excluir os usuários da tabela \"ssh_accounts\": " . mysqli_error($conexao));
    }
    $_SESSION["revenda"] = "<div>Atribuição excluída com sucesso!</div>";
    header("Location: listarrev.php");
    exit;
} else {
    $queryVerificacao = "SELECT limite, limitetest, categoriaid FROM atribuidos WHERE id = " . $userid;
    $resultadoVerificacao = mysqli_query($conexao, $queryVerificacao);
    if (!$resultadoVerificacao) {
        exit("Erro na verificação do registro de atribuição: " . mysqli_error($conexao));
    }
    if (0 < mysqli_num_rows($resultadoVerificacao)) {
        $row = mysqli_fetch_assoc($resultadoVerificacao);
        $limite = $row["limite"];
        $limitetest = $row["limitetest"];
        $categoriaid = $row["categoriaid"];
        $queryAtualizacao = "SELECT limite, limitetest FROM atribuidos WHERE userid = " . $_SESSION["iduser"] . " AND categoriaid = " . $categoriaid;
        $resultadoAtualizacao = mysqli_query($conexao, $queryAtualizacao);
        if (!$resultadoAtualizacao) {
            exit("Erro na consulta da tabela de atribuição: " . mysqli_error($conexao));
        }
        if (0 < mysqli_num_rows($resultadoAtualizacao)) {
            $rowAtualizacao = mysqli_fetch_assoc($resultadoAtualizacao);
            $limiteAtual = $rowAtualizacao["limite"];
            $limitetestAtual = $rowAtualizacao["limitetest"];
            $limiteNovo = $limite + $limiteAtual;
            $limitetestNovo = $limitetest + $limitetestAtual;
            $queryAtualizacao = "UPDATE atribuidos SET limite = " . $limiteNovo . ", limitetest = " . $limitetestNovo . " WHERE userid = " . $_SESSION["iduser"] . " AND categoriaid = " . $categoriaid;
            $resultadoAtualizacao = mysqli_query($conexao, $queryAtualizacao);
            if (!$resultadoAtualizacao) {
                exit("Erro na atualização da tabela de atribuição: " . mysqli_error($conexao));
            }
        }
    }
    $sql = "SELECT * FROM ssh_accounts WHERE byid = " . $user_id;
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $user_list = "";
    $usersToDelete = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $user_list .= "./ExcluirExpiradoApi.sh " . $row["login"] . PHP_EOL;
        $usersToDelete[] = $row["login"];
    }
    $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
    $stmt_servidor = $conexao->prepare($sql_servidor);
    $stmt_servidor->execute();
    $result_servidor = $stmt_servidor->get_result();
    $servidores = [];
    while ($row = $result_servidor->fetch_assoc()) {
        $servidores[] = $row;
    }
    $tasks = [];
    foreach ($servidores as $server) {
        $tasks[] = function () use($server, $user_list) {
            $connection = ssh2_connect($server["ip"], $server["porta"]);
            if (!$connection) {
                return NULL;
            }
            if (!ssh2_auth_password($connection, $server["usuario"], $server["senha"])) {
                return NULL;
            }
            if (!function_exists("ssh2_scp_send")) {
                $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                ssh2_disconnect($connection);
            } else {
                if (!ssh2_scp_send($connection, "../../home/modulos/ExcluirAtribuicao.sh", "ExcluirAtribuicao.sh", 493)) {
                    $errors[] = "Falha ao enviar o arquivo para o servidor";
                    ssh2_disconnect($connection);
                } else {
                    $exec_command = "./ExcluirAtribuicao.sh >/dev/null 2>&1 &";
                    ssh2_exec($connection, $exec_command);
                }
            }
        };
    }
    foreach ($tasks as $task) {
        $task();
    }
    $queryExclusao = "DELETE FROM atribuidos WHERE id = " . $userid;
    $resultadoExclusao = mysqli_query($conexao, $queryExclusao);
    if (!$resultadoExclusao) {
        exit("Não foi possível excluir o registro de atribuição: " . mysqli_error($conexao));
    }
    $queryExclusaoUsuarios = "DELETE FROM ssh_accounts WHERE byid = " . $user_id;
    $resultadoExclusaoUsuarios = mysqli_query($conexao, $queryExclusaoUsuarios);
    if (!$resultadoExclusaoUsuarios) {
        exit("Não foi possível excluir os usuários da tabela \"ssh_accounts\": " . mysqli_error($conexao));
    }
    $_SESSION["revenda"] = "<div>Atribuição excluída com sucesso!</div>";
    header("Location: listarrev.php");
    exit;
}

?>