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
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$sql = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["iduser"] . "'";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION["validade"] = $row["expira"];
        $_SESSION["limite"] = $row["limite"];
    }
} else {
    header("Location: ../../nivel.php");
}
$sql4 = "SELECT * FROM accounts WHERE id = '" . $_SESSION["byid"] . "'";
$result4 = $conexao->query($sql4);
if (0 < $result4->num_rows) {
    while ($row4 = $result4->fetch_assoc()) {
        $_SESSION["valorlogin"] = $row4["valorrevenda"];
        $valorlogin = $_SESSION["valorlogin"];
    }
    if ($valorlogin == 0) {
        echo "<script>alert(\"Seu Revendedor Não esta cadrastado em nossa Plataforma\");</script><script>window.location.href = \"../../nivel.php\";</script>";
    }
}
$data = $_SESSION["validade"];
$data = date("d/m/Y", strtotime($data));
$limite = $_SESSION["limite"];
$_SESSION["limite"] = $limite;
$valor = $limite * $valorlogin;
$_SESSION["valor"] = $valor;
$sql5 = "SELECT * FROM accounts WHERE id = '" . $_SESSION["iduser"] . "'";
$result5 = $conexao->query($sql5);
if (0 < $result5->num_rows) {
    while ($row5 = $result5->fetch_assoc()) {
        $resu = $row5["valorrevenda"];
        if ($resu == 0) {
            echo "<script>window.location.href = \"../configuracao_pagamento/config.php\";</script>";
        }
    }
}
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n     \r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n\r\n                        <style>\r\n                            .bloco img {\r\n                                width: 190px;\r\n                                height: 190px;\r\n                            }\r\n                        </style>\r\n                        <div id=\"texto\" class=\"container\">\r\n                            <div id=\"bloco\" class=\"bloco\">\r\n                                <h1>APROVADO</h1>\r\n                                <p>Seu pagamento foi aprovado</p>\r\n                                <img src=\"https://www.pngplay.com/wp-content/uploads/2/Approved-PNG-Photos.png\"\r\n                                    alt=\"aprovado\">\r\n                            </div><br><br>\r\n\r\n\r\n                            <h4>Seu login é: <span id=\"login\">\r\n                                    ";
echo $_SESSION["login"];
echo "                                </span></h4>\r\n                            <h5>Vencimento:<span id=\"vencimento\">\r\n                                    ";
echo " " . $data;
echo "                                </span></h5>\r\n                            <h5>Limite:<span id=\"limite\">\r\n                                    ";
echo " " . $limite;
echo "                                </span></h5>\r\n                            <h5>Mensalidade:<span id=\"valor\">\r\n                                    ";
echo " " . $valor;
echo "                                </span></h5>\r\n                            <button type=\"button\" class=\"btn btn-primary\"\r\n                                onclick=\"copyToClipboard()\">Copiar</button>\r\n\r\n\r\n                            <br>\r\n                            <br>\r\n                        </div>\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n        <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n   \r\n</body>\r\n\r\n</html>";

?>