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
$_SESSION["TIPO"];
$_SESSION["login"];
$_SESSION["senha"];
$_SESSION["iduser"];
$_SESSION["byid"];
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST["codigo"];
    $codigo .= substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 2);
    $tipo = $_POST["tipo"];
    $valor = $_POST["valor"];
    $data_validade = $_POST["data_validade"];
    $usos_maximos = $_POST["usos_maximos"];
    $byid = $_POST["byid"];
    $sql_verifica_codigo = "SELECT codigo FROM cupom WHERE codigo = '" . $codigo . "'";
    $result_verifica_codigo = $conexao->query($sql_verifica_codigo);
    if (0 < $result_verifica_codigo->num_rows) {
        echo "<div class=\"alert alert-danger\" role=\"alert\">";
        $_SESSION["cupon2"] = "<div>O código informado já existe, por favor escolha outro.</div>";
        echo "</div>";
    } else {
        $sql_insere_cupom = "INSERT INTO cupom (codigo, tipo, valor, data_validade, usos_maximos, usos_restantes, byid) VALUES ('" . $codigo . "', '" . $tipo . "', " . $valor . ", '" . $data_validade . "', " . $usos_maximos . ", " . $usos_maximos . ", " . $byid . ")";
        $result_insere_cupom = $conexao->query($sql_insere_cupom);
        if ($result_insere_cupom) {
            $_SESSION["cupon1"] = "<div>Cupom criado com sucesso!<br>Cupom: <strong>" . $codigo . "</strong><br><br></div>";
        } else {
            $_SESSION["cupon2"] = "<div>Erro ao criar o cupom.</div>";
        }
    }
    echo "<script> window.location.href = 'listar_cupons.php'; </script>";
    exit;
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
echo "  \r\n  <style>\r\n    .checkbox-group {\r\n        display: flex;\r\n        justify-content: center;\r\n        align-items: center;\r\n    }\r\n    \r\n    .checkbox-option {\r\n        display: flex;\r\n        align-items: center;\r\n    }\r\n\r\n\r\n  </style>\r\n\r\n\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-6\">\r\n                        <!-- Inicio -->\r\n                        <h2 class=\"card-title\">Adicionar Cupom</h2>\r\n                        <br>\r\n                        <br>\r\n\r\n                        ";
if (!empty($errors)) {
    echo "                        <ul style=\"color: red;\">\r\n                            ";
    foreach ($errors as $error) {
        echo "                            <li>\r\n                                ";
        echo $error;
        echo "                            </li>\r\n                            ";
    }
    echo "                        </ul>\r\n                        ";
}
echo "                        ";
if (isset($success)) {
    echo "                        <p style=\"color: green;\">\r\n                            ";
    echo $success;
    echo "                        </p>\r\n                        ";
}
echo "                        <form method=\"POST\" action=\"\" class=\"mt-4\" class=\"login100-form validate-form\"\r\n                            onsubmit=\"return validarPost()\">\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                    <span class=\"input-group-text\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\" viewBox=\"0 0 20 20\"\r\n                                            xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" type=\"text\" name=\"codigo\" id=\"nome\"\r\n                                        placeholder=\"Nome do cupom\" required=\"required\" />\r\n                                </div>\r\n                            </div>\r\n                            <!-- End of Form -->\r\n\r\n                      <div class=\"form-group text-center\">\r\n                            <h8 class=\"card-title\" style=\"font-family: Arial;\">Selecione o tipo</h8>\r\n                            <div class=\"checkbox-group\">\r\n                                <div class=\"checkbox-option\">\r\n                                    <input type=\"checkbox\" name=\"tipo\" value=\"valor\">\r\n                                    <span>R\$</span>\r\n                                </div>\r\n                                <div class=\"checkbox-option\">\r\n                                    <input type=\"checkbox\" name=\"tipo\" value=\"%\">\r\n                                    <span>%</span>\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                    <span class=\"input-group-text\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\" viewBox=\"0 0 20 20\"\r\n                                            xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" type=\"number\" name=\"valor\" min=\"1\" max=\"100\" id=\"valor\"\r\n                                        placeholder=\"Desconto (%)\" required=\"required\" />\r\n                                </div>\r\n                            </div>\r\n                            <!-- End of Form -->\r\n                            <div class=\"form-group\">\r\n                                <div class=\"form-group mb-4\">\r\n                                    <div class=\"input-group\">\r\n                                        <span class=\"input-group-text\" id=\"basic-addon2\">\r\n                                            <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                            </svg>\r\n                                        </span>\r\n                                        <input class=\"form-control\" type=\"date\" name=\"data_validade\" id=\"data_validade\"\r\n                                            placeholder=\"Data de validade\" required=\"required\" />\r\n                                    </div>\r\n                                </div>\r\n\r\n                                <div class=\"form-group mb-4\">\r\n                                    <div class=\"input-group\">\r\n                                        <span class=\"input-group-text\" id=\"basic-addon1\">\r\n                                            <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                            </svg>\r\n                                        </span>\r\n                                        <input class=\"form-control\" type=\"number\" name=\"usos_maximos\" id=\"usos_maximos\"\r\n                                            placeholder=\"Limite\" required=\"required\" />\r\n                                    </div>\r\n                                </div>\r\n                                <!-- End of Form -->\r\n\r\n                                <!-- <label for=\"data_validade\">IDUSER:</label> -->\r\n                                <input class=\"form-control\" type=\"hidden\" name=\"byid\"\r\n                                    value=\"";
echo $_SESSION["iduser"];
echo "\">\r\n                                <a class=\"btn btn-danger\"\r\n                                    href=\"listar_cupons.php\">Cancelar</a>\r\n                                <input type=\"submit\" style=\"background-color: #007bff; border: none;\" name=\"submit\"\r\n                                    class=\"btn btn-primary\" value=\"Criar cupom\">\r\n\r\n\r\n                        </form>\r\n            </center>\r\n        </div>\r\n        </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n  <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>