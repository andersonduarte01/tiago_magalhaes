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
if (!isset($_GET["id"])) {
    echo "<script> window.location.href='listarrev.php'; </script>";
    exit;
}
$userid = $_GET["id"];
$iduser = $_SESSION["iduser"];
if ($_SESSION["iduser"] == 1) {
    $query = "SELECT * FROM atribuidos";
} else {
    $query = "SELECT * FROM atribuidos WHERE userid = " . $iduser;
}
$resultado = mysqli_query($conexao, $query);
$atribuidos = [];
while ($atribuido = mysqli_fetch_assoc($resultado)) {
    $atribuidos[] = $atribuido;
}
if ($_SESSION["iduser"] == 1) {
    $queryCategorias = "SELECT * FROM categorias";
} else {
    $queryCategorias = "SELECT * FROM categorias WHERE subid IN (SELECT categoriaid FROM atribuidos WHERE userid = " . $iduser . ")";
}
$resultadoCategorias = mysqli_query($conexao, $queryCategorias);
$categorias = [];
while ($categoria = mysqli_fetch_assoc($resultadoCategorias)) {
    $categorias[$categoria["subid"]] = $categoria;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST["valor"];
    $limite = $_POST["limite"];
    $limitetest = $_POST["limitetest"];
    $tipo = $_POST["tipo"];
    $categoria_id = $_POST["categoriaid"];
    $dias = $_POST["dias"];
    $subrev = $_POST["subrev"];
    if ($_SESSION["iduser"] != 1) {
        $query = "SELECT categoriaid, suspenso, tipo, expira FROM atribuidos WHERE userid = ?";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "s", $iduser);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $categoriaEncontrada = false;
        while ($atribuido = mysqli_fetch_assoc($resultado)) {
            if ($atribuido["categoriaid"] == $categoria_id) {
                $categoriaEncontrada = true;
                if ($atribuido["suspenso"] == 1) {
                    $_SESSION["erroredit"] = "A atribuição está suspensa temporariamente e não poderá editar essa contas. Por favor, entre em contato com o administrador para obter mais informações e resolver essa questão.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($atribuido["tipo"] == "Validade" && strtotime($atribuido["expira"]) < time()) {
                    $_SESSION["erroredit"] = "A atribuição está vencida. Por favor, renová para poder continuar.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            }
        }
        if (!$categoriaEncontrada) {
            $_SESSION["erroredit"] = "A categoria selecionada não corresponde à atribuição atual.";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    $date = new DateTime();
    $date->add(new DateInterval("P" . $dias . "D"));
    $expira = $date->format("Y-m-d H:i:s");
    $sql_verificar_categoria = "SELECT * FROM atribuidos WHERE userid = ? AND categoriaid = ?";
    $stmt_verificar_categoria = $conexao->prepare($sql_verificar_categoria);
    $stmt_verificar_categoria->bind_param("ii", $userid, $categoria_id);
    $stmt_verificar_categoria->execute();
    $result_verificar_categoria = $stmt_verificar_categoria->get_result();
    if (0 < $result_verificar_categoria->num_rows) {
        $_SESSION["erroredit"] = "Este revendedor já possui esta categoria atribuída.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($iduser != 1) {
        $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = ? AND categoriaid = ?";
        $stmt_atribuidos = $conexao->prepare($sql_atribuidos);
        $stmt_atribuidos->bind_param("ii", $iduser, $categoria_id);
        $stmt_atribuidos->execute();
        $result_atribuidos = $stmt_atribuidos->get_result();
        if (0 < $result_atribuidos->num_rows) {
            $atribuido = $result_atribuidos->fetch_assoc();
            if ($atribuido["tipo"] == "Validade") {
                $sql_usuarios = "SELECT SUM(limite) AS total_limite FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
                $stmt_usuarios = $conexao->prepare($sql_usuarios);
                $stmt_usuarios->bind_param("ii", $iduser, $categoria_id);
                $stmt_usuarios->execute();
                $result_usuarios = $stmt_usuarios->get_result();
                $usuarios_criados = $result_usuarios->fetch_assoc()["total_limite"];
                if ($atribuido["limite"] <= $usuarios_criados) {
                    $_SESSION["erroredit"] = "Limite de usuários excedido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($atribuido["limite"] < $usuarios_criados + $limite) {
                    $_SESSION["erroredit"] = "O limite fornecido excede o limite permitido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($limite < $limite) {
                    $_SESSION["erroredit"] = "O limite fornecido excede o limite de crédito disponível para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            } else {
                if ($atribuido["tipo"] == "Credito") {
                    if (1 <= $atribuido["limite"] && 1 <= $atribuido["limitetest"]) {
                        if ($atribuido["limite"] < $limite) {
                            $_SESSION["erroredit"] = "O limite fornecido excede o limite de Creditos disponíveis para a categoria selecionada.";
                            header("Location: " . $_SERVER["HTTP_REFERER"]);
                            exit;
                        }
                        if ($atribuido["limitetest"] < $limitetest) {
                            $_SESSION["erroredit"] = "O limite fornecido excede o limite de testes disponíveis para a categoria selecionada.";
                            header("Location: " . $_SERVER["HTTP_REFERER"]);
                            exit;
                        }
                        $new_limit = $atribuido["limite"] - $limite;
                        $new_limit_test = $atribuido["limitetest"] - $limitetest;
                        $sql_update = "UPDATE atribuidos SET limite = ?, limitetest = ? WHERE userid = ? AND categoriaid = ?";
                        $stmt_update = $conexao->prepare($sql_update);
                        $stmt_update->bind_param("iiii", $new_limit, $new_limit_test, $iduser, $categoria_id);
                        $stmt_update->execute();
                        $stmt_update->close();
                    } else {
                        $_SESSION["erroredit"] = "Usuário não possui Creditos suficientes para criar esse acesso.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                }
            }
        }
    }
    $byid = $_SESSION["iduser"];
    $query = "INSERT INTO atribuidos (valor, userid, byid, limite, limitetest, tipo, expira, categoriaid, subrev) VALUES ('" . $valor . "', '" . $userid . "', '" . $byid . "', '" . $limite . "', '" . $limitetest . "', '" . $tipo . "', '" . $expira . "', '" . $categoria_id . "', '" . $subrev . "')";
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Não foi possível atualizar os dados: " . mysqli_error($conexao));
    }
    $_SESSION["atribuicao"] = "<div>Atribuição adicionada com sucesso!</div>";
    header("Location: atribuicao.php?id=" . $userid);
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
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n        ";
include "../../menu.php";
echo "\r\n\r\n\r\n            <center>\r\n                <div class=\"container py-5\">\r\n                    <div class=\"col-lg-8 mx-auto\">\r\n                        <center>\r\n                            <h2 class=\"card-title\">Adicionar atribuição</h2>\r\n                        </center>\r\n\r\n\r\n                      ";
if (isset($_SESSION["erroredit"])) {
    echo "<script>Swal.fire({\r\n                            icon: 'error',\r\n                            title: 'Erro!',\r\n                            html: '" . $_SESSION["erroredit"] . "',\r\n                            showConfirmButton: true,\r\n                            confirmButtonText: 'OK'\r\n                          });</script>";
    unset($_SESSION["erroredit"]);
}
echo "\r\n\r\n                        <div class=\"card-body\">\r\n                            <center>\r\n                                <form method=\"post\" action=\"\">\r\n\r\n\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Categoria</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <select class=\"form-control\" name=\"categoriaid\" id=\"categoriaid\" required>\r\n                                            <option value=\"\">Selecione uma categoria</option>\r\n                                            ";
foreach ($categorias as $subid => $categoria) {
    echo "                                                <option value=\"";
    echo $subid;
    echo "\" data-tipo=\"";
    echo $categoria["tipo"];
    echo "\">\r\n                                                    ";
    echo $categoria["nome"];
    echo "                                                </option>\r\n                                            ";
}
echo "                                        </select>\r\n                                    </div>\r\n\r\n\r\n                                    ";
if ($iduser === 1) {
    echo "                                        <h8 class=\"card-title\" style=\"font-family: Arial;\">Tipo de Revenda</h8>\r\n                                        <div class=\"form-group\" id=\"tipo-group\">\r\n                                            <select class=\"form-control\" name=\"tipo\" id=\"tipo\" disabled required>\r\n                                                <option value=\"\">Selecione uma categoria primeiro</option>\r\n                                                <option value=\"Validade\" class=\"tipo-option\" data-categoriaid=\"1\">\r\n                                                    Validade\r\n                                                </option>\r\n                                                <option value=\"Credito\" class=\"tipo-option\" data-categoriaid=\"2\">\r\n                                                    Credito\r\n                                                </option>\r\n                                            </select>\r\n                                        </div>\r\n                                    ";
} else {
    echo "                                    \r\n                                        <div class=\"form-group\" id=\"tipo-group\" style=\"display: none;\">\r\n                                            <select class=\"form-control\" name=\"tipo\" id=\"tipo\" disabled required>\r\n                                                <option value=\"\">Selecione uma categoria primeiro</option>\r\n                                                ";
    foreach ($atribuidos as $atribuido) {
        echo "                                                    <option value=\"";
        echo $atribuido["tipo"];
        echo "\" class=\"tipo-option\" data-categoriaid=\"";
        echo $atribuido["categoriaid"];
        echo "\">\r\n                                                        ";
        echo $atribuido["tipo"];
        echo "                                                    </option>\r\n                                                ";
    }
    echo "                                            </select>\r\n                                        </div>\r\n                                    ";
}
echo "\r\n                                    <script>\r\n                                        var tipoField = document.getElementById('tipo');\r\n                                        var tipoOptions = document.getElementsByClassName('tipo-option');\r\n\r\n                                        document.getElementById('categoriaid').addEventListener('change', function() {\r\n                                            var selectedOption = this.options[this.selectedIndex];\r\n                                            var selectedCategoriaId = selectedOption.value;\r\n\r\n                                            if (selectedCategoriaId === '') {\r\n                                                tipoField.value = '';\r\n                                                tipoField.disabled = true;\r\n                                            } else {\r\n                                                tipoField.disabled = false;\r\n\r\n                                                for (var i = 0; i < tipoOptions.length; i++) {\r\n                                                    var tipoOption = tipoOptions[i];\r\n                                                    var categoriaId = tipoOption.getAttribute('data-categoriaid');\r\n\r\n                                                    if (categoriaId === selectedCategoriaId) {\r\n                                                        tipoField.value = tipoOption.value;\r\n                                                        break;\r\n                                                    }\r\n                                                }\r\n                                            }\r\n                                        });\r\n\r\n                                        document.getElementById('form').addEventListener('submit', function() {\r\n                                            tipoField.disabled = false;\r\n                                        });\r\n                                    </script>\r\n\r\n\r\n\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Sub-revenda</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <select class=\"form-control\" name=\"subrev\" id=\"subrev\" required>\r\n                                            <option value=\"1\" ";
if ($atribuido["subrev"] == "sim") {
    echo "selected=\"selected\"";
}
echo ">Sim\r\n                                            </option>\r\n                                            <option value=\"0\" ";
if ($atribuido["subrev"] == "nao") {
    echo "selected=\"selected\"";
}
echo ">Não\r\n                                            </option>\r\n                                        </select>\r\n                                    </div>\r\n                                    \r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Limite</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <input type=\"number\" class=\"form-control\" name=\"limite\" id=\"limite\" value=\"1\" required>\r\n                                    </div>\r\n                                    \r\n                                    <div class=\"form-group\">\r\n                                        <h8 class=\"card-title\" style=\"font-family: Arial;\">Limite de teste</h8>\r\n                                        <div class=\"form-group\">\r\n                                            <input type=\"number\" class=\"form-control\" name=\"limitetest\" id=\"limitetest\" value=\"1\" required>\r\n                                        </div>\r\n                                    </div>\r\n                                    \r\n\r\n\r\n\r\n                                                                        \r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Dias</h8>\r\n                                    <div class=\"form-group\">\r\n                                        ";
$expira = $atribuido["expira"];
$dataAtual = new DateTime();
$dataExpiracao = new DateTime($expira);
$diferenca = $dataExpiracao->diff($dataAtual);
$valorDias = $diferenca->days;
if ($valorDias < 0) {
    $valorDias = 0;
}
echo "<input type=\"number\" class=\"form-control\" id=\"dias\" name=\"dias\" value=\"30\" required>                                    </div>\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Valor</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <input type=\"number\" class=\"form-control\" name=\"valor\" id=\"valor\" value=\"30\" required>\r\n                                    </div>\r\n                                    <a class=\"btn btn-danger\" href=\"atribuicao.php?id=";
echo $userid;
echo "\">Cancelar</a>\r\n                                    <button type=\"submit\" style=\"background-color: #007bff; border: none;\" class=\"btn btn-primary\" name=\"submit\">Salvar</button>\r\n                                </form>\r\n                            </center>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n        \$(document).ready(function() {\r\n            // Adicione a funcionalidade de busca na tabela\r\n            \$('#searchInput').on('keyup', function() {\r\n                var value = \$(this).val().toLowerCase();\r\n                \$('#usuario tbody tr').filter(function() {\r\n                    var rowText = \$(this).text().toLowerCase();\r\n                    var searchTerms = value.split(' ');\r\n                    var found = true;\r\n                    for (var i = 0; i < searchTerms.length; i++) {\r\n                        if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                            found = false;\r\n                            break;\r\n                        }\r\n                    }\r\n                    \$(this).toggle(found);\r\n                });\r\n            });\r\n        });\r\n    </script>\r\n    <!--<script>\r\n        document.addEventListener('contextmenu', function(event) {\r\n            event.preventDefault();\r\n        });\r\n    </script>-->\r\n</body>\r\n\r\n</html>";

?>