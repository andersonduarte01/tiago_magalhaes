<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if (mysqli_connect_errno()) {
    exit("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    header("Location: ../logout.php");
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../logout.php");
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    header("Location: ../logout.php");
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
echo "\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n  <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n    ";
include "../menu.php";
echo "\r\n      <div class=\"container-fluid py-4\">\r\n        <div class=\"col-lg-14\">\r\n          <!-- Inicio -->\r\n          <div class=\"card card-plain mt-4\">\r\n            <center>\r\n              <h3>CheckUser</h3>\r\n              <br>\r\n         \r\n            <h6>CheckUser do Studio</h6>\r\n             <div class=\"form-group mb-4\">\r\n                <div class=\"input-group\">\r\n                    <input type=\"text\" class=\"form-control\" id=\"studio\" name=\"studio\" value=\"";
echo "https://" . $_SERVER["HTTP_HOST"] . "/checkuser/miracle/gordon/checkuser.php";
echo "\" readonly required>\r\n                    <button class=\"btn\" onclick=\"copyToClipboard('#studio')\">Copiar</button>\r\n                </div>\r\n            </div>\r\n        \r\n            <h6>CheckUser do Miracle 1</h6>\r\n            <div class=\"form-group mb-4\">\r\n                <div class=\"input-group\">\r\n                    <input type=\"text\" class=\"form-control\" id=\"Miracle1\" name=\"Miracle1\" value=\"";
echo "https://" . $_SERVER["HTTP_HOST"] . "/checkuser";
echo "\" readonly required>\r\n                    <button class=\"btn\" onclick=\"copyToClipboard('#Miracle1')\">Copiar</button>\r\n                </div>\r\n            </div>\r\n            \r\n            <h6>CheckUser do Miracle 2</h6>\r\n            <div class=\"form-group mb-4\">\r\n                <div class=\"input-group\">\r\n                    <input type=\"text\" class=\"form-control\" id=\"Miracle2\" name=\"Miracle2\" value=\"";
echo "https://" . $_SERVER["HTTP_HOST"] . "/checkuser/miracle/gordon/checkuser.php";
echo "\" readonly required>\r\n                    <button class=\"btn\" onclick=\"copyToClipboard('#Miracle2')\">Copiar</button>\r\n                </div>\r\n            </div>\r\n            \r\n             <h6>CheckUser do AnyVpn</h6>\r\n            <div class=\"form-group mb-4\">\r\n                <div class=\"input-group\">\r\n                    <input type=\"text\" class=\"form-control\" id=\"AnyVpn\" name=\"AnyVpn\" value=\"";
echo "https://" . $_SERVER["HTTP_HOST"] . "/checkuser/checkany.php";
echo "\" readonly required>\r\n                    <button class=\"btn\" onclick=\"copyToClipboard('#AnyVpn')\">Copiar</button>\r\n                </div>\r\n            </div>\r\n        \r\n\r\n            </center>\r\n          </div>\r\n          <!--FIM-->\r\n        </div>\r\n      </div>\r\n    </div>\r\n  </main>\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    <script>\r\n    function copyToClipboard(elementId) {\r\n      var copyText = document.querySelector(elementId);\r\n      copyText.select();\r\n      copyText.setSelectionRange(0, 99999); /* For mobile devices */\r\n      document.execCommand(\"copy\");\r\n    }\r\n    </script>\r\n\r\n</body>\r\n\r\n</html>\r\n";

?>