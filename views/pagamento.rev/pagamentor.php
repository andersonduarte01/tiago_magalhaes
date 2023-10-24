<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
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
$data = $_SESSION["validade"];
$data = date("d/m/Y", strtotime($data));
$limite = $_SESSION["limite"];
$valor = $limite * $valorlogin;
$valor = $_SESSION["valor"];
$revendedor_id = $_SESSION["byid"];
if (isset($_POST["cupom"])) {
    $cupom = mysqli_real_escape_string($conexao, $_POST["cupom"]);
    $stmt = $conexao->prepare("SELECT * FROM cupom WHERE codigo = ? AND byid = ?");
    $stmt->bind_param("si", $cupom, $revendedor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        $usos_restantes = $row["usos_restantes"];
        if (0 < $usos_restantes) {
            $tipo = $row["tipo"];
            $valor_desconto = $row["valor"];
            $valorlogin = $_SESSION["valorlogin"];
            $limite = $_SESSION["limite"];
            if ($tipo == "valor") {
                $valor = $limite * $valorlogin - $valor_desconto;
            } else {
                if ($tipo == "%") {
                    $valor_desconto = $valor * $valor_desconto / 100;
                    $valor = $limite * $valorlogin - $valor_desconto;
                }
            }
            $_SESSION["valor"] = $valor;
            $stmt = $conexao->prepare("UPDATE cupom SET usos_restantes = usos_restantes - 1 WHERE codigo = ?");
            $stmt->bind_param("s", $cupom);
            $stmt->execute();
            $_SESSION["mensagem"] = "<div class=\"alert alert-danger\"><h6>Parabéns... <br>Seu cupom foi aplicado.</h6></div>";
        } else {
            $_SESSION["mensagem"] = "<div class=\"alert alert-danger\"><h6>Infelizmente o cupom informado já esgotou.</h6></div>";
        }
    } else {
        $_SESSION["mensagem"] = "<div class=\"alert alert-danger\"><h6>Ops... <br> O cupom informado não é válido.</h6></div>";
    }
}
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
echo "\r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n\r\n     \r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-6\">\r\n                        <!-- Inicio -->\r\n                        \r\n                        <h3 class=\"card-title\">Bem vindo a pagina de pagamento</h3><br><br>\r\n                        \r\n                        \r\n                        <form method=\"post\">\r\n                            <div class=\"limiter\">\r\n                                <span class=\"login100-form-title p-b-48\">\r\n                                    <h4 class=\"zmdi zmdi-font\" style=\"font-size: 20px; text-align: center;\">Seu\r\n                                        login é:\r\n                                        ";
echo $_SESSION["login"];
echo "                                    </h4>\r\n                                </span>\r\n                                <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Seu\r\n                                    vencimento é: ";
echo " " . $data;
echo "                                </h4>\r\n                                <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Seu\r\n                                    limite é:  ";
echo " " . $limite;
echo "                                </h4>\r\n                                <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Sua\r\n                                    Mensalidade é: ";
echo " R\$ " . $valor;
echo "                                </h4>\r\n                        </form>\r\n                        <form method=\"post\" class=\"mt-4\" class=\"login100-form validate-form\"\r\n                            onsubmit=\"return validarPost2()\">\r\n                            <div class=\"input-group\">\r\n                                   <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                <input class=\"form-control\" type=\"text\" name=\"cupom\"\r\n                                    placeholder=\"Digite o cupom\">\r\n                            </div>\r\n                            <br>\r\n                            <input type=\"submit\" class=\"btn btn-primary\" value=\"Aplicar\">\r\n                        </form>\r\n                        <br>\r\n\r\n                        ";
if (isset($_SESSION["mensagem"])) {
    echo "<h3>" . $_SESSION["mensagem"] . "</h3>";
    unset($_SESSION["mensagem"]);
}
echo "\r\n                        <form method=\"POST\" action=\"processando.php\" class=\"mt-4\"\r\n                            class=\"login100-form validate-form\" onsubmit=\"return validarPost()\">\r\n                            <p>Formulário de Pagamento</p>\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                     <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"Nome\" type=\"text\" id=\"nome\"\r\n                                        name=\"nome\" placeholder=\"Nome\" required>\r\n                                </div>\r\n                            </div>\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                       <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"Sobrenome\" type=\"text\"\r\n                                        id=\"sobrenome\" name=\"sobrenome\" required>\r\n                                </div>\r\n                            </div>\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                      <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"Email\" type=\"email\" id=\"email\"\r\n                                        name=\"email\" required>\r\n                                </div>\r\n                            </div>\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                      <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"CPF\" type=\"text\" id=\"cpf\"\r\n                                        name=\"cpf\" required>\r\n                                </div>\r\n                            </div>\r\n                            <a class=\"btn btn-danger\" href=\"renov.php\">Cancelar</a>\r\n                            <input type=\"submit\" name=\"pagar\" class=\"btn btn-primary\" value=\"Pagar\">\r\n\r\n                        </form>\r\n\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n       <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>