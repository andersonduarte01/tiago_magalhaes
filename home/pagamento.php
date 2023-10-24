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
if ($_SESSION["byid"] != 1) {
    $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["byid"] . "'";
    $result_atribuidos = $conexao->query($sql_atribuidos);
    if (0 < $result_atribuidos->num_rows) {
        $atribuido = $result_atribuidos->fetch_assoc();
        if ($atribuido["tipo"] == "Credito") {
            if ($atribuido["limite"] <= 0) {
                header("Location: user.php");
                exit;
            }
        } else {
            if ($atribuido["tipo"] == "Validade") {
                $expira = $atribuido["expira"];
                if (strtotime($expira) < time()) {
                    header("Location: user.php");
                    exit;
                }
            } else {
                header("Location: user.php");
                exit;
            }
        }
    } else {
        header("Location: user.php");
        exit;
    }
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
$data = $_SESSION["validade"];
$data = date("d/m/Y", strtotime($data));
$limite = $_SESSION["limite"];
$valorusuario = $_SESSION["valorusuario"];
$valor = $valorusuario * $limite;
$revendedor_id = $_SESSION["byid"];
if (isset($_POST["cupom"])) {
    $cupom = mysqli_real_escape_string($conexao, $_POST["cupom"]);
    $stmt = $conexao->prepare("SELECT * FROM cupom WHERE codigo = ? AND byid = ?");
    $stmt->bind_param("si", $cupom, $revendedor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        $usos_restantes = $row["usos_restantes"];
        if (0 < $usos_restantes) {
            $tipo = $row["tipo"];
            $valor_desconto = $row["valor"];
            if ($tipo == "valor") {
                $valor = $valor - $valor_desconto;
            } else {
                if ($tipo == "%") {
                    $valor_desconto = $valor * $valor_desconto / 100;
                    $valor = $valor - $valor_desconto;
                }
            }
            $_SESSION["valor"] = $valor;
            $stmt = $conexao->prepare("UPDATE cupom SET usos_restantes = usos_restantes - 1 WHERE codigo = ?");
            $stmt->bind_param("s", $cupom);
            $stmt->execute();
            $_SESSION["mensagem"] = "<div class=\"alert alert-danger\"><h6>Parabéns... <br>Seu cupom foi aplicado.</h6></div>";
        } else {
            $_SESSION["mensagem"] = "<div class=\"alert alert-danger\"><h6>Infelizmente o cupom informado já esgotou.</h6></div>";
        }
    } else {
        $_SESSION["mensagem"] = "<div class=\"alert alert-danger\"><h6>Ops... <br> O cupom informado não é válido.</h6></div>";
    }
}
echo "\r\n        \r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n \r\n    ";
include "../menuuser.php";
echo "          \r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-5\">\r\n                        <!-- Inicio -->\r\n\r\n\r\n\r\n                                 <form method=\"post\">\r\n                                    <div class=\"limiter\">\r\n                                        <h3 class=\"card-title\">Bem vindo a pagina de pagamento.</h3><br><br>\r\n\r\n                                        <span class=\"login100-form-title p-b-48\">\r\n                                            <h4 class=\"zmdi zmdi-font\" style=\"font-size: 20px; text-align: center;\">Seu\r\n                                                login é:\r\n                                                ";
echo $_SESSION["login"];
echo "                                            </h4>\r\n                                        </span>\r\n                                        <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Seu\r\n                                            vencimento é:\r\n                                            ";
echo " " . $data;
echo "                                        </h4>\r\n                                        <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Seu\r\n                                            limite é:\r\n                                            ";
echo " " . $limite;
echo "                                        </h4>\r\n                                        <h4 class=\"zmdi zmdi-font\" style=\"font-size: 17px; text-align: center;\">Sua\r\n                                            Mensalidade é:\r\n                                            ";
echo " R\$ " . $valor;
echo "                                        </h4>\r\n                                </form>\r\n                                <form method=\"post\" class=\"mt-4\" class=\"login100-form validate-form\"\r\n                                    onsubmit=\"return validarPost2()\">\r\n                                    <div class=\"input-group\">\r\n                                      <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                                <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                    viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                                </svg>\r\n                                            </span>\r\n                                        <input class=\"form-control\" type=\"text\" name=\"cupom\"\r\n                                            placeholder=\"Digite o cupom\">\r\n                                    </div>\r\n                                    <br>\r\n                                    <input type=\"submit\" class=\"btn btn-primary\" value=\"Aplicar\">\r\n                                </form>\r\n                                <br>\r\n\r\n                                ";
if (isset($_SESSION["mensagem"])) {
    echo "<h3>" . $_SESSION["mensagem"] . "</h3>";
    unset($_SESSION["mensagem"]);
}
echo "\r\n                                <form method=\"POST\" action=\"processandouser.php\" class=\"mt-4\"\r\n                                    class=\"login100-form validate-form\" onsubmit=\"return validarPost()\">\r\n                                    <p>Formulário de Pagamento</p>\r\n                                    <div class=\"form-group mb-4\">\r\n                                        <div class=\"input-group\">\r\n                                         <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                                <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                    viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                                </svg>\r\n                                            </span>\r\n                                            <input class=\"form-control\" placeholder=\"Nome\" type=\"text\" id=\"nome\"\r\n                                                name=\"nome\" placeholder=\"Nome\" required>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                    <div class=\"form-group mb-4\">\r\n                                        <div class=\"input-group\">\r\n                                        <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                                <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                    viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                                </svg>\r\n                                            </span>\r\n                                            <input class=\"form-control\" placeholder=\"Sobrenome\" type=\"text\"\r\n                                                id=\"sobrenome\" name=\"sobrenome\" required>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                    <div class=\"form-group mb-4\">\r\n                                        <div class=\"input-group\"> \r\n                                             <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                                <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                    viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                                </svg>\r\n                                            </span>\r\n                                            <input class=\"form-control\" placeholder=\"Email\" type=\"email\" id=\"email\"\r\n                                                name=\"email\" required>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                    <div class=\"form-group mb-4\">\r\n                                        <div class=\"input-group\">\r\n                                          <span class=\"input-group-text icon-wrapper\" id=\"basic-addon1\">\r\n                                                <svg class=\"icon icon-xs text-gray-600\" fill=\"currentColor\"\r\n                                                    viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\">\r\n                                                </svg>\r\n                                            </span>\r\n                                            <input class=\"form-control\" placeholder=\"CPF\" type=\"text\" id=\"cpf\"\r\n                                                name=\"cpf\" required>\r\n                                        </div>\r\n                                    </div>\r\n                                    <a class=\"btn btn-danger\" href=\"user.php\">Cancelar</a>\r\n                                    <input type=\"submit\" name=\"pagar\" class=\"btn btn-primary\" value=\"Pagar\">\r\n\r\n                                </form>\r\n\r\n\r\n                                <script>\r\n                                    // Seleciona o formulário\r\n                                    var formulario = document.querySelector('form');\r\n\r\n                                    // Adiciona um evento para validar CPF quando o formulário é enviado\r\n                                    formulario.addEventListener('submit', function (e) {\r\n                                        // Seleciona o campo de CPF\r\n                                        var campoCPF = document.querySelector('#cpf');\r\n                                        // Valida o CPF\r\n                                        if (!validaCPF(campoCPF.value)) {\r\n                                            // Impede o envio do formulário\r\n                                            e.preventDefault();\r\n                                            // Exibe uma mensagem de erro para o usuário\r\n                                            alert('CPF inválido!');\r\n                                        }\r\n                                    });\r\n\r\n                                    // Função para validar CPF\r\n                                    function validaCPF(cpf) {\r\n                                        cpf = cpf.replace(/[^\\d]+/g, '');\r\n                                        if (cpf == '') return false;\r\n                                        // Elimina CPFs inválidos conhecidos\r\n                                        if (cpf.length != 11 ||\r\n                                            cpf == \"00000000000\" ||\r\n                                            cpf == \"11111111111\" ||\r\n                                            cpf == \"22222222222\" ||\r\n                                            cpf == \"33333333333\" ||\r\n                                            cpf == \"44444444444\" ||\r\n                                            cpf == \"55555555555\" ||\r\n                                            cpf == \"66666666666\" ||\r\n                                            cpf == \"77777777777\" ||\r\n                                            cpf == \"88888888888\" ||\r\n                                            cpf == \"99999999999\")\r\n                                            return false;\r\n                                        // Validação do primeiro dígito verificador\r\n                                        add = 0;\r\n                                        for (i = 0; i < 9; i++)\r\n                                            add += parseInt(cpf.charAt(i)) * (10 - i);\r\n                                        rev = 11 - (add % 11);\r\n                                        if (rev == 10 || rev == 11)\r\n                                            rev = 0;\r\n                                        if (rev != parseInt(cpf.charAt(9)))\r\n                                            return false;\r\n                                        // Validação do segundo dígito verificador\r\n                                        add = 0;\r\n                                        for (i = 0; i < 10; i++)\r\n                                            add += parseInt(cpf.charAt(i)) * (11 - i);\r\n                                        rev = 11 - (add % 11);\r\n                                        if (rev == 10 || rev == 11)\r\n                                            rev = 0;\r\n                                        if (rev != parseInt(cpf.charAt(10)))\r\n                                            return false;\r\n                                        return true;\r\n                                    }\r\n                                </script>\r\n            \r\n            \r\n            \r\n                    \r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        \r\n\r\n    \r\n    </main>\r\n\r\n\r\n\r\n     <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    </script>\r\n    <script>\r\n        document.addEventListener('contextmenu', function(event) {\r\n            event.preventDefault();\r\n        });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>