<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
if (!extension_loaded("ssh2")) {
    $_SESSION["configerr"] = "A extensão SSH2 não está instalada. Verifique sua configuração do PHP.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
$stmt_servidor = $conexao->prepare($sql_servidor);
$stmt_servidor->execute();
$result_servidor = $stmt_servidor->get_result();
$servidores = [];
$servidores_erro = [];
while ($row = $result_servidor->fetch_assoc()) {
    $servidores[] = $row;
}
if (!empty($servidores)) {
    foreach ($servidores as $servidor) {
        $connection = ssh2_connect($servidor["ip"], $servidor["porta"]);
        if (ssh2_auth_password($connection, $servidor["usuario"], $servidor["senha"])) {
            $stream = ssh2_exec($connection, "sudo systemctl is-active online.service\n");
            stream_set_blocking($stream, true);
            $response = stream_get_contents($stream);
            fclose($stream);
            if (trim($response) !== "active") {
                $_SESSION["configerr"] = "Erro ao desativar online. <br> O serviço já está desativado.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            ssh2_exec($connection, "sudo systemctl stop online.service >/dev/null 2>&1 &");
            ssh2_exec($connection, "sudo systemctl disable online.service >/dev/null 2>&1 &");
            ssh2_disconnect($connection);
        } else {
            $servidores_erro[] = $servidor["ip"];
        }
    }
}
if (1 < count($servidores_erro)) {
    $msg_sucesso = "<div>Online dedativado com sucesso nos seguintes servidores:</div>";
    $msg_erro = "<div>Erro ao desativar online nos seguintes servidores:</div>";
    foreach ($servidores_erro as $ip) {
        $msg_erro .= "<div>" . $ip . " - Falha na conexão SSH com o servidor.</div>";
    }
    $_SESSION["config"] = $msg_sucesso . $msg_erro;
} else {
    if (count($servidores_erro) === 1) {
        $_SESSION["configerr"] = "Erro ao desativar online no servidor: " . $servidores_erro[0];
    } else {
        $_SESSION["config"] = "<div>Online desativado com sucesso!</div>";
    }
}
header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;

?>