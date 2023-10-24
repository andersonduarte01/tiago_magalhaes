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
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n<style>\r\n.letra-icon {\r\n    font-size: 28px;\r\n    border-radius: 50%;\r\n    border: 1px solid #d9dbdf;\r\n    background-color: #d9dbdf; /* Defina a cor de fundo desejada */\r\n    display: inline-block; /* Garante que o background-color abranja todo o espaço do ícone */\r\n    width: 44px; /* Define a largura igual à font-size para tornar o ícone circular */\r\n    height: 45px; /* Define a altura igual à font-size para tornar o ícone circular */\r\n    text-align: center; /* Centraliza o conteúdo do ícone verticalmente */\r\n    line-height: 45px; /* Centraliza o conteúdo do ícone horizontalmente */\r\n    color: #1c1c1d; /* Define a cor do texto do ícone */\r\n}\r\n\r\n.espaco {\r\n    width: 20px;\r\n}\r\n.cor-escura {\r\n    background-color: #dee5ee; /* Defina a cor escura desejada */\r\n}\r\n\r\n</style>\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-7\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                            ";
if (isset($_SESSION["revenda"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["revenda"] . "' });</script>";
    unset($_SESSION["revenda"]);
}
echo "\r\n                        <h2 class=\"card-title\">Lista de revendas criadas</h2>\r\n                        </center>\r\n                        <!-- Inicio -->\r\n                        <form class=\"form-inline my-3\" action=\"\" method=\"GET\">\r\n                            <div class=\"input-group\">\r\n                                <input type=\"text\" id=\"searchInput\" class=\"form-control\" placeholder=\"Buscar por nome\">\r\n                                ";
if (isset($_GET["showAll"]) && $_GET["showAll"] == "true") {
    echo "                                    <div class=\"input-group-append\">\r\n                                        <a href=\"?searchName=";
    echo $searchName;
    echo "&page=1\" class=\"btn btn-secondary\">Deslistar Todos</a>\r\n                                    </div>\r\n                                ";
} else {
    echo "                                    <div class=\"input-group-append\">\r\n                                        <a href=\"?searchName=";
    echo $searchName;
    echo "&page=1&showAll=true\" class=\"btn btn-primary\">Listar Todos</a>\r\n                                    </div>\r\n                                ";
}
echo "                            </div>\r\n                        </form>\r\n                            <div class=\"col-12 mb-3\">\r\n                                <div class=\"card card-sm\">\r\n                                    <div class=\"card-body1 p-2\">\r\n                                        \r\n                                        ";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$resultsPerPage = 10;
$startFrom = ($page - 1) * $resultsPerPage;
$iduser = $_SESSION["iduser"];
$sql = "SELECT id, login, senha, byid FROM accounts WHERE byid = '" . $iduser . "'";
if (isset($_GET["searchName"]) && !empty($_GET["searchName"])) {
    $searchName = $_GET["searchName"];
    $sql .= " AND login LIKE '%" . $searchName . "%'";
}
if (!(isset($_GET["showAll"]) && $_GET["showAll"] == "true")) {
    $sql .= " LIMIT " . $startFrom . ", " . $resultsPerPage;
}
$resultado = mysqli_query($conexao, $sql);
$total_users = mysqli_num_rows($resultado);
if (0 < $total_users) {
    while ($account = mysqli_fetch_assoc($resultado)) {
        $nome = $account["login"];
        $primeira_letra = strtoupper(substr($nome, 0, 1));
        $id = $account["id"];
        $sql_byid = "SELECT COUNT(*) as total_byid FROM accounts WHERE byid = '" . $id . "'";
        $resultado_byid = mysqli_query($conexao, $sql_byid);
        $total_byid = mysqli_fetch_assoc($resultado_byid)["total_byid"];
        $sql_ssh = "SELECT id, login, byid FROM ssh_accounts WHERE byid = '" . $id . "'";
        $resultado_ssh = mysqli_query($conexao, $sql_ssh);
        $total_ssh_accounts = mysqli_num_rows($resultado_ssh);
        echo "                                                <a href=\"atribuicao.php?id=";
        echo $account["id"];
        echo "\">\r\n                                                    <div class=\"col-12 usuario\">\r\n                                                        <div class=\"card mb-2 cor-escura\">\r\n                                                            <div class=\"card-body d-flex align-items-center p-2\">\r\n                                                                <div class=\"revendedor-icon rounded-circle\">\r\n                                                                    <span class=\"letra-icon\">";
        echo $primeira_letra;
        echo "</span>\r\n                                                                </div>\r\n                                                                <div class=\"espaco\"></div>\r\n                                                                <div class=\"informacoes\">\r\n                                                                    <div class=\"d-flex\">\r\n                                                                        <i class=\"material-icons\">person</i>\r\n                                                                        <h6 class=\"card-title\">";
        echo $account["login"];
        echo "</h6>\r\n                                                                    </div>\r\n                                                                    <div class=\"d-flex\">\r\n                                                                        <i class=\"material-icons\">lock</i>\r\n                                                                        <h6 class=\"card-text\">";
        echo $account["senha"];
        echo "</h6>\r\n                                                                    </div>\r\n                                                                </div>\r\n                                                                <img src=\"https://cdn-icons-png.flaticon.com/512/3064/3064224.png\" style=\"position: absolute; height: 65px; right: 2px; opacity: 0.3;\">\r\n                                                            </div>\r\n                                                            <div>\r\n                                                                <p style=\"text-align: left; padding-left: 15px;\" class=\"card-text\">";
        echo $total_byid;
        echo " Revendas criadas | ";
        echo $total_ssh_accounts;
        echo " Acesso criado</p>\r\n                                                            </div>\r\n                                                        </div>\r\n                                                    </div>\r\n                                                </a>\r\n                                                ";
    }
} else {
    echo "<p>Nenhuma revenda cadastrada.</p>";
}
echo "                                    </div>\r\n                                </div>\r\n                            </div>\r\n                            \r\n                            <!-- Paginação -->\r\n                            <nav aria-label=\"Paginação\">\r\n                                <ul class=\"pagination justify-content-center my-3\">\r\n                                    ";
$sqlCount = "SELECT COUNT(*) AS total FROM accounts WHERE byid = '" . $iduser . "'";
if (isset($_GET["searchName"]) && !empty($_GET["searchName"])) {
    $searchName = $_GET["searchName"];
    $sqlCount .= " AND login LIKE '%" . $searchName . "%'";
}
if (!(isset($_GET["showAll"]) && $_GET["showAll"] == "true")) {
    $resultCount = mysqli_query($conexao, $sqlCount);
    $rowCount = mysqli_fetch_assoc($resultCount);
    $totalResults = $rowCount["total"];
    $totalPages = ceil($totalResults / $resultsPerPage);
    if (1 < $page) {
        echo "                                            <li class=\"page-item mx-2\">\r\n                                                <a class=\"page-link\" href=\"?page=";
        echo $page - 1;
        echo "&showAll=";
        echo isset($_GET["showAll"]) ? $_GET["showAll"] : "false";
        echo "\">Anterior</a>\r\n                                            </li>\r\n                                            ";
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "                                            <li class=\"page-item ";
        echo $i == $page ? "active" : "";
        echo "\">\r\n                                                <a class=\"page-link\" href=\"?page=";
        echo $i;
        echo "&showAll=";
        echo isset($_GET["showAll"]) ? $_GET["showAll"] : "false";
        echo "\">";
        echo $i;
        echo "</a>\r\n                                            </li>\r\n                                            ";
    }
    if ($page < $totalPages) {
        echo "                                            <li class=\"page-item mx-2\">\r\n                                                <a class=\"page-link\" href=\"?page=";
        echo $page + 1;
        echo "&showAll=";
        echo isset($_GET["showAll"]) ? $_GET["showAll"] : "false";
        echo "\">Próxima</a>\r\n                                            </li>\r\n                                            ";
    }
}
echo "                                </ul>\r\n                            </nav>\r\n                            <hr>\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n      <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n    \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca dinâmica na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n            var value = \$(this).val().toLowerCase();\r\n            \$('.usuario').filter(function() {\r\n                var text = \$(this).find('.card-title').text().toLowerCase();\r\n                \$(this).toggle(text.includes(value));\r\n            });\r\n        });\r\n    });\r\n</script>\r\n   \r\n</body>\r\n\r\n</html>";

?>