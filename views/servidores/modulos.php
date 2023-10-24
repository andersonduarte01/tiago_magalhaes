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
$sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores WHERE ip = ?";
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
$connection = ssh2_connect($servidor["ip"], $servidor["porta"]);
if (!ssh2_auth_password($connection, $servidor["usuario"], $servidor["senha"])) {
    $_SESSION["servdor2"] = "Falha ao conectar com o servidor";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
$files = ["../../home/modulos/ExcluirExpiradoApi.sh", "../../home/modulos/CriarApi.sh", "../../home/modulos/KillUser.sh", "../../home/modulos/SshturboMakeAccount.sh"];
foreach ($files as $file) {
    $remoteFileName = basename($file);
    $localFile = file_get_contents($file);
    if (!ssh2_scp_send($connection, $file, $remoteFileName, 493)) {
        $_SESSION["servdor2"] = "Falha ao enviar o arquivo para o servidor";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
}
ssh2_disconnect($connection);
$conexao->close();
$_SESSION["servdor1"] = "<div>Modulos instalados com sucesso!</div>";
echo "<script> window.location.href='listar.servidor.php'; </script>";

?>