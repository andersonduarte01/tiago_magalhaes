<?php


date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
date_default_timezone_set("America/Sao_Paulo");
$conexao = mysqli_connect($server, $user, $pass, $db);
if (mysqli_connect_errno()) {
    exit("Connection failed: " . mysqli_connect_error());
}
if (!isset($_GET["id"])) {
    echo "<script> window.location.href='listarrev.php'; </script>";
    exit;
}
$id = $_GET["id"];
$userid = $_GET["userid"];
$query = "UPDATE atribuidos SET suspenso = 1 WHERE id = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $id);
$resultado = $stmt->execute();
$stmt->close();
$sql = "SELECT login, senha, expira, limite FROM ssh_accounts WHERE byid = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $userid);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
if (0 < $resultado->num_rows) {
    $file = fopen("../../home/modulos/excluir.sh", "w");
    while ($row = $resultado->fetch_assoc()) {
        $login = $row["login"];
        $expira = $row["expira"];
        $dias_restantes = floor((strtotime($expira) - time()) / 86400);
        $command = "./ExcluirExpiradoApi.sh " . $login . "\n";
        fwrite($file, $command);
    }
    fclose($file);
    $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
    $stmt_servidor = $conexao->prepare($sql_servidor);
    $stmt_servidor->execute();
    $result_servidor = $stmt_servidor->get_result();
    $servidores = [];
    while ($row = $result_servidor->fetch_assoc()) {
        $servidores[] = $row;
    }
    $stmt_servidor->close();
    if ($servidores) {
        foreach ($servidores as $servidor) {
            $ssh = ssh2_connect($servidor["ip"], $servidor["porta"]);
            if (!$ssh) {
                $_SESSION["error"] = "Falha ao conectar com o servidor";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_auth_password($ssh, $servidor["usuario"], $servidor["senha"])) {
                $_SESSION["error"] = "Falha na autenticação SSH";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_scp_send($ssh, "../../home/modulos/excluir.sh", "excluir.sh")) {
                $_SESSION["error"] = "Falha ao enviar o arquivo para o servidor.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_exec($ssh, "chmod +x excluir.sh")) {
                $_SESSION["error"] = "Falha ao definir permissões no arquivo remoto.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_exec($ssh, "./excluir.sh >/dev/null 2>&1 &")) {
                $_SESSION["error"] = "Falha ao executar o arquivo no servidor.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            ssh2_disconnect($ssh);
        }
    }
    $_SESSION["revenda"] = "<div>Revenda suspensa com sucesso!</div>";
    header("Location: listarrev.php");
    exit;
} else {
    $_SESSION["excluidocomsucesso"] = "Nenhuma conta foi excluída.";
    header("Location: listarrev.php");
    exit;
}

?>