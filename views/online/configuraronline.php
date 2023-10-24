<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if (mysqli_connect_errno()) {
    exit("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    header("Location: ../../logout.php");
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../../logout.php");
    exit;
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    header("Location: ../../logout.php");
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
echo "\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n  <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n    ";
include "../../menu.php";
echo "\r\n    <!-- Navbar -->\r\n    \r\n    <style>\r\n        .btn-equal-width {\r\n    width: 100%;\r\n}\r\n\r\n    </style>\r\n\r\n      <div class=\"container-fluid py-4\">\r\n        <div class=\"col-lg-14\">\r\n          <!-- Inicio -->\r\n          <div class=\"card card-plain mt-4\">\r\n            <center>\r\n              <h3>Configuração do Online</h3>\r\n              <br>\r\n            \r\n              <div class=\"card-body\">\r\n                ";
if (isset($_SESSION["config"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["config"] . "' });</script>";
    unset($_SESSION["config"]);
}
echo "                ";
if (isset($_SESSION["configerr"])) {
    echo "<script>Swal.fire({ icon: 'error', html: '" . $_SESSION["configerr"] . "' });</script>";
    unset($_SESSION["configerr"]);
}
echo "\r\n           \r\n\r\n                <form class=\"login100-form validate-form\" action=\"\" method=\"post\" onsubmit=\"return validateForm();\">\r\n                    <div class=\"row\">\r\n                        <div class=\"col-md-10 text-center\">\r\n                            <h6>Instalar os módulos do online</h6>\r\n                            <div class=\"input-group-append mb-5\">\r\n                                <a href=\"instalar.php\" class=\"btn btn-primary btn-dm btn-equal-width\" style=\"background-color: #5e17eb; border: none;\" onclick=\"showLoading()\">Instalar</a>\r\n                            </div>\r\n                        </div>\r\n                \r\n                        <div class=\"col-md-10 text-center\">\r\n                            <h6>Ativar o Online</h6>\r\n                            <div class=\"input-group-append mb-5\">\r\n                                <a href=\"ativar.php\" class=\"btn btn-primary btn-dm btn-equal-width\" style=\"background-color: #5e17eb; border: none;\" onclick=\"showLoading()\">Ativar</a>\r\n                            </div>\r\n                        </div>\r\n                \r\n                        <div class=\"col-md-10 text-center\">\r\n                            <h6>Desativar o Online</h6>\r\n                            <div class=\"input-group-append mb-5\">\r\n                                <a href=\"desativar.php\" class=\"btn btn-primary btn-dm btn-equal-width\" style=\"background-color: #5e17eb; border: none;\" onclick=\"showLoading()\">Desativar</a>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                </form>\r\n\r\n              </div>\r\n            </center>\r\n          </div>\r\n          <!--FIM-->\r\n        </div>\r\n      </div>\r\n    </div>\r\n  </main>\r\n\r\n  <!--   Core JS Files   -->\r\n  <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n  <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n  <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n  <script src=\"../assets/js/menu.js\"></script>\r\n  <script src=\"../assets/js/page.js\"></script>\r\n    <script>\r\n        function showLoading() {\r\n            Swal.fire({\r\n                title: 'Carregando...',\r\n                html: '<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin fa-3x\"></i></div>',\r\n                showCancelButton: false,\r\n                showConfirmButton: false,\r\n                allowOutsideClick: false,\r\n                allowEscapeKey: false,\r\n                allowEnterKey: false\r\n            });\r\n        }\r\n    </script>\r\n \r\n</body>\r\n\r\n</html>\r\n";

?>