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
if (!isset($_GET["id"])) {
    echo "<script> window.location.href='listarrev.php'; </script>";
    exit;
}
$userid = $_GET["id"];
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
excluirrevendas($conexao, $userid);
$_SESSION["revenda"] = "<div>Revenda excluída com sucesso!</div>";
header("Location: listarrev.php");
exit;
function createScriptFile($user_list)
{
    $file = fopen("../../home/modulos/ExcluirRevenda.sh", "w");
    $command = $user_list . "\\n";
    fwrite($file, $command);
    fclose($file);
}
function sendAndExecuteScript($servidores)
{
    $tasks = [];
    foreach ($servidores as $server) {
        $tasks[] = function () use($server) {
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
                if (!ssh2_scp_send($connection, "../../home/modulos/ExcluirRevenda.sh", "ExcluirRevenda.sh", 493)) {
                    $errors[] = "Falha ao enviar o arquivo para o servidor";
                    ssh2_disconnect($connection);
                } else {
                    $exec_command = "./ExcluirRevenda.sh >/dev/null 2>&1 &";
                    ssh2_exec($connection, $exec_command);
                }
            }
        };
    }
    foreach ($tasks as $task) {
        $task();
    }
}
function excluirRevendas($conexao, $userid)
{
    $query = "DELETE FROM atribuidos WHERE userid = " . $userid;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Não foi possível excluir o registro de atribuição: " . mysqli_error($conexao));
    }
    $query = "SELECT login FROM ssh_accounts WHERE byid = " . $userid;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Erro ao recuperar os usuários da tabela ssh_accounts: " . mysqli_error($conexao));
    }
    $user_list = "";
    $usersToDelete = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $user_list .= "./ExcluirExpiradoApi.sh " . $row["login"] . PHP_EOL;
        $usersToDelete[] = $row["login"];
    }
    createscriptfile($user_list);
    $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
    $stmt_servidor = $conexao->prepare($sql_servidor);
    $stmt_servidor->execute();
    $result_servidor = $stmt_servidor->get_result();
    $servidores = [];
    while ($row = $result_servidor->fetch_assoc()) {
        $servidores[] = $row;
    }
    sendandexecutescript($servidores);
    $user_list_for_sql = implode("','", $usersToDelete);
    $query_delete_ssh_accounts = "DELETE FROM ssh_accounts WHERE login IN ('" . $user_list_for_sql . "')";
    $resultado_delete_ssh_accounts = mysqli_query($conexao, $query_delete_ssh_accounts);
    if (!$resultado_delete_ssh_accounts) {
        exit("Não foi possível excluir os registros da tabela ssh_accounts: " . mysqli_error($conexao));
    }
    $query = "SELECT id FROM accounts WHERE byid = " . $userid;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Erro ao recuperar as revendas associadas: " . mysqli_error($conexao));
    }
    while ($row = mysqli_fetch_assoc($resultado)) {
        $revendaId = $row["id"];
        excluirRevendas($conexao, $revendaId);
    }
    $query = "DELETE FROM accounts WHERE id = " . $userid . " OR byid = " . $userid;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Não foi possível excluir o registro da conta: " . mysqli_error($conexao));
    }
}

?>