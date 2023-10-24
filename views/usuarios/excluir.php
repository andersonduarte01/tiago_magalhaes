<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
if (!extension_loaded("ssh2")) {
    throw new Exception("A extensão SSH2 não está carregada. Verifique se a extensão está instalada e ativada no PHP.");
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    redirect("logout.php");
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    redirect("logout.php");
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    redirect("logout.php");
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    throw new Exception("Connection failed: " . $conexao->connect_error);
}
$sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
$stmt_servidor = $conexao->prepare($sql_servidor);
$stmt_servidor->execute();
$result_servidor = $stmt_servidor->get_result();
$servidores = [];
while ($row = $result_servidor->fetch_assoc()) {
    $servidores[] = $row;
}
$iduser = $_SESSION["iduser"];
$sql = "SELECT login, expira FROM ssh_accounts WHERE expira < NOW() AND byid = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $iduser);
$stmt->execute();
$expired_accounts = $stmt->get_result();
if (0 < $expired_accounts->num_rows) {
    while ($row = $expired_accounts->fetch_assoc()) {
        $login = $row["login"];
        $expira = $row["expira"];
        $dias_restantes = floor((strtotime($expira) - time()) / 86400);
        $command = "./ExcluirExpiradoApi.sh " . $login . "\n";
        foreach ($servidores as $servidor) {
            $file = fopen("../../home/modulos/excluir_" . $servidor["ip"] . ".sh", "a");
            fwrite($file, $command);
            fclose($file);
        }
    }
    foreach ($servidores as $servidor) {
        processServer($servidor, "../../home/modulos/excluir_" . $servidor["ip"] . ".sh");
    }
}
$query = "DELETE FROM ssh_accounts WHERE expira < NOW()";
$resultado = mysqli_query($conexao, $query);
if (!$resultado) {
    throw new Exception("Não foi possível excluir os dados da tabela ssh_accounts: " . mysqli_error($conexao));
}
$_SESSION["excluidocomsucesso"] = "<div>Todos os usuários globais que estão expirados foram excluídos!</div>";
redirect($_SERVER["HTTP_REFERER"] ?? "default_page.php");
exit;
function redirect($url)
{
    header("Location: " . $url);
    exit;
}
function processServer($servidor, $filepath)
{
    $ssh = ssh2_connect($servidor["ip"], $servidor["porta"]);
    if (!$ssh) {
        throw new Exception("Falha na conexão SSH com o servidor");
    }
    if (!ssh2_auth_password($ssh, $servidor["usuario"], $servidor["senha"])) {
        throw new Exception("Falha na autenticação SSH com o servidor");
    }
    if (!ssh2_scp_send($ssh, $filepath, "excluir.sh")) {
        throw new Exception("Falha ao enviar o arquivo para o servidor");
    }
    if (!ssh2_exec($ssh, "chmod +x excluir.sh")) {
        throw new Exception("Falha ao definir permissões no arquivo remoto");
    }
    if (!ssh2_exec($ssh, "./excluir.sh >/dev/null 2>&1 &")) {
        throw new Exception("Falha ao executar o arquivo no servidor");
    }
    ssh2_disconnect($ssh);
}

?>