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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $ip = $_POST["ip"];
    $porta = $_POST["porta"];
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];
    $subid = $_POST["subid"];
    if (!checkServerOnline($ip, $porta)) {
        $_SESSION["servdor2"] = "<div>O servidor está offline. Verifique a conexão e tente novamente.</div>";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if (!checkCredentials($ip, $porta, $usuario, $senha)) {
        $_SESSION["servdor2"] = "<div>As credenciais de acesso ao servidor estão incorretas. Verifique os dados e tente novamente.</div>";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    $query = "UPDATE servidores SET ip = '" . $ip . "', porta = '" . $porta . "', usuario = '" . $usuario . "', senha = '" . $senha . "', subid = " . $subid . " WHERE id = " . $id;
    if ($conexao->query($query)) {
        $_SESSION["servdor1"] = "<div>Servidor, editado com sucesso!</div>";
        header("Location: listar.servidor.php");
        exit;
    }
    echo "Erro ao editar servidor: " . $conexao->error;
} else {
    $id = $_GET["id"];
    $query = "SELECT * FROM servidores WHERE id = " . $id;
    $resultado = $conexao->query($query);
    if ($resultado->num_rows == 1) {
        $servidor = $resultado->fetch_assoc();
        $query = "SELECT * FROM categorias";
        $resultado = $conexao->query($query);
        $categorias = [];
        while ($categoria = $resultado->fetch_assoc()) {
            $categorias[] = $categoria;
        }
    } else {
        echo "Servidor não encontrado.";
        exit;
    }
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
echo "\r\n<!-- Inicio -->\r\n\r\n\r\n\r\n          \r\n                           <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-5\">\r\n                        <!-- Inicio -->\r\n\r\n                           <h2 class=\"card-title\">Editar Servidor</h2>\r\n                                <br>\r\n                                <br>\r\n                                \r\n                                 ";
if (isset($_SESSION["servdor2"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["servdor2"] . "' });</script>";
    unset($_SESSION["servdor2"]);
}
echo "\r\n                        <!-- Inicio -->\r\n                        <center>\r\n                           \r\n                            <form class=\"login100-form validate-form\" action=\"editar_servidor.php\" method=\"post\" onsubmit=\"return validateForm();\">\r\n                                <input type=\"hidden\" name=\"id\" value=\"";
echo $servidor["id"];
echo "\">\r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Nome do servidor</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"text\" class=\"form-control\" id=\"ip\" name=\"ip\" value=\"";
echo $servidor["nome"];
echo "\">\r\n                                </div>\r\n                                \r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Ip do servidor</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"text\" class=\"form-control\" id=\"ip\" name=\"ip\" value=\"";
echo $servidor["ip"];
echo "\">\r\n                                </div>\r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Porta do servidor</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"text\" class=\"form-control\" id=\"porta\" name=\"porta\" value=\"";
echo $servidor["porta"];
echo "\">\r\n                                </div>\r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Usuario do servidor</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"text\" class=\"form-control\" id=\"usuario\" name=\"usuario\" value=\"";
echo $servidor["usuario"];
echo "\">\r\n                                </div>\r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Senha do servidor</h8>\r\n                                <div class=\"form-group\">\r\n                                    <input type=\"password\" class=\"form-control\" id=\"senha\" name=\"senha\" value=\"";
echo $servidor["senha"];
echo "\">\r\n                                </div>\r\n                                 <h8 class=\"card-title\" style=\"font-family: Arial;\">Categoria</h8>\r\n                                <div class=\"form-group\">\r\n                                    <select class=\"form-control\" id=\"subid\" name=\"subid\">\r\n                                        ";
foreach ($categorias as $categoria) {
    echo "                                            <option value=\"";
    echo $categoria["subid"];
    echo "\" ";
    if ($categoria["subid"] == $servidor["subid"]) {
        echo "selected";
    }
    echo ">";
    echo $categoria["nome"];
    echo "</option>\r\n                                        ";
}
echo "                                    </select>\r\n                                </div>\r\n                                 <a class=\"btn btn-danger\" href=\"listar.servidor.php\">Cancelar</a>\r\n                                <button type=\"submit\" class=\"btn btn-primary\">Salvar</button>\r\n                               \r\n                            </form>\r\n                        </center>\r\n                    </div>\r\n                </div>\r\n        </div>\r\n       \r\n    </main>\r\n\r\n\r\n\r\n      <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n\r\n</body>\r\n\r\n</html>";
function checkServerOnline($ip, $porta)
{
    $connection = ssh2_connect($ip, $porta);
    if (!$connection) {
        return false;
    }
    return true;
}
function checkCredentials($ip, $porta, $usuario, $senha)
{
    $connection = ssh2_connect($ip, $porta);
    if (!$connection) {
        return false;
    }
    if (!ssh2_auth_password($connection, $usuario, $senha)) {
        return false;
    }
    return true;
}

?>