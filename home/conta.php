<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if (mysqli_connect_errno()) {
    exit("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    header("Location: ../logout.php");
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../logout.php");
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    header("Location: ../logout.php");
    exit;
}
$hora_atual = date("H");
if (0 <= $hora_atual && $hora_atual < 5) {
    $saudacao = "Boa madrugada";
} else {
    if (5 <= $hora_atual && $hora_atual < 12) {
        $saudacao = "Bom dia";
    } else {
        if (12 <= $hora_atual && $hora_atual < 18) {
            $saudacao = "Boa tarde";
        } else {
            $saudacao = "Boa noite";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"]) && isset($_POST["senha"])) {
    $login = $_POST["login"];
    $senha = $_POST["senha"];
    session_start();
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(*) FROM accounts WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["iduser"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (0 < $count) {
        $stmt = mysqli_prepare($conexao, "UPDATE accounts SET login=?, senha=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $login, $senha, $_SESSION["iduser"]);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION["config"] = "Dados atualizados com sucesso!";
        } else {
            echo "Erro ao atualizar dados: " . mysqli_error($conexao);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Registro não encontrado para o ID da sessão atual.";
    }
}
$iduser = $_SESSION["iduser"];
$sql_login = "SELECT login FROM accounts WHERE id = '" . $iduser . "'";
$resultado_login = mysqli_query($conexao, $sql_login);
$sql_senha = "SELECT senha FROM accounts WHERE id = '" . $iduser . "'";
$resultado_senha = mysqli_query($conexao, $sql_senha);
$row_login = NULL;
$row_senha = NULL;
if (0 < mysqli_num_rows($resultado_login)) {
    $row_login = mysqli_fetch_assoc($resultado_login);
}
if (0 < mysqli_num_rows($resultado_senha)) {
    $row_senha = mysqli_fetch_assoc($resultado_senha);
}
$iduser = $_SESSION["iduser"];
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_FILES["foto_perfil"]["error"] === UPLOAD_ERR_OK) {
    $caminhoDestino = "../assets/img/logo.png";
    $logoLink = "https://" . $_SERVER["HTTP_HOST"] . "/assets/img/logo.png?timestamp=" . time();
    $caminhoTemporario = $_FILES["foto_perfil"]["tmp_name"];
    if (file_exists($caminhoDestino)) {
        unlink($caminhoDestino);
    }
    if (move_uploaded_file($caminhoTemporario, $caminhoDestino)) {
        $logoLink = mysqli_real_escape_string($conexao, $logoLink);
        $sql = "UPDATE config SET logo='" . $logoLink . "' WHERE byid='1'";
        mysqli_query($conexao, $sql);
        mysqli_close($conexao);
        $_SESSION["config"] = "Logo atualizada com sucesso!";
        echo "<script>window.location.href = \"" . $_SERVER["HTTP_REFERER"] . "\";</script>";
        exit;
    }
    echo "Ocorreu um erro ao atualizar a foto de perfil.";
}
mysqli_close($conexao);
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../header.php";
echo "\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n  <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n    ";
include "../menu.php";
echo "\r\n      <div class=\"container-fluid py-4\">\r\n        <div class=\"col-lg-14\">\r\n          <!-- Inicio -->\r\n          <div class=\"card card-plain mt-4\">\r\n            <center>\r\n              <h3>Configuração da conta</h3>\r\n              <br>\r\n            <div class=\"card-body\">  \r\n\r\n\r\n     \r\n                ";
if (isset($_SESSION["config"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["config"] . "' });</script>";
    unset($_SESSION["config"]);
}
echo "                ";
$nivel_sessao = $_SESSION["nivel"];
if ($nivel_sessao == 3) {
    echo "              <form method=\"POST\" action=\"\" enctype=\"multipart/form-data\" class=\"mb-3\">\r\n                    <div class=\"mb-3\">\r\n                        <h6 class=\"mb-3\">Altere a Logo</h6>\r\n                        <input class=\"form-control form-control-sm\" id=\"formFileSm\" type=\"file\" name=\"foto_perfil\" id=\"foto_perfil\">\r\n                    </div>\r\n                    <button type=\"submit\" class=\"btn btn-primary\" style=\"background-color: #5e17eb; border: none;\">Atualizar</button>\r\n                </form>\r\n\r\n                    ";
}
echo "\r\n\r\n            \r\n            <form class=\"login100-form validate-form\" action=\"\" method=\"post\">\r\n                <h6>Altere seu login</h6>\r\n                <div class=\"form-group mb-4\">\r\n                    <div class=\"input-group\">\r\n                        <input type=\"text\" class=\"form-control\" name=\"login\" placeholder=\"Altere seu login\" value=\"";
echo $row_login["login"];
echo "\" ";
echo $_SESSION["nivel"] == 3 ? "" : "readonly";
echo " required>\r\n                    </div>\r\n                </div>\r\n            \r\n                <h6>Altere sua senha</h6>\r\n                <div class=\"form-group mb-4\">\r\n                    <div class=\"input-group\">\r\n                        <input type=\"text\" class=\"form-control\" name=\"senha\" placeholder=\"Altere sua senha\" value=\"";
echo $row_senha["senha"];
echo "\" required>\r\n                    </div>\r\n                </div>\r\n            \r\n                <button class=\"btn btn-primary\" style=\"background-color: #5e17eb; border: none;\" name=\"atualizar\" id=\"atualizar\" value=\"atualizar\">\r\n                    Atualizar\r\n                </button>\r\n            </form>\r\n\r\n              </div>\r\n            </center>\r\n          </div>\r\n          <!--FIM-->\r\n        </div>\r\n      </div>\r\n    </div>\r\n  </main>\r\n\r\n  <!--   Core JS Files   -->\r\n  <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n  <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n  <script src=\"../assets/js/menu.js\"></script>\r\n  <script src=\"../assets/js/page.js\"></script>\r\n\r\n</body>\r\n\r\n</html>\r\n";

?>