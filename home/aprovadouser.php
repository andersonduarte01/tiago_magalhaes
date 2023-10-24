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
    echo "<script> window.location.href = '..logout.php'; </script>";
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
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$sql = "SELECT * FROM ssh_accounts WHERE id = '" . $_SESSION["iduser"] . "'";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION["validade"] = $row["expira"];
        $_SESSION["limite"] = $row["limite"];
        $_SESSION["byid"] = $row["byid"];
    }
}
$sql = "SELECT * FROM accounts WHERE id = '" . $_SESSION["byid"] . "'";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION["valorusuario"] = $row["valorusuario"];
        $valorusuario = $row["valorusuario"];
        if ($valorusuario == 0) {
            echo "<script>alert(\"Seu Revendedor Não esta cadrastado em nossa Plataforma\");</script><script>window.location.href = \"index.php\";</script>";
        }
    }
}
$data = $_SESSION["validade"];
$data = date("d/m/Y", strtotime($data));
$limite = $_SESSION["limite"];
$_SESSION["limite"] = $limite;
$valor = $limite * $valorusuario;
$_SESSION["valor"] = $valor;
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$valor = $_SESSION["valor"];
$dias = $_SESSION["validade"];
$dias = date("d/m/Y H:i:s", strtotime($dias));
$dias = explode("/", $dias);
$dias = $dias[2] . "-" . $dias[1] . "-" . $dias[0];
$dias = strtotime($dias);
$hoje = strtotime(date("Y-m-d"));
$dias = floor(($dias - $hoje) / 86400);
$totatl = $dias + 30;
$_SESSION["totatl"] = $totatl;
echo "        \r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n \r\n       ";
include "../menuuser.php";
echo "       \r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-5\">\r\n                        <!-- Inicio -->\r\n\r\n                                    <style>\r\n                            .bloco img {\r\n                                width: 190px;\r\n                                height: 190px;\r\n                            }\r\n                        </style>\r\n                        <div id=\"texto\" class=\"container\">\r\n                            <div id=\"bloco\" class=\"bloco\">\r\n                                <h1>APROVADO</h1>\r\n                                <p>Seu pagamento foi aprovado</p>\r\n                                <img src=\"https://www.pngplay.com/wp-content/uploads/2/Approved-PNG-Photos.png\"\r\n                                    alt=\"aprovado\">\r\n                            </div><br><br>\r\n\r\n\r\n                            <h4>Seu login é: <span id=\"login\">\r\n                                    ";
echo $_SESSION["login"];
echo "                                </span></h4>\r\n                            <h5>Vencimento:<span id=\"vencimento\">\r\n                                    ";
echo " " . $data;
echo "                                </span></h5>\r\n                            <h5>Limite:<span id=\"limite\">\r\n                                    ";
echo " " . $limite;
echo "                                </span></h5>\r\n                            <h5>Mensalidade:<span id=\"valor\">\r\n                                    ";
echo " " . $valor;
echo "                                </span></h5>\r\n                            <button type=\"button\" class=\"btn btn-primary\"\r\n                                onclick=\"copyToClipboard()\">Copiar</button>\r\n\r\n\r\n                            <br>\r\n                            <br>\r\n                        </div>\r\n            \r\n                    \r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        \r\n\r\n    \r\n    </main>\r\n\r\n\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    </script>\r\n    <script>\r\n        document.addEventListener('contextmenu', function(event) {\r\n            event.preventDefault();\r\n        });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>