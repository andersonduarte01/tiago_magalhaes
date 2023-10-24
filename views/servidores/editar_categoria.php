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
if (isset($_POST["submit"])) {
    $subid = $_POST["subid"];
    $novo_id = $_POST["novo_id"];
    $nome = $_POST["nome"];
    $sql = "SELECT * FROM categorias WHERE subid = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $novo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $categoria_id_existente = $result->fetch_assoc();
    if ($categoria_id_existente && $categoria_id_existente["subid"] != $subid) {
        $_SESSION["categoria2"] = "<div>O ID da categoria já existe..</div>";
        header("Location: listar_categorias.php");
        exit;
    }
    $sql = "SELECT * FROM categorias WHERE nome = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $nome);
    $stmt->execute();
    $result = $stmt->get_result();
    $categoria_nome_existente = $result->fetch_assoc();
    if ($categoria_nome_existente && $categoria_nome_existente["subid"] != $subid) {
        $_SESSION["categoria2"] = "<div>O nome da categoria já existe.</div>";
        header("Location: listar_categorias.php");
        exit;
    }
    $sql = "UPDATE categorias SET subid = ?, nome = ? WHERE subid = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("isi", $novo_id, $nome, $subid);
    if ($stmt->execute()) {
        $_SESSION["categoria1"] = "<div>Categoria, editado com sucesso!</div>";
        header("Location: listar_categorias.php");
    } else {
        echo "Erro ao atualizar a categoria: " . $stmt->error;
    }
} else {
    $subid = $_GET["subid"];
    $sql = "SELECT * FROM categorias WHERE subid = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $subid);
    $stmt->execute();
    $result = $stmt->get_result();
    $categoria = $result->fetch_assoc();
    if (!$categoria) {
        echo "Categoria não encontrada.";
        exit;
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
echo "\r\n            \r\n<!-- Inicio -->\r\n\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-5\">\r\n                        <!-- Inicio -->\r\n                        <center>\r\n                            <h2 class=\"card-title\">Editar Categoria</h2>\r\n                        </center>\r\n                        <br>\r\n                        <br>\r\n\r\n                        <!-- Inicio -->\r\n                        <center>\r\n                             <form method=\"POST\" action=\"\">\r\n                                 <input type=\"hidden\" name=\"subid\" value=\"";
echo $categoria["subid"];
echo "\">\r\n                                  <h8 class=\"card-title\" style=\"font-family: Arial;\">ID da categoria</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"text\" class=\"form-control\" name=\"novo_id\" value=\"";
echo $categoria["subid"];
echo "\">\r\n                                </div>\r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Nome da categoria</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"text\" class=\"form-control\" name=\"nome\" value=\"";
echo $categoria["nome"];
echo "\">\r\n                                </div>\r\n                               \r\n                                <button type=\"submit\" name=\"submit\" class=\"btn btn-primary\">Salvar</button>\r\n                                <a class=\"btn btn-danger\" href=\"listar_categorias.php\">Cancelar</a>\r\n                            </form>\r\n                        </center>\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n        <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n\r\n</body>\r\n\r\n</html>";

?>