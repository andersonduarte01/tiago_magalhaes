<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../config/conexao.php";
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
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
echo "<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                        <h2 class=\"card-title\">Lista de Categorias</h2><br><br>\r\n        \r\n                                ";
if (isset($_SESSION["categoria1"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["categoria1"] . "' });</script>";
    unset($_SESSION["categoria1"]);
}
echo "                                \r\n                                   ";
if (isset($_SESSION["categoria2"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["categoria2"] . "' });</script>";
    unset($_SESSION["categoria2"]);
}
echo "\r\n                        <div class=\"d-grid gap-2 d-md-flex justify-content-md-end\">\r\n                            <a class=\"btn btn-primary\" style=\"background-color: #007bff; border: none;\"\r\n                                href=\"adicionar_categoria.php\" role=\"button\">NOVA CATEGORIA</a>\r\n                                <a class=\"btn btn-primary\" style=\"background-color: #007bff; border: none;\"\r\n                                href=\"listar.servidor.php\" role=\"button\">SERVIDORES</a>\r\n                        </div>\r\n                        <br>\r\n\r\n                          ";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$resultsPerPage = isset($_GET["showAll"]) ? 1000 : 10;
$startFrom = ($page - 1) * $resultsPerPage;
$sql = "SELECT * FROM categorias LIMIT " . $startFrom . ", " . $resultsPerPage;
$resultado = mysqli_query($conexao, $sql);
$sqlCount = "SELECT COUNT(*) AS total FROM categorias";
$resultCount = mysqli_query($conexao, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalResults = $rowCount["total"];
$totalPages = ceil($totalResults / $resultsPerPage);
echo "                                    \r\n                                  \r\n                                    <form class=\"form-inline my-3\" action=\"\" method=\"GET\">\r\n                                        <div class=\"input-group\">\r\n                                            <input type=\"text\" id=\"searchInput\" class=\"form-control\" placeholder=\"Buscar por nome\">\r\n                                            ";
if (isset($_GET["showAll"])) {
    echo "                                                <div class=\"input-group-append\">\r\n                                                    <a href=\"?searchName=";
    echo $searchName;
    echo "&page=1\" class=\"btn btn-secondary\">Deslistar Todos</a>\r\n                                                </div>\r\n                                            ";
} else {
    echo "                                                <div class=\"input-group-append\">\r\n                                                    <a href=\"?searchName=&page=1&showAll\" class=\"btn btn-primary\">Listar Todos</a>\r\n                                                </div>\r\n                                            ";
}
echo "                                        </div>\r\n                                    </form>\r\n                                     <div class=\"table-responsive\">\r\n                                        <table id=\"usuario\" class=\"table table-hover\">\r\n                                            <thead\">\r\n                                                <tr>\r\n                                                    <th>Nome</th>\r\n                                                    <th>Id da categoria</th>\r\n                                                    <th>Editar</th>\r\n                                                    <th>Excluir</th>\r\n                                                </tr>\r\n                                            </thead>\r\n                                            <tbody>\r\n                                                ";
while ($categoria = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $categoria["nome"] . "</td>";
    echo "<td>" . $categoria["subid"] . "</td>";
    echo "<td>";
    echo "<a href=\"editar_categoria.php?subid=" . $categoria["subid"] . "\" class=\"btn btn-primary btn-sm\">Editar</a>";
    echo "</td><td>";
    echo "<a href=\"excluir_categoria.php?id=" . $categoria["id"] . "\" onclick=\"return confirm('Tem certeza que deseja excluir?')\" class=\"btn btn-danger btn-sm\">Excluir</a>";
    echo "</td></tr>";
}
echo "                                            </tbody>\r\n                                        </table>\r\n                                    </div>\r\n                                        <nav aria-label=\"Paginação\">\r\n                                            <ul class=\"pagination justify-content-center my-3\">\r\n                                                ";
if (1 < $page) {
    echo "                                                   <li class=\"page-item mx-4\">\r\n                                                        <a class=\"page-link\" href=\"?page=";
    echo $page - 1;
    echo "\">\r\n                                                            Anterior\r\n                                                        </a>\r\n                                                    </li>\r\n                                                    ";
}
for ($i = 1; $i <= $totalPages; $i++) {
    echo "                                                    <li class=\"page-item ";
    echo $i == $page ? "active" : "";
    echo "\">\r\n                                                        <a class=\"page-link\" href=\"?page=";
    echo $i;
    echo "\">";
    echo $i;
    echo "</a>\r\n                                                    </li>\r\n                                                    ";
}
if ($page < $totalPages) {
    echo "                                                     <li class=\"page-item mx-4\">\r\n                                                        <a class=\"page-link\" href=\"?page=";
    echo $page + 1;
    echo "\">\r\n                                                            Próxima\r\n                                                        </a>\r\n                                                    </li>\r\n                                                    ";
}
echo "                                            </ul>\r\n                                        </nav>\r\n                                    \r\n\r\n                                  </div>\r\n\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n      <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n\r\n</body>\r\n\r\n</html>";

?>