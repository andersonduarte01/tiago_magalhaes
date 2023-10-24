<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";

// VALIDAÇÃO DE TOKEN E API FOI REMOVIDA - SEJAM FELIZES By: @eu_misterioso - Hospedagem para o painel apartir de 10 reais

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
            $_SESSION["loginerro"] = "<div>A função de pagamento automático ainda não está disponível. Por favor, entre em contato com o suporte para obter mais informações.</div>";
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
$id_usuario = $_SESSION["iduser"];
$sql = "SELECT SUM(valor) AS total_aprovado FROM pagamentos WHERE status = 'Aprovado' AND iduser = '" . $id_usuario . "'";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    $row = $result->fetch_assoc();
    $total_aprovado = $row["total_aprovado"];
}
$currentMonth = date("m");
$sql = "SELECT * FROM pagamentos WHERE iduser = " . $_SESSION["iduser"] . " AND MONTH(data_pagamento) = " . $currentMonth . " AND status = 'Pendente'";
$total = 0;
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $total += $row["valor"];
    }
}
echo "        \r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n \r\n    ";
include "../menuuser.php";
echo "\r\n            \r\n    <link href=\"https://fonts.googleapis.com/icon?family=Material+Icons\" rel=\"stylesheet\">\r\n            <div class=\"row\">\r\n                <div class=\"col-xl-12 col-sm-6 mb-xl-0 mb-4\"><br></div>\r\n\r\n                <b>Faturamento</b>\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                        <a style=\"cursor: pointer;\" href=\"../nivel.php\">\r\n                            <div class=\"card-body p-3\">\r\n                                <div class=\"row\">\r\n                                    <div class=\"col-10\">\r\n                                        <div class=\"numbers\">\r\n                                            <p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Aprovado</p>\r\n                                            <h5 class=\"font-weight-bolder mb-0\">\r\n                                                ";
echo "R\$: " . number_format($total_aprovado, 2, ",", ".");
echo "                                            </h5>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                     <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/aprovado.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n\r\n                                </div>\r\n                            </div>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                        <a style=\"cursor: pointer;\" href=\"../nivel.php\">\r\n                            <div class=\"card-body p-3\">\r\n                                <div class=\"row\">\r\n                                    <div class=\"col-10\">\r\n                                        <div class=\"numbers\">\r\n                                            <p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Pendente</p>\r\n                                            <h5 class=\"font-weight-bolder mb-0\">\r\n                                                ";
echo "R\$: " . number_format($total, 2, ",", ".");
echo "                                            </h5>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                     <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/pendente.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n\r\n                                </div>\r\n                            </div>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                     ";
if (isset($_SESSION["loginerro"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["loginerro"] . "', showConfirmButton: true }).then(() => { window.location.href = '../logout.php'; });</script>";
    unset($_SESSION["loginerro"]);
}
echo "\r\n\r\n\r\n                                    <span class=\"login100-form-title p-b-48\">\r\n                                        <h4 class=\"zmdi zmdi-font\" style=\"font-size: 20px; text-align: center;\">Seu\r\n                                            login é:\r\n                                            ";
echo $_SESSION["login"];
echo "                                        </h4>\r\n                                    </span>\r\n                                    <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Seu\r\n                                        vencimento é:\r\n                                        ";
echo " " . $data;
echo "                                    </h4>\r\n                                    <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Seu limite\r\n                                        é:\r\n                                        ";
echo " " . $limite;
echo "                                    </h4>\r\n                                    <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Sua\r\n                                        Mensalidade é:\r\n                                        ";
echo " R\$ " . $valor . ",00";
echo "                                    </h4>\r\n                                    <br>\r\n\r\n                         ";
$sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["byid"] . "'";
$result_atribuidos = $conexao->query($sql_atribuidos);
if ($_SESSION["byid"] == 1) {
    $impedir_envio = "";
} else {
    if (0 < $result_atribuidos->num_rows) {
        $atribuido = $result_atribuidos->fetch_assoc();
        if ($atribuido["tipo"] == "Credito") {
            if ($atribuido["limite"] <= 0) {
                $impedir_envio = "Swal.fire({\r\n                                                    icon: 'warning',\r\n                                                    title: 'Você não pode continuar.',\r\n                                                    text: 'Entre em contato com o suporte.'\r\n                                                  });\r\n                                                  return false;";
            } else {
                $impedir_envio = "";
            }
        } else {
            if ($atribuido["tipo"] == "Validade") {
                $expira = $atribuido["expira"];
                if (strtotime($expira) < time()) {
                    $impedir_envio = "Swal.fire({\r\n                                                    icon: 'warning',\r\n                                                    title: 'Você não pode continuar.',\r\n                                                    text: 'Entre em contato com o suporte.'\r\n                                                  });\r\n                                                  return false;";
                } else {
                    $impedir_envio = "";
                }
            } else {
                $impedir_envio = "Swal.fire({\r\n                                                  icon: 'error',\r\n                                                  title: 'Tipo de atribuição inválido',\r\n                                                  text: 'Contate o suporte.'\r\n                                                });\r\n                                                return false;";
            }
        }
    } else {
        $impedir_envio = "Swal.fire({\r\n                                                icon: 'error',\r\n                                                title: 'Erro ao verificar o limite',\r\n                                                text: 'Tente novamente mais tarde.'\r\n                                              });\r\n                                              return false;";
    }
}
echo "                        \r\n                        <form method=\"POST\" action=\"pagamento.php\" class=\"mt-4\" class=\"login100-form validate-form\" onsubmit=\"";
echo $impedir_envio;
echo "\">\r\n                          <button type=\"submit\" class=\"btn btn-primary\">Continuar</button>\r\n                        </form>\r\n               \r\n                        \r\n\r\n\r\n\r\n            \r\n            \r\n                    \r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        \r\n\r\n    \r\n    </main>\r\n\r\n\r\n\r\n  <!--   Core JS Files   -->\r\n    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    </script>\r\n    <script>\r\n        document.addEventListener('contextmenu', function(event) {\r\n            event.preventDefault();\r\n        });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>