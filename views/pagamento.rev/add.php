<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] != 2 && $_SESSION["nivel"] != 3) {
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
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$sql = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["iduser"] . "'";
$result = $conexao->query($sql);
if ($result && 0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION["validade"] = $row["expira"];
        $_SESSION["limite"] = $row["limite"];
        $_SESSION["byid"] = $row["byid"];
    }
}
$sql4 = "SELECT * FROM accounts WHERE id = '" . $_SESSION["byid"] . "'";
$result4 = $conexao->query($sql4);
if ($result4 && 0 < $result4->num_rows) {
    while ($row4 = $result4->fetch_assoc()) {
        $_SESSION["valorlogin"] = $row4["valorrevenda"];
        $valorlogin = $_SESSION["valorlogin"];
        if ($valorlogin == 0) {
            echo "<script>alert(\"Seu Revendedor Não esta cadastrado em nossa Plataforma\");</script><script>window.location.href = \"../../nivel.php\";</script>";
            exit;
        }
        $data = $_SESSION["validade"];
        $data = date("d/m/Y", strtotime($data));
        $limite = $_SESSION["limite"];
    }
    $sql5 = "SELECT * FROM accounts WHERE id = '" . $_SESSION["iduser"] . "'";
    $result5 = $conexao->query($sql5);
    if ($result5 && 0 < $result5->num_rows) {
        while ($row5 = $result5->fetch_assoc()) {
            $resu = $row5["valorrevenda"];
            if ($resu == 0) {
                echo "<script>window.location.href = \"../../views/configuracao_pagamento/config.php\";</script>";
                exit;
            }
        }
    }
}
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
$conexao->close();
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n\r\n\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-5\">\r\n                        <!-- Inicio -->\r\n\r\n                        <form action=\"processandor.php\" method=\"post\">\r\n                          \r\n                            <h3 class=\"card-title\">Bem vindo a página de compras</h3><br><br>\r\n                            \r\n                            <h4 class=\"zmdi zmdi-font\" style=\"font-size: 20px; text-align: center;\">\r\n                                Seu login é:\r\n                                ";
echo $_SESSION["login"];
echo "                            </h4>\r\n                            <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">\r\n                                Seu vencimento é:\r\n                                ";
echo $data;
echo "                            </h4>\r\n                            <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">\r\n                                Seu limite é:\r\n                                ";
echo $limite;
echo "                            </h4>\r\n                            <input type=\"number\" name=\"addquantidade\" placeholder=\"Quantidade de Créditos (mínimo 10)\"\r\n       style=\"width: 70%; height: 40px; border-radius: 5px; border: 1px solid #ccc; padding: 0 10px; margin-bottom: 10px; margin-top: 10px;\" min=\"10\">\r\n\r\n                                \r\n                             \r\n                    \r\n                            <p>Formulário de Pagamento</p>\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                   <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"Nome\" type=\"text\" id=\"nome\"\r\n                                        name=\"nome\" placeholder=\"Nome\" required>\r\n                                </div>\r\n                            </div>\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                     <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"Sobrenome\" type=\"text\"\r\n                                        id=\"sobrenome\" name=\"sobrenome\" required>\r\n                                </div>\r\n                            </div>\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                    <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"Email\" type=\"email\" id=\"email\"\r\n                                        name=\"email\" required>\r\n                                </div>\r\n                            </div>\r\n\r\n                            <div class=\"form-group mb-4\">\r\n                                <div class=\"input-group\">\r\n                                    <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                                    <input class=\"form-control\" placeholder=\"CPF\" type=\"text\" id=\"cpf\"\r\n                                        name=\"cpf\" required>\r\n                                </div>\r\n                            </div>\r\n                            \r\n                       \r\n                            <div class=\"container-login100-form-btn\">\r\n                                <div class=\"wrap-login100-form-btn\">\r\n                                    <button class=\"btn btn-primary\" type=\"submit\" value=\"Renovar\">\r\n                                        Adicionar\r\n                                    </button>\r\n                                </div>\r\n                            </div>\r\n                        </form>\r\n\r\n         \r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n         \r\n\r\n\r\n         <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n\r\n</body>\r\n\r\n</html>";

?>