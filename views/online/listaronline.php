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
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n<style>\r\n.letra-icon {\r\n    font-size: 28px;\r\n    border-radius: 50%;\r\n    border: 1px solid #d9dbdf;\r\n    background-color: #00ff00; /* Define a cor verde desejada */\r\n    display: inline-block;\r\n    width: 44px;\r\n    height: 45px;\r\n    text-align: center;\r\n    line-height: 45px;\r\n    color: #1c1c1d;\r\n}\r\n\r\n.espaco {\r\n    width: 20px;\r\n}\r\n.cor-escura {\r\n    background-color: #dee5ee; /* Defina a cor escura desejada */\r\n}\r\n\r\n.bola {\r\n    width: 15px;\r\n    height: 15px;\r\n    border-radius: 50%;\r\n    background-color: #00ff00; /* Cor verde */\r\n    position: relative;\r\n    animation: wave-animation 2s linear infinite;\r\n}\r\n\r\n@keyframes wave-animation {\r\n    0% {\r\n        box-shadow: 0 0 0 0 rgba(0, 255, 0, 0);\r\n    }\r\n    50% {\r\n        box-shadow: 0 0 0 5px rgba(0, 255, 0, 0.3);\r\n    }\r\n    100% {\r\n        box-shadow: 0 0 0 10px rgba(0, 255, 0, 0);\r\n    }\r\n}\r\n\r\n</style>\r\n\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n     \r\n\r\n            <center>\r\n               <div class=\"container-fluid py-5\">\r\n                      <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                        <h2 class=\"card-title\">Lista de Usuários Online</h2><br><br>\r\n                    \r\n                    \r\n                     <div class=\"col-12 mb-3\">\r\n                            <div class=\"card card-sm\">\r\n                                <div class=\"card-body1 p-2\">\r\n                        \r\n                       \r\n                  ";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$resultsPerPage = isset($_GET["showAll"]) ? 1000 : 10;
$startFrom = ($page - 1) * $resultsPerPage;
echo "                        \r\n                        <form class=\"form-inline my-3\" action=\"\" method=\"GET\">\r\n                            <div class=\"input-group\">\r\n                                <input type=\"text\" id=\"searchInput\" class=\"form-control\" name=\"searchName\" placeholder=\"Buscar por nome\">\r\n                                ";
if (isset($_GET["showAll"])) {
    echo "                                    <div class=\"input-group-append\">\r\n                                        <a href=\"?searchName=";
    echo $_GET["searchName"];
    echo "&page=1\" class=\"btn btn-secondary\">Deslistar Todos</a>\r\n                                    </div>\r\n                                ";
} else {
    echo "                                    <div class=\"input-group-append\">\r\n                                        <a href=\"?searchName=";
    echo $_GET["searchName"];
    echo "&page=1&showAll\" class=\"btn btn-primary\">Listar Todos</a>\r\n                                    </div>\r\n                                ";
}
echo "                            </div>\r\n                        </form>\r\n                        \r\n                        ";
$sessao_iduser = $_SESSION["iduser"];
$searchName = isset($_GET["searchName"]) ? $_GET["searchName"] : "";
$sql = "SELECT a.login AS account_login, o.login AS online_login, o.online, o.start_time, o.limite\r\n                                FROM api_online o\r\n                                INNER JOIN accounts a ON o.byid = a.id\r\n                                WHERE o.byid = '" . $sessao_iduser . "' AND o.login LIKE '%" . $searchName . "%'\r\n                                ORDER BY o.start_time DESC\r\n                                LIMIT " . $startFrom . ", " . $resultsPerPage;
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $account_login = $row["account_login"];
        $online_login = $row["online_login"];
        $limite = $row["limite"];
        $start_time = strtotime($row["start_time"]);
        $now = time();
        $diff_in_seconds = $now - $start_time;
        $diff_in_hours = floor($diff_in_seconds / 3600);
        $diff_in_minutes = floor($diff_in_seconds % 3600 / 60);
        $diff_in_seconds = $diff_in_seconds % 60;
        echo "<div class=\"col-12 usuario\"><div class=\"card mb-2 cor-escura\"><div class=\"card-body d-flex align-items-center p-1\"><div class=\"revendedor-icon rounded-circle\">";
        echo "<span class=\"letra-icon\">" . strtoupper(substr($online_login, 0, 1)) . "</span>";
        echo "</div><div class=\"espaco\"></div><div class=\"informacoes\"><div class=\"d-flex\">";
        echo "<span  style=\"font-size: 0.9rem;\"class=\"card-text\">Dono: " . $account_login . " | Login: " . $online_login . "</span>";
        echo "</div><div class=\"d-flex\">";
        echo "<span class=\"card-text\">Limite: " . $limite . "</span>";
        echo "</div><div style=\"position: absolute;right: 7px;top: 13px;\" class=\"bola\"></div></div></div>";
        if (0 < $diff_in_hours) {
            echo "<p style=\"text-align: left; font-size: 0.9rem; padding-left: 15px;\" class=\"card-text\">Online há " . $diff_in_hours . " horas, " . $diff_in_minutes . " minutos e " . $diff_in_seconds . " segundos</p>";
        } else {
            if (0 < $diff_in_minutes) {
                echo "<p style=\"text-align: left; font-size: 0.9rem; padding-left: 15px;\" class=\"card-text\">Online há " . $diff_in_minutes . " minutos e " . $diff_in_seconds . " segundos</p>";
            } else {
                echo "<p style=\"text-align: left; font-size: 0.9rem; padding-left: 15px;\" class=\"card-text\">Online há " . $diff_in_seconds . " segundos</p>";
            }
        }
        echo "</div></div>";
    }
} else {
    echo "<p>Nenhum usuário encontrado.</p>";
}
$sqlCount = "SELECT COUNT(*) AS total\r\n                                     FROM api_online o\r\n                                     INNER JOIN accounts a ON o.byid = a.id\r\n                                     WHERE o.byid = '" . $sessao_iduser . "' AND o.login LIKE '%" . $searchName . "%'";
$resultCount = $conexao->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalResults = $rowCount["total"];
$totalPages = ceil($totalResults / $resultsPerPage);
$maxVisibleButtons = 5;
$firstVisibleButton = max(1, $page - floor($maxVisibleButtons / 2));
$lastVisibleButton = min($totalPages, $firstVisibleButton + $maxVisibleButtons - 1);
echo "                 </div>\r\n                    </div>\r\n                        </div\r\n                        <!-- Botões de navegação -->\r\n                        <nav aria-label=\"Paginação\">\r\n                            <ul class=\"pagination justify-content-center my-4\">\r\n                                <!-- Link para página anterior -->\r\n                                ";
if (1 < $page) {
    echo "                                    <li class=\"page-item mx-4\">\r\n                                        <a class=\"page-link\" href=\"?searchName=";
    echo $searchName;
    echo "&page=";
    echo $page - 1;
    echo "\">\r\n                                            Anterior\r\n                                        </a>\r\n                                    </li>\r\n                                ";
}
echo "                        \r\n                                <!-- Links das páginas -->\r\n                                ";
for ($i = $firstVisibleButton; $i <= $lastVisibleButton; $i++) {
    echo "                                    <li class=\"page-item";
    echo $page == $i ? " active" : "";
    echo "\">\r\n                                        <a class=\"page-link\" href=\"?searchName=";
    echo $searchName;
    echo "&page=";
    echo $i;
    echo "\">";
    echo $i;
    echo "</a>\r\n                                    </li>\r\n                                ";
}
echo "                        \r\n                                <!-- Link para próxima página -->\r\n                                ";
if ($page < $totalPages) {
    echo "                                    <li class=\"page-item mx-4\">\r\n                                        <a class=\"page-link\" href=\"?searchName=";
    echo $searchName;
    echo "&page=";
    echo $page + 1;
    echo "\">\r\n                                            Próxima\r\n                                        </a>\r\n                                    </li>\r\n                                ";
}
echo "                            </ul>\r\n                        </nav>\r\n\r\n\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n   <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script>\r\n    \$(document).ready(function() {\r\n      // Adicione a funcionalidade de busca dinâmica na tabela\r\n      \$('#searchInput').on('keyup', function() {\r\n        var value = \$(this).val().toLowerCase();\r\n        \$('.usuario').each(function() {\r\n          var text = \$(this).find('.card-text').text().toLowerCase();\r\n          \$(this).toggle(text.includes(value));\r\n        });\r\n      });\r\n    });\r\n    </script>\r\n\r\n   \r\n</body>\r\n\r\n</html>";

?>