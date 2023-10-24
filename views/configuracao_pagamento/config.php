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
$sql = "SELECT * FROM accounts WHERE id = '" . $_SESSION["iduser"] . "'";
$resultado = mysqli_query($conexao, $sql);
if (0 < mysqli_num_rows($resultado)) {
    $row = mysqli_fetch_assoc($resultado);
    $accesstoken = $row["accesstoken"];
    $valorrevenda = $row["valorrevenda"];
    $valorusuario = $row["valorusuario"];
} else {
    echo "Usuário não encontrado.";
}
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
mysqli_close($conexao);
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n  ";
include "../../menu.php";
echo "\r\n\r\n\r\n            <center>\r\n              <div class=\"container-fluid py-4\">\r\n                      <div class=\"col-lg-14\">\r\n                        <!-- Inicio -->\r\n                    \r\n                        <div class=\"card card-plain mt-4\">\r\n                            <h3>Configuração de pagamento</h3>\r\n                            <br>\r\n                  \r\n                  <center>\r\n                  \r\n                <div class=\"card-body\">\r\n                 <form class=\"login100-form validate-form\" action=\"updatepag.php\" method=\"post\">\r\n                  <h8>Caso não queira usar deixa essa caixa desmarcada e salva.</h8>\r\n                  \r\n                  <div class=\"form-group\">\r\n                      <input type=\"checkbox\" id=\"ativarCampos\"> Ativar/Desativar\r\n                  </div>\r\n\r\n                  <h5>Access token Mercado Pago</h5>\r\n                  <div class=\"form-group mb-4\">\r\n                    <div class=\"input-group\">\r\n                       <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                      <input type=\"text\" class=\"form-control\" name=\"accesstoken\" placeholder=\"  Access token\"\r\n                        value=\"";
echo $accesstoken;
echo "\" disabled required>\r\n                    </div>\r\n                  </div>\r\n\r\n\r\n                  <h6>Valor do Login Para Usuario Final</h6>\r\n                  <div class=\"form-group mb-4\">\r\n                    <div class=\"input-group\">\r\n                   <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                      <input type=\"text\" class=\"form-control\" name=\"valorusuario\"\r\n                        placeholder=\"  Valor do Login Para Usuario\" value=\"";
echo $valorusuario;
echo "\" disabled\r\n                        required>\r\n                    </div>\r\n                  </div>\r\n\r\n\r\n                  <h6>Valor de Cada Login Para Revenda</h6>\r\n                  <div class=\"form-group mb-4\">\r\n                    <div class=\"input-group\">\r\n                       <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                        <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                            viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                        </svg>\r\n                                    </span>\r\n                      <input type=\"text\" class=\"form-control\" name=\"valorrevenda\"\r\n                        placeholder=\"  Valor de Cada Login Para Revenda\" value=\"";
echo $valorrevenda;
echo "\" disabled\r\n                        required>\r\n                    </div>\r\n                  </div>\r\n\r\n\r\n                  <button class=\"btn btn-primary\" style=\"background-color: #007bff; border: none;\" name=\"salvar\" id=\"salvar\">\r\n                    Salvar\r\n                  </button>\r\n                  ";
if (isset($_POST["valorrevenda"]) && isset($_POST["valorusuario"]) && isset($_POST["accesstoken"])) {
}
echo "                </form>\r\n\r\n                <script>\r\n                  const checkbox = document.getElementById('ativarCampos');\r\n                  const campos = document.querySelectorAll('input[type=\"text\"]');\r\n\r\n                  checkbox.addEventListener('change', () => {\r\n                    campos.forEach(campo => {\r\n                      campo.disabled = !checkbox.checked;\r\n                    });\r\n                  });\r\n                </script>\r\n                            </div>\r\n                        </div>\r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n      <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>