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
$byid = $_SESSION["iduser"];
$mainid = mt_rand(100000, 999999);
$query = "SELECT suspenso FROM atribuidos WHERE userid = ?";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "s", $byid);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
if (0 < mysqli_num_rows($resultado)) {
    $atribuido = mysqli_fetch_assoc($resultado);
    if ($atribuido["suspenso"] == 1) {
        $_SESSION["link2"] = "Você está suspenso temporariamente e não poderá criar novas contas. Por favor, entre em contato com o administrador para obter mais informações e resolver essa questão.";
        echo "<script> window.location.href='../../nivel.php'; </script>";
        exit;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $senha = $_POST["senha"];
    $nome = $_POST["nome"];
    $contato = $_POST["contato"];
    $query_verifica = "SELECT id FROM accounts WHERE login = ?";
    $stmt_verifica = $conexao->prepare($query_verifica);
    $stmt_verifica->bind_param("s", $login);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();
    $query_verifica_ssh = "SELECT id FROM ssh_accounts WHERE login = ?";
    $stmt_verifica_ssh = $conexao->prepare($query_verifica_ssh);
    $stmt_verifica_ssh->bind_param("s", $login);
    $stmt_verifica_ssh->execute();
    $stmt_verifica_ssh->store_result();
    if (0 < $stmt_verifica->num_rows || 0 < $stmt_verifica_ssh->num_rows) {
        $_SESSION["errorcriar"] = "<div>Revenda já cadastrado, adicionar outro login.</div>";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    $query = "INSERT INTO accounts (login, senha, nome, contato, byid, mainid) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($query);
    $stmt->bind_param("ssssis", $login, $senha, $nome, $contato, $byid, $mainid);
    $resultado = $stmt->execute();
    if (!$resultado) {
        exit("Não foi possível inserir os dados: " . $conexao->error);
    }
    $id = $stmt->insert_id;
    $successMessage = [];
    $successMessage["message1"] = "Revenda criada com sucesso!\n";
    $successMessage["message2"] = "Login: " . $login . "\n";
    $successMessage["message3"] = "Senha: " . $senha . "\n";
    $successMessage["message4"] = "Nome: " . $nome . "\n";
    $successMessage["message5"] = "Contato: " . $contato;
    $_SESSION["atribuicao"] = $successMessage;
    $_SESSION["login"] = $login;
    $_SESSION["senha"] = $senha;
    $_SESSION["nome"] = $nome;
    $_SESSION["contato"] = $contato;
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
echo "\r\n            <center>\r\n                <div class=\"container py-3\">\r\n                    <div class=\"col-lg-8 mx-auto\">\r\n                        <center>\r\n                            <h2 class=\"card-title py-5\">Cria Revenda</h2>\r\n                        </center>\r\n\r\n\r\n                 ";
if (isset($_SESSION["atribuicao"])) {
    $login = $_SESSION["login"];
    $senha = $_SESSION["senha"];
    $nome = $_SESSION["nome"];
    $contato = $_SESSION["contato"];
    echo "\r\n                        <script>\r\n                            document.addEventListener('DOMContentLoaded', function() {\r\n                                // Concatenate the information to copy\r\n                                const informationToCopy = \r\n                                    'Login: " . $login . "<br>' +\r\n                                    'Senha: " . $senha . "<br>' +\r\n                                    'Nome: " . $nome . "<br>' +\r\n                                    'Contato: " . $contato . "';\r\n                \r\n                                // Show SweetAlert2 popup with the information and the copy button\r\n                                Swal.fire({\r\n                                    icon: 'success',\r\n                                    title: 'Sucesso!',\r\n                                    html: informationToCopy, // Use 'html' instead of 'text' to render HTML content\r\n                                    showCloseButton: true,\r\n                                    showCancelButton: true,\r\n                                    focusConfirm: false,\r\n                                    confirmButtonText: 'Copiar Informações',\r\n                                    cancelButtonText: 'Fechar',\r\n                                    customClass: {\r\n                                        content: 'text-left' // Add custom class for text alignment\r\n                                    }\r\n                                }).then((result) => {\r\n                                    // If the user clicks on 'Copiar Informações', copy the text\r\n                                    if (result.isConfirmed) {\r\n                                        // Create a temporary textarea element to hold the text\r\n                                        const tempTextarea = document.createElement('textarea');\r\n                                        tempTextarea.value = informationToCopy.replace(/<br>/g, '\\n'); // Replace <br> with newline characters\r\n                \r\n                                        // Append the textarea to the body\r\n                                        document.body.appendChild(tempTextarea);\r\n                \r\n                                        // Select the text and copy it\r\n                                        tempTextarea.select();\r\n                                        document.execCommand('copy');\r\n                \r\n                                        // Remove the temporary textarea\r\n                                        document.body.removeChild(tempTextarea);\r\n                \r\n                                        // Show a message indicating the information has been copied\r\n                                        Swal.fire({\r\n                                            icon: 'success',\r\n                                            title: 'Informações copiadas com sucesso!',\r\n                                            showConfirmButton: false,\r\n                                            timer: 1500,\r\n                                        }).then(() => {\r\n                                            // Redirect to atribuicao.php?id=" . $id . " after the information has been copied\r\n                                            window.location.href = 'atribuicao.php?id=" . $id . "';\r\n                                        });\r\n                                    } else {\r\n                                        // Redirect to atribuicao.php?id=" . $id . " if the user clicks on 'Fechar'\r\n                                        window.location.href = 'atribuicao.php?id=" . $id . "';\r\n                                    }\r\n                                });\r\n                            });\r\n                        </script>\r\n                    ";
    unset($_SESSION["atribuicao"]);
}
echo "\r\n\r\n\r\n\r\n\r\n\r\n                        \r\n                      \r\n\r\n                        <div class=\"card-body\">\r\n                            <center>\r\n                                <form method=\"post\" action=\"\">\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Login</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <input type=\"text\" class=\"form-control\" name=\"login\" id=\"login\" value=\"\" required>\r\n                                    </div>\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Senha</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <input type=\"text\" class=\"form-control\" name=\"senha\" id=\"senha\" value=\"\" required>\r\n                                    </div>\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Nome</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <input type=\"text\" class=\"form-control\" name=\"nome\" id=\"nome\" value=\"\">\r\n                                    </div>\r\n                                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Celular</h8>\r\n                                    <div class=\"form-group\">\r\n                                        <input type=\"text\" class=\"form-control\" name=\"contato\" id=\"contato\" maxlength=\"16\" placeholder=\"Exemplo: 99 9 9999-9999\" oninput=\"formatarCelular(this)\">\r\n                                    </div>\r\n                                    <a class=\"btn btn-danger\" href=\"atribuicao.php?id=";
echo $userid;
echo "\">Cancelar</a>\r\n                                    <button type=\"submit\" style=\"background-color: #007bff; border: none;\" class=\"btn btn-primary\" name=\"submit\">Salvar</button>\r\n                                </form>\r\n\r\n                                <script>\r\n                                    function formatarCelular(input) {\r\n                                        var numero = input.value.replace(/\\D/g, '');\r\n                                        var formatado = '';\r\n\r\n                                        if (numero.length > 0) {\r\n                                            formatado = '(' + numero.substring(0, 2);\r\n                                        }\r\n\r\n                                        if (numero.length > 2) {\r\n                                            formatado += ') ' + numero.substring(2, 3);\r\n                                        }\r\n\r\n                                        if (numero.length > 3) {\r\n                                            formatado += ' ' + numero.substring(3, 7);\r\n                                        }\r\n\r\n                                        if (numero.length > 7) {\r\n                                            formatado += '-' + numero.substring(7, 11);\r\n                                        }\r\n\r\n                                        input.value = formatado;\r\n                                    }\r\n                                </script>\r\n\r\n\r\n                            </center>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n        \$(document).ready(function() {\r\n            // Adicione a funcionalidade de busca na tabela\r\n            \$('#searchInput').on('keyup', function() {\r\n                var value = \$(this).val().toLowerCase();\r\n                \$('#usuario tbody tr').filter(function() {\r\n                    var rowText = \$(this).text().toLowerCase();\r\n                    var searchTerms = value.split(' ');\r\n                    var found = true;\r\n                    for (var i = 0; i < searchTerms.length; i++) {\r\n                        if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                            found = false;\r\n                            break;\r\n                        }\r\n                    }\r\n                    \$(this).toggle(found);\r\n                });\r\n            });\r\n        });\r\n    </script>\r\n  \r\n</body>\r\n\r\n</html>";

?>