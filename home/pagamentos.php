<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] != 1) {
    echo "<script> window.location.href='../logout.php'; </script>";
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '../logout.php'; </script>";
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    echo "<script> window.location.href = '../logout.php'; </script>";
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
include "../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n \r\n    ";
include "../menuuser.php";
echo "            \r\n \r\n            \r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                      <center>\r\n            <h2 class=\"card-title\">Lista seus pagamentos</h2>\r\n          </center>\r\n          <br>\r\n          <br>\r\n          <br>\r\n         \r\n          <!-- Inicio -->\r\n          \r\n\r\n    \r\n                <div class=\"table-responsive\">\r\n                    <div class=\"input-group mb-3\">\r\n                        <input type=\"text\" class=\"form-control\" id=\"searchInput\" placeholder=\"Buscar por ID do pagamento ou data (YYYY-MM-DD)\">\r\n                    </div>\r\n                \r\n                    <table id=\"usuario\" class=\"table table-hover\">\r\n                        <thead class=\"table-dark\">\r\n                            <tr>\r\n                                <th>Login</th>\r\n                                <th>Valor</th>\r\n                                <th>Data</th>\r\n                                <th>N° PEDIDO:</th>\r\n                                <th>Status</th>\r\n                            </tr>\r\n                        </thead>\r\n                        <tbody>\r\n                            ";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$resultsPerPage = 10;
$startFrom = ($page - 1) * $resultsPerPage;
$sql = "SELECT login, valor, status, data_pagamento, payment_id FROM pagamentos WHERE iduser = " . $_SESSION["iduser"] . " LIMIT " . $startFrom . ", " . $resultsPerPage;
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["login"] . "</td>";
        echo "<td>" . $row["valor"] . "</td>";
        echo "<td>" . $row["data_pagamento"] . "</td>";
        echo "<td>" . $row["payment_id"] . "</td>";
        echo "<td>";
        if ($row["status"] == "APROVADO") {
            echo "<button id=\"btn-" . $row["payment_id"] . "\" type=\"button\" class=\"btn btn-primary btn-sm\">" . $row["status"] . "</button>";
        } else {
            echo "<button id=\"btn-" . $row["payment_id"] . "\" type=\"button\" class=\"btn btn-danger btn-sm\">" . $row["status"] . "</button>";
        }
        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan=\"5\">Nenhum pagamento encontrado.</td></tr>";
}
$sqlCount = "SELECT COUNT(*) AS total FROM pagamentos WHERE iduser = " . $_SESSION["iduser"];
$resultCount = $conexao->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalResults = $rowCount["total"];
$totalPages = ceil($totalResults / $resultsPerPage);
echo "                \r\n                        </tbody>\r\n                    </table>\r\n                    <nav aria-label=\"Paginação\">\r\n                        <ul class=\"pagination justify-content-center my-3\">\r\n                            ";
if (1 < $page) {
    echo "                                 <li class=\"page-item mx-4\">\r\n                                    <a class=\"page-link\" href=\"?page=";
    echo $page - 1;
    echo "\">\r\n                                        Anterior\r\n                                    </a>\r\n                                </li>\r\n                                ";
}
for ($i = 1; $i <= $totalPages; $i++) {
    echo "                                <li class=\"page-item ";
    echo $i == $page ? "active" : "";
    echo "\">\r\n                                    <a class=\"page-link\" href=\"?page=";
    echo $i;
    echo "\">";
    echo $i;
    echo "</a>\r\n                                </li>\r\n                                ";
}
if ($page < $totalPages) {
    echo "                                <li class=\"page-item mx-4\">\r\n                                    <a class=\"page-link\" href=\"?page=";
    echo $page + 1;
    echo "\">\r\n                                        Próxima\r\n                                    </a>\r\n                                </li>\r\n                                ";
}
echo "                        </ul>\r\n                    </nav>\r\n                </div>\r\n                \r\n                ";
$conexao->close();
echo "       \r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n  <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    \r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n    <script>\r\n        document.addEventListener('contextmenu', function(event) {\r\n            event.preventDefault();\r\n        });\r\n    </script>\r\n\r\n\r\n</body>\r\n\r\n</html>";

?>