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
if (isset($_POST["id"])) {
    $id = $_POST["id"];
    $sql = "DELETE FROM cupom WHERE id = " . $id;
    if ($conexao->query($sql) === true) {
        $_SESSION["cupon3"] = "<div>Cupom excluído com sucesso!</div>";
    } else {
        $_SESSION["cupon2"] = "<div>Erro ao excluir o cupom.</div>";
    }
    $stmt = $conexao->prepare("SELECT title FROM config");
    $stmt->execute();
    $stmt->bind_result($title);
    $stmt->fetch();
    $stmt->close();
    $titulo = $title;
}
echo "\r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                        <h2>Lista de Cupons</h2>\r\n                                   \r\n                        <br>\r\n                        <br>\r\n\r\n                        ";
if (isset($_SESSION["cupon3"])) {
    echo "<script>Swal.fire({\r\n                                        icon: 'success', \r\n                                        html: '" . $_SESSION["cupon3"] . "'\r\n                                    }).then(() => {\r\n                                        window.location.href = '" . $_SERVER["HTTP_REFERER"] . "';\r\n                                    });</script>";
    unset($_SESSION["cupon3"]);
}
echo "                        \r\n                             ";
if (isset($_SESSION["cupon1"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["cupon1"] . "' });</script>";
    unset($_SESSION["cupon1"]);
}
echo "                                \r\n                                   ";
if (isset($_SESSION["cupon2"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["cupon2"] . "' });</script>";
    unset($_SESSION["cupon2"]);
}
echo "                       <div class=\"d-grid gap-2 d-md-flex justify-content-md-end\">\r\n                          <a class=\"btn btn-primary\" style=\"background-color: #007bff; border: none;\" href=\"criar_cupom.php\" role=\"button\">Novo Cupom</a>\r\n                        </div>\r\n\r\n                        <br>\r\n                       ";
$sql = "SELECT * FROM cupom WHERE byid = " . $_SESSION["iduser"];
$result = $conexao->query($sql);
echo "                        \r\n                        <div class=\"table-responsive\">\r\n                            <table id=\"usuario\" class=\"table table-hover\">\r\n                                <thead class=\"table-dark\">\r\n                                    <tr>\r\n                                        <th>Cupom</th>\r\n                                        <th>Tipo</th>\r\n                                        <th>Valor</th>\r\n                                        <th>Validade</th>\r\n                                        <th>Limite</th>\r\n                                        <th>Restantes</th>\r\n                                        <th>Ações</th>\r\n                                    </tr>\r\n                                </thead>\r\n                                <tbody>\r\n                                    ";
if ($result && 0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["codigo"] . "</td>";
        echo "<td>" . $row["tipo"] . "</td>";
        echo "<td>" . $row["valor"] . "</td>";
        echo "<td class=\"p-3\">" . $row["data_validade"] . "</td>";
        echo "<td>" . $row["usos_maximos"] . "</td>";
        echo "<td>" . $row["usos_restantes"] . "</td>";
        echo "<td>\r\n                                                    <form action=\"\" method=\"post\">\r\n                                                        <input type=\"hidden\" name=\"id\" value=\"" . $row["id"] . "\">\r\n                                                        <button class=\"btn btn-danger\" type=\"submit\">Apagar</button>\r\n                                                    </form>\r\n                                                  </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan=\"7\">Nenhum cupom encontrado.</td></tr>";
}
echo "                                </tbody>\r\n                            </table>\r\n                        </div>\r\n\r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n  <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>