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
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<style>\r\n.letra-icon {\r\n    font-size: 28px;\r\n    border-radius: 50%;\r\n    border: 1px solid #d9dbdf;\r\n    background-color: #d9dbdf; /* Defina a cor de fundo desejada */\r\n    display: inline-block; /* Garante que o background-color abranja todo o espaço do ícone */\r\n    width: 45px; /* Define a largura igual à font-size para tornar o ícone circular */\r\n    height: 45px; /* Define a altura igual à font-size para tornar o ícone circular */\r\n    text-align: center; /* Centraliza o conteúdo do ícone verticalmente */\r\n    line-height: 45px; /* Centraliza o conteúdo do ícone horizontalmente */\r\n    color: #1c1c1d; /* Define a cor do texto do ícone */\r\n}\r\n\r\n.espaco {\r\n    width: 20px;\r\n}\r\n.cor-escura {\r\n    background-color: #dee5ee; /* Defina a cor escura desejada */\r\n}\r\n\r\n</style>\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n\r\n\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-7\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                        <!-- Inicio -->\r\n\r\n                        <h2 class=\"card-title\">Lista de atribuições vencidas</h2><br><br>\r\n\r\n\r\n                      <!-- Inicio -->\r\n                   <div class=\"col-12 mb-3\">\r\n                        <div class=\"card card-sm\">\r\n                            <div class=\"card-body1 p-2\">\r\n                                ";
$iduser = $_SESSION["iduser"];
$sql = "SELECT atribuidos.id, atribuidos.valor, atribuidos.limite, atribuidos.limitetest, atribuidos.tipo, atribuidos.expira, categorias.nome, accounts.login, accounts.senha, atribuidos.userid \r\n                                        FROM atribuidos \r\n                                        INNER JOIN categorias ON atribuidos.categoriaid = categorias.subid \r\n                                        INNER JOIN accounts ON atribuidos.userid = accounts.id \r\n                                        WHERE atribuidos.byid = '" . $iduser . "' AND atribuidos.expira < NOW()";
$resultado = mysqli_query($conexao, $sql);
if (0 < mysqli_num_rows($resultado)) {
    while ($atribuido = mysqli_fetch_assoc($resultado)) {
        $atribuidos_id = $atribuido["id"];
        $userid = $atribuido["userid"];
        $categoriaId = $atribuido["categoriaid"];
        $primeira_letra = substr($atribuido["login"], 0, 1);
        $sqlVerificaSuspensao = "SELECT suspenso FROM atribuidos WHERE id = " . $atribuidos_id;
        $resultadoVerificaSuspensao = $conexao->query($sqlVerificaSuspensao);
        $linhaVerificaSuspensao = $resultadoVerificaSuspensao->fetch_assoc();
        $suspenso = $linhaVerificaSuspensao["suspenso"];
        echo "                                    \r\n                                            <div class=\"col-12\">\r\n                                                <div class=\"card mb-2 cor-escura\">\r\n                                                    <div class=\"card-body d-flex align-items-center p-2\">\r\n                                                        <div class=\"revendedor-icon rounded-circle\">\r\n                                                            <span class=\"letra-icon\">";
        echo $primeira_letra;
        echo "</span>\r\n                                                        </div>\r\n                                                        <div class=\"espaco\"></div>\r\n                                                        <div class=\"informacoes\">\r\n                                                            <div class=\"d-flex\">\r\n                                                                <i class=\"material-icons\">person</i>\r\n                                                                <h6 class=\"card-title\">";
        echo $atribuido["login"];
        echo "</h6>\r\n                                                            </div>\r\n                                                            <div class=\"d-flex\">\r\n                                                                <i class=\"material-icons\">lock</i>\r\n                                                                <h6 class=\"card-text\">";
        echo $atribuido["senha"];
        echo "</h6>\r\n                                                            </div>\r\n                                                             ";
        if ($suspenso == 1) {
            echo "                                                       <a href=\"editar.php?id=";
            echo $atribuidos_id;
            echo "&userid=";
            echo $userid;
            echo "&categoriaid=";
            echo $categoriaId;
            echo "\" class=\"btn btn-info btn-sm\" style=\"font-size: 11px;\">Reativa Revenda</a>\r\n                                                    ";
        } else {
            echo "                                                        <a href=\"suspenso.php?id=";
            echo $atribuidos_id;
            echo "&userid=";
            echo $userid;
            echo "\" class=\"btn btn-info btn-sm\" style=\"font-size: 11px;\">Suspende Revenda</a>\r\n                                                    ";
        }
        echo "                                                        </div>\r\n                                                        <img src=\"https://cdn-icons-png.flaticon.com/512/3064/3064224.png\" style=\"position: absolute; height: 65px; right: 2px; opacity: 0.3;\">\r\n                                                    </div>\r\n                                                    <div>\r\n                                                        <p style=\"text-align: left; padding-left: 15px;\" class=\"card-text\">Categoria: ";
        echo $atribuido["nome"];
        echo " | Tipo: ";
        echo $atribuido["tipo"];
        echo "</p>\r\n                                                    </div>\r\n                                                </div>\r\n                                            </div>\r\n                                ";
    }
} else {
    echo "<p>Nenhuma revenda cadastrada.</p>";
}
echo "                            </div>\r\n                        </div>\r\n                    </div>\r\n                    <hr>\r\n\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n\r\n</body>\r\n\r\n</html>";

?>