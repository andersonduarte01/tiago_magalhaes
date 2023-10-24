<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
if (!extension_loaded("ssh2")) {
    $_SESSION["servdor2"] = "A extensão SSH2 não está instalada. Verifique sua configuração do PHP.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$ip = $_GET["ip"];
$sql_servidor = "SELECT ip, porta, usuario, senha, subid FROM servidores WHERE ip = ?";
$stmt_servidor = $conexao->prepare($sql_servidor);
$stmt_servidor->bind_param("s", $ip);
$stmt_servidor->execute();
$result_servidor = $stmt_servidor->get_result();
$servidor = $result_servidor->fetch_assoc();
if (!$servidor) {
    $_SESSION["servdor2"] = "Servidor não encontrado";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
$subid_servidor = $servidor["subid"];
$sql = "SELECT login, senha, expira, limite FROM ssh_accounts WHERE categoriaid = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $subid_servidor);
$stmt->execute();
$resultado = $stmt->get_result();
$conta_enviada = false;
$file = fopen("../../home/modulos/exportar.sh", "w");
while ($row = $resultado->fetch_assoc()) {
    $login = $row["login"];
    $senha = $row["senha"];
    $expira = $row["expira"];
    $limite = $row["limite"];
    $dias_restantes = floor((strtotime($expira) - time()) / 86400) + 2;
    $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias_restantes . " " . $limite . "\n";
    fwrite($file, $command);
    $conta_enviada = true;
}
fclose($file);
$connection = ssh2_connect($servidor["ip"], $servidor["porta"]);
if (!ssh2_auth_password($connection, $servidor["usuario"], $servidor["senha"])) {
    $_SESSION["servdor2"] = "Falha ao conectar com o servidor";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
if (!ssh2_exec($connection, "echo \"Servidor ativo\"")) {
    $_SESSION["servdor2"] = "Servidor inativo";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
if (!ssh2_scp_send($connection, "../../home/modulos/exportar.sh", "exportar.sh", 493)) {
    $_SESSION["servdor2"] = "Falha ao enviar o arquivo para o servidor";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
$exec_command = "./exportar.sh >/dev/null 2>&1 &";
ssh2_exec($connection, $exec_command);
ssh2_disconnect($connection);
$conexao->close();
if ($conta_enviada) {
    $_SESSION["servdor1"] = "<div>Servidor Sincronizado com sucesso!</div>";
    echo "<script> window.location.href='listar.servidor.php'; </script>";
} else {
    $_SESSION["servdor1"] = "<div>Nenhum usuário foi enviado!</div>";
    echo "<script> window.location.href='listar.servidor.php'; </script>";
}

?>