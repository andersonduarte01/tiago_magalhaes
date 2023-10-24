<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Falha na conexão: " . $conexao->connect_error);
}
verificarSessaoUsuario();
if (isset($_GET["id"])) {
    $idUsuario = $_GET["id"];
    $detalhesUsuario = obterDetalhesUsuario($idUsuario, $conexao);
} else {
    definirMensagemSessao("ID do usuário não fornecido.");
    redirecionarParaListaUsuarios();
}
$servidores = obterTodosServidores($conexao);
foreach ($servidores as $servidor) {
    excluirContaExpirada($servidor, $detalhesUsuario["login"]);
}
excluirUsuario($idUsuario, $conexao);
$conexao->close();
function verificarSessaoUsuario()
{
    if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2 || !isset($_SESSION["login"]) || !isset($_SESSION["senha"]) || isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
        echo "<script> window.location.href='../../logout.php'; </script>";
        exit;
    }
}
function obterDetalhesUsuario($idUsuario, $conexao)
{
    $stmt = $conexao->prepare("SELECT login, categoriaid FROM ssh_accounts WHERE id = ?");
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if (0 < $resultado->num_rows) {
        return $resultado->fetch_assoc();
    }
    definirMensagemSessao("Usuário não encontrado.");
    redirecionarParaListaUsuarios();
}
function obterTodosServidores($conexao)
{
    $resultado = $conexao->query("SELECT ip, porta, usuario, senha FROM servidores");
    $servidores = [];
    while ($row = $resultado->fetch_assoc()) {
        $servidores[] = $row;
    }
    return $servidores;
}
function excluirContaExpirada($servidor, $nomeUsuario)
{
    $conexao = ssh2_connect($servidor["ip"], $servidor["porta"]);
    if (!$conexao || !ssh2_auth_password($conexao, $servidor["usuario"], $servidor["senha"])) {
        return NULL;
    }
    $comando_exclusao = "./ExcluirExpiradoApi.sh \"" . $nomeUsuario . "\" >/dev/null 2>&1 &";
    ssh2_exec($conexao, $comando_exclusao);
}
function excluirUsuario($idUsuario, $conexao)
{
    $stmt = $conexao->prepare("DELETE FROM ssh_accounts WHERE id = ?");
    $stmt->bind_param("i", $idUsuario);
    if ($stmt->execute()) {
        definirMensagemSessao("Usuário excluído com sucesso.");
    } else {
        definirMensagemSessao("Erro ao excluir o usuário: " . $conexao->error);
    }
    redirecionarParaListaUsuarios();
}
function definirMensagemSessao($mensagem)
{
    $_SESSION["excluidocomsucesso"] = $mensagem;
}
function redirecionarParaListaUsuarios()
{
    header("Location: listarusuarios.php");
    exit;
}

?>