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
echo "\r\n    <style>\r\n    .letra-icon {\r\n        font-size: 50px;\r\n        border-radius: 80%;\r\n        border: 1px solid #d9dbdf;\r\n        background-color: #d9dbdf; /* Defina a cor de fundo desejada */\r\n        display: inline-block; /* Garante que o background-color abranja todo o espaço do ícone */\r\n        width: 80px; /* Define a largura igual à font-size para tornar o ícone circular */\r\n        height: 80px; /* Define a altura igual à font-size para tornar o ícone circular */\r\n        text-align: center; /* Centraliza o conteúdo do ícone verticalmente */\r\n        line-height: 80px; /* Centraliza o conteúdo do ícone horizontalmente */\r\n        color: #1c1c1d; /* Define a cor do texto do ícone */\r\n    }\r\n\r\n    .cor-escura {\r\n        background-color: #dee5ee; /* Defina a cor escura desejada */\r\n    }\r\n    \r\n    </style>\r\n     \r\n\r\n            \r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n                  \r\n                          \r\n                       <div class=\"col-12\">\r\n                            <div class=\"card mb-2 cor-escura\">\r\n                                <div class=\"card-body d-flex align-items-center p-1\">\r\n                                    ";
$iduser_get = $_GET["id"];
$sql = "SELECT id, login, senha, nome, contato, byid FROM accounts WHERE id = '" . $iduser_get . "'";
$resultado = mysqli_query($conexao, $sql);
$total_users = mysqli_num_rows($resultado);
while ($row = mysqli_fetch_assoc($resultado)) {
    $account_login = $row["login"];
    $account_senha = $row["senha"];
    $account_nome = $row["nome"];
    $account_contato = $row["contato"];
    $primeira_letra = substr($account_login, 0, 1);
    $userid_sql = "SELECT id, userid FROM atribuidos WHERE userid = '" . $iduser_get . "'";
    $userid_result = mysqli_query($conexao, $userid_sql);
    $userid = "";
    if ($userid_row = mysqli_fetch_assoc($userid_result)) {
        $userid = $userid_row["userid"];
        $idd = $userid_row["id"];
    }
    $sqlVerificaSuspensao = "SELECT suspenso FROM atribuidos WHERE userid = " . $iduser_get;
    $resultadoVerificaSuspensao = $conexao->query($sqlVerificaSuspensao);
    $linhaVerificaSuspensao = $resultadoVerificaSuspensao->fetch_assoc();
    $suspenso = $linhaVerificaSuspensao["suspenso"];
    echo "                                        \r\n                                       \r\n                                        \r\n                                        <div class=\"informacoes\">\r\n                                            <div class=\"d-flex\">\r\n                                                <i class=\"material-icons\">person</i>\r\n                                                <h6 class=\"card-title\">Login: \r\n                                                <p style=\"font-size: 14px;display: inline;\">";
    echo $account_login;
    echo "</p>\r\n                                                </h6>\r\n                                            </div>\r\n                                            <div class=\"d-flex\">\r\n                                                <i class=\"material-icons\">lock</i>\r\n                                                <h6 class=\"card-text\">Senha:\r\n                                                <p style=\"font-size: 14px;display: inline;\">";
    echo $account_senha;
    echo "</p>\r\n                                                </h6>\r\n                                            </div>\r\n                                            <div class=\"d-flex\">\r\n                                                <i class=\"material-icons\">person</i>\r\n                                                <h6 class=\"card-text\">Nome: \r\n                                                <p style=\"font-size: 14px;display: inline;\"> ";
    echo $account_nome;
    echo "</p>\r\n                                               \r\n                                                </h6>\r\n                                            </div>\r\n                                           <div class=\"d-flex\">\r\n                                            <i class=\"material-icons\">phone</i>\r\n                                            <h6 class=\"card-text\">\r\n                                                Contato:\r\n                                                <p style=\"font-size: 14px;display: inline;\">";
    echo $account_contato;
    echo "</p>\r\n                                            </h6>\r\n                                        </div>\r\n\r\n                                        </div>\r\n                                     <div class=\"ml-auto\" style=\"position: absolute; right: 15px;\">\r\n                                          <div style=\"margin-bottom: 5px;\">\r\n                                            <a href=\"editarrev.php?id=";
    echo $iduser_get;
    echo "\" class=\"btn btn-primary btn-sm btn-block text-truncate\" style=\"background-color: #007bff; border: none; font-size: 10px;\">Editar Revenda</a>\r\n                                        </div>\r\n                                        <div style=\"margin-bottom: 5px;\">\r\n                                            <button class=\"btn btn-danger btn-sm btn-block text-truncate\" style=\"font-size: 10px;\" onclick=\"confirmarExclusao(";
    echo $iduser_get;
    echo ")\">\r\n                                                Excluir Revenda\r\n                                            </button>\r\n                                        </div>\r\n                                        <div style=\"margin-top: 5px;\">\r\n                                            <a href=\"criaratribuicao.php?id=";
    echo $iduser_get;
    echo "\" class=\"btn btn-primary btn-sm btn-block text-truncate\" style=\"font-size: 10px;\">Nova Atribuição</a>\r\n                                        </div>\r\n                                      <!--<div style=\"margin-top: 5px;\">\r\n                                            ";
    if ($suspenso == 1) {
        echo "                                                <a href=\"reativarrev.php?id=";
        echo $id;
        echo "&userid=";
        echo $userid;
        echo "\" class=\"btn btn-info btn-sm\" style=\"font-size: 10px;\">Reativa Revenda</a>\r\n                                            ";
    } else {
        echo "                                                <a href=\"suspenso.php?id=";
        echo $id;
        echo "&userid=";
        echo $userid;
        echo "\" class=\"btn btn-info btn-sm btn-block text-truncate\" style=\"font-size: 10px;\">Suspende Revenda</a>\r\n                                            ";
    }
    echo "                                        </div>-->\r\n                                    </div>\r\n\r\n                                        ";
}
echo "                                </div>\r\n                            </div>\r\n                        </div><hr>\r\n\r\n\r\n                        ";
if (isset($_SESSION["atribuicao"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["atribuicao"] . "' });</script>";
    unset($_SESSION["atribuicao"]);
}
echo "\r\n                        <center>\r\n                        <h2 class=\"card-title\">Servidores Atribuidos</h2>\r\n                        </center>\r\n                       \r\n                        <!-- Inicio -->\r\n                     <div class=\"col-12 mb-3\">\r\n                                <div class=\"card card-sm\">\r\n                                    <div class=\"card-body1 p-2\">\r\n                                        ";
$sql = "SELECT id, categoriaid FROM atribuidos WHERE userid = '" . $iduser_get . "'";
$resultado = mysqli_query($conexao, $sql);
if (0 < mysqli_num_rows($resultado)) {
    while ($row = mysqli_fetch_assoc($resultado)) {
        $atribuidosId = $row["id"];
        $categoriaId = $row["categoriaid"];
        $countQueryAtribuidos = "SELECT SUM(limite) AS totalLimiteAtribuidos FROM atribuidos WHERE byid = '" . $iduser_get . "' AND categoriaid = '" . $categoriaId . "'";
        $countResultAtribuidos = mysqli_query($conexao, $countQueryAtribuidos);
        $countRowAtribuidos = mysqli_fetch_assoc($countResultAtribuidos);
        $totalLimiteAtribuidos = $countRowAtribuidos["totalLimiteAtribuidos"];
        $countQuerySshAccounts = "SELECT SUM(limite) AS totalLimiteSshAccounts FROM ssh_accounts WHERE byid = '" . $iduser_get . "' AND categoriaid = '" . $categoriaId . "'";
        $countResultSshAccounts = mysqli_query($conexao, $countQuerySshAccounts);
        $countRowSshAccounts = mysqli_fetch_assoc($countResultSshAccounts);
        $totalLimiteSshAccounts = $countRowSshAccounts["totalLimiteSshAccounts"];
        $userCount = $totalLimiteAtribuidos + $totalLimiteSshAccounts;
        if ($userCount === NULL) {
            $userCount = 0;
        }
        $sqlAtribuidos = "SELECT atribuidos.id, atribuidos.valor, atribuidos.limite, atribuidos.limitetest, atribuidos.tipo, atribuidos.expira, categorias.nome, accounts.login, atribuidos.userid, accounts.id \r\n                                                                    FROM atribuidos \r\n                                                                    INNER JOIN categorias ON atribuidos.categoriaid = categorias.subid \r\n                                                                    INNER JOIN accounts ON atribuidos.userid = accounts.id \r\n                                                                    WHERE atribuidos.id = '" . $atribuidosId . "'";
        $resultadoAtribuidos = mysqli_query($conexao, $sqlAtribuidos);
        while ($atribuidos = mysqli_fetch_assoc($resultadoAtribuidos)) {
            $userid = $atribuidos["userid"];
            $accountid = $atribuidos["id"];
            echo "                                            <a href=\"editar.php?id=";
            echo $atribuidosId;
            echo "&userid=";
            echo $userid;
            echo "&categoriaid=";
            echo $categoriaId;
            echo "\">\r\n                                            <div class=\"col-12\">\r\n                                                <div class=\"card mb-2 cor-escura\">\r\n                                                    <div class=\"card-body d-flex align-items-center p-2\">\r\n                            \r\n                                                        <div class=\"informacoes\">\r\n                                                        ";
            $nome = "<div class=\"d-flex\"><h6 class=\"card-title\">" . $atribuidos["nome"] . "</h6></div>";
            $limite_usado = "<div class=\"d-flex\"><span class=\"card-text\">Limite usado: " . $userCount . " de " . $atribuidos["limite"] . "</span></div>";
            echo $nome;
            if ($atribuidos["tipo"] == "Credito") {
                $creditos_disponiveis = "<div class=\"d-flex\"><span class=\"card-text\">Creditos disponíveis: " . $atribuidos["limite"] . "</span></div>";
                $testes_disponiveis = "<div class=\"d-flex\"><span class=\"card-text\">Testes disponíveis: " . $atribuidos["limitetest"] . "</span></div>";
                echo $creditos_disponiveis;
                echo $testes_disponiveis;
            } else {
                echo $limite_usado;
                if ($atribuidos["tipo"] == "Validade") {
                    $hoje = new DateTime();
                    $expira = new DateTime($atribuidos["expira"]);
                    $diferenca = $hoje->diff($expira);
                    $expira_info = "";
                    if ($expira < $hoje) {
                        $expira_info = "<span class=\"card-text\">Expirado</span>";
                    } else {
                        if ($diferenca->days == 0) {
                            $expira_info = "<span class=\"card-text\">Expira hoje</span>";
                        } else {
                            $format = "%a dias, %h horas, %i minutos";
                            $expira_info = "<span class=\"card-text\">Expira em: " . $diferenca->format($format) . "</span>";
                        }
                    }
                    echo "<div class=\"d-flex\">" . $expira_info . "</div>";
                    if ($suspenso == 1) {
                        echo "<a href=\"editar.php?id=" . $atribuidosId . "&userid=" . $userid . "&categoriaid=" . $categoriaid . "\" class=\"btn btn-info btn-sm\" style=\"font-size: 10px;\">Reativa Revenda</a>";
                    } else {
                        echo "<a href=\"suspenso.php?id=" . $atribuidosId . "&userid=" . $userid . "\" class=\"btn btn-info btn-sm btn-block text-truncate\" style=\"font-size: 10px;\">Suspende Revenda</a>";
                    }
                }
            }
            echo "                                                            <img src=\"https://i.imgur.com/K2BjolL.png\" style=\"height: 45px;position: absolute;right: 7px;top: 13px;\">\r\n                                                        </div>\r\n                                                    </div>\r\n                                                </div>\r\n                                            </div>\r\n                                        </a>\r\n                                        ";
        }
    }
} else {
    echo "<p>Nenhuma revenda cadastrada.</p>";
}
echo "                                    </div>\r\n                                </div>\r\n                            </div>\r\n                            <hr>\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n    <script>\r\n    function confirmarExclusao(id) {\r\n        Swal.fire({\r\n            title: 'Tem certeza?',\r\n            html: 'Essa ação não pode ser desfeita! <br> E irá excluir todas as revendas, atribuições e usuários finais.',\r\n            icon: 'warning',\r\n            showCancelButton: true,\r\n            confirmButtonColor: '#3085d6',\r\n            cancelButtonColor: '#d33',\r\n            cancelButtonText: 'Cancelar',\r\n            confirmButtonText: 'Sim, excluir!'\r\n            \r\n        }).then((result) => {\r\n            if (result.isConfirmed) {\r\n                // Redirecionar para a página de exclusão com o parâmetro id\r\n                window.location.href = 'excluir.php?id=' + id;\r\n            }\r\n        });\r\n    }\r\n</script>\r\n   \r\n</body>\r\n\r\n</html>";

?>