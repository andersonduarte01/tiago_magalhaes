<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
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
if (!extension_loaded("ssh2")) {
    $_SESSION["error"] = "A extensão SSH2 não está instalada. Verifique sua configuração do PHP.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $id = $_GET["id"];
    $iduser = $_SESSION["iduser"];
    $mainid = $_SESSION["mainid"];
    $_SESSION["LAST_ACTIVITY"] = time();
    $sql_ssh_accounts = "SELECT * FROM ssh_accounts WHERE id = ?";
    $stmt = $conexao->prepare($sql_ssh_accounts);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados_usuario = $result->fetch_assoc();
    if (0 < $result->num_rows) {
        $LoginConsultar = $dados_usuario["login"];
        $SenhaConsultar = $dados_usuario["senha"];
        $LimiteConsultar = $dados_usuario["limite"];
        $ExpiraConsultar = $dados_usuario["expira"];
        $CategoriaConsultar = $dados_usuario["categoriaid"];
        $expira_em_dias = floor((strtotime($ExpiraConsultar) - time()) / 86400) + 1;
        $data_expiracao = date("Y-m-d H:i:s", strtotime($ExpiraConsultar));
        $dias = $expira_em_dias;
        $query = "SELECT c.nome, c.subid \r\n              FROM atribuidos a \r\n              JOIN categorias c ON a.categoriaid = c.subid \r\n              WHERE a.userid = ?";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "s", $iduser);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (0 < mysqli_num_rows($resultado)) {
            $categorias = [];
            while ($categoria = mysqli_fetch_assoc($resultado)) {
                $subid = $categoria["subid"];
                $nome = $categoria["nome"];
                $categorias[$subid][] = $nome;
            }
        } else {
            $query = "SELECT * FROM accounts WHERE id = ?";
            $stmt = mysqli_prepare($conexao, $query);
            mysqli_stmt_bind_param($stmt, "s", $iduser);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            if (0 < mysqli_num_rows($resultado)) {
                $account = mysqli_fetch_assoc($resultado);
                if ($account["id"] == 1) {
                    $query = "SELECT nome, subid FROM categorias";
                    $resultado = mysqli_query($conexao, $query);
                    $categorias = [];
                    while ($categoria = mysqli_fetch_assoc($resultado)) {
                        $subid = $categoria["subid"];
                        $nome = $categoria["nome"];
                        $categorias[$subid][] = $nome;
                    }
                } else {
                    echo "Usuário não encontrado.";
                }
            } else {
                echo "Usuário não encontrado.";
            }
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
            $login = $_POST["login"];
            $senha = $_POST["senha"];
            $categoriaid = $_POST["categoriaid"];
            if ($iduser != 1) {
                $query = "SELECT categoriaid, suspenso, tipo, expira FROM atribuidos WHERE userid = ?";
                $stmt = mysqli_prepare($conexao, $query);
                mysqli_stmt_bind_param($stmt, "s", $iduser);
                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);
                $categoriaEncontrada = false;
                while ($atribuido = mysqli_fetch_assoc($resultado)) {
                    if ($atribuido["categoriaid"] == $categoriaid) {
                        $categoriaEncontrada = true;
                        if ($atribuido["suspenso"] == 1) {
                            $_SESSION["error"] = "A atribuição está suspensa temporariamente e não é possível editar essa conta. Por favor, entre em contato com o administrador para obter mais informações e resolver essa questão.";
                            header("Location: " . $_SERVER["HTTP_REFERER"]);
                            exit;
                        }
                        if ($atribuido["tipo"] == "Validade" && strtotime($atribuido["expira"]) < time()) {
                            $_SESSION["error"] = "A atribuição está vencida. Por favor, entre em contato com o administrador para renová-la.";
                            header("Location: " . $_SERVER["HTTP_REFERER"]);
                            exit;
                        }
                    }
                }
                if (!$categoriaEncontrada) {
                    $_SESSION["error"] = "A categoria selecionada não corresponde à atribuição atual.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            }
            if ($iduser == 1) {
                $dias1 = $dias;
                $dias1 += 1;
                $file = fopen("../../home/modulos/EditarUsuario.sh", "w");
                $command = "./ExcluirExpiradoApi.sh " . $LoginConsultar . "\n./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias1 . " " . $LimiteConsultar . "\n";
                fwrite($file, $command);
                fclose($file);
                $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
                $resultadoServidores = $conexao->query($sqlServidores);
                if (0 < $resultadoServidores->num_rows) {
                    $errors = [];
                    while ($row = $resultadoServidores->fetch_assoc()) {
                        if ($row["subid"] == $categoriaid) {
                            try {
                                $connection = ssh2_connect($row["ip"], $row["porta"]);
                                if (!$connection) {
                                    $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                                } else {
                                    if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                                        $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                                        ssh2_disconnect($connection);
                                    } else {
                                        if (!function_exists("ssh2_scp_send")) {
                                            $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                                            ssh2_disconnect($connection);
                                        } else {
                                            if (!ssh2_scp_send($connection, "../../home/modulos/EditarUsuario.sh", "EditarUsuario.sh", 493)) {
                                                $errors[] = "Falha ao enviar o arquivo para o servidor";
                                                ssh2_disconnect($connection);
                                            } else {
                                                $exec_command = "./EditarUsuario.sh >/dev/null 2>&1 &";
                                                ssh2_exec($connection, $exec_command);
                                                ssh2_disconnect($connection);
                                            }
                                        }
                                    }
                                }
                            } catch (ErrorException $e) {
                                $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
                            }
                        }
                    }
                    if (!empty($errors)) {
                        $_SESSION["error"] = implode("<br>", $errors);
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                }
                $stmt = $conexao->prepare("UPDATE ssh_accounts SET senha=?, limite=?, expira=?, byid=?, mainid=?, categoriaid=?, login=? WHERE id=?");
                $stmt->bind_param("sisiiisi", $senha, $LimiteConsultar, $data_expiracao, $iduser, $mainid, $categoriaid, $login, $id);
                $stmt->execute();
            } else {
                $stmt_select_tipo = $conexao->prepare("SELECT tipo FROM atribuidos WHERE userid=?");
                $stmt_select_tipo->bind_param("i", $iduser);
                $stmt_select_tipo->execute();
                $result_tipo = $stmt_select_tipo->get_result();
                if (0 < $result_tipo->num_rows) {
                    $row_tipo = $result_tipo->fetch_assoc();
                    $tipo = $row_tipo["tipo"];
                    if ($tipo === "Credito") {
                        $stmt_select = $conexao->prepare("SELECT expira FROM ssh_accounts WHERE id=?");
                        $stmt_select->bind_param("s", $id);
                        $stmt_select->execute();
                        $result = $stmt_select->get_result();
                        if (0 < $result->num_rows) {
                            $row = $result->fetch_assoc();
                            $data_expiracao_atual = $row["expira"];
                            $data_expiracao_atual_obj = new DateTime($data_expiracao_atual);
                            $data_expiracao = $data_expiracao_atual_obj->format("Y-m-d H:i:s");
                            $data_atual = new DateTime();
                            $interval = $data_atual->diff($data_expiracao_atual_obj);
                            $dias = $interval->days;
                            $file = fopen("../../home/modulos/EditarUsuario.sh", "w");
                            $command = "./ExcluirExpiradoApi.sh " . $LoginConsultar . "\n./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias . " " . $LimiteConsultar . "\n";
                            fwrite($file, $command);
                            fclose($file);
                            $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
                            $resultadoServidores = $conexao->query($sqlServidores);
                            if (0 < $resultadoServidores->num_rows) {
                                $errors = [];
                                while ($row = $resultadoServidores->fetch_assoc()) {
                                    if ($row["subid"] == $categoriaid) {
                                        try {
                                            $connection = ssh2_connect($row["ip"], $row["porta"]);
                                            if (!$connection) {
                                                $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                                            } else {
                                                if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                                                    $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                                                    ssh2_disconnect($connection);
                                                } else {
                                                    if (!function_exists("ssh2_scp_send")) {
                                                        $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                                                        ssh2_disconnect($connection);
                                                    } else {
                                                        if (!ssh2_scp_send($connection, "../../home/modulos/EditarUsuario.sh", "EditarUsuario.sh", 493)) {
                                                            $errors[] = "Falha ao enviar o arquivo para o servidor";
                                                            ssh2_disconnect($connection);
                                                        } else {
                                                            $exec_command = "./EditarUsuario.sh >/dev/null 2>&1 &";
                                                            ssh2_exec($connection, $exec_command);
                                                            ssh2_disconnect($connection);
                                                        }
                                                    }
                                                }
                                            }
                                        } catch (ErrorException $e) {
                                            $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
                                        }
                                    }
                                }
                                if (!empty($errors)) {
                                    $_SESSION["error"] = implode("<br>", $errors);
                                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                                    exit;
                                }
                            }
                            $stmt = $conexao->prepare("UPDATE ssh_accounts SET senha=?, limite=?, expira=?, byid=?, mainid=?, categoriaid=?, login=? WHERE id=?");
                            $stmt->bind_param("sisiiisi", $senha, $LimiteConsultar, $data_expiracao, $iduser, $mainid, $categoriaid, $login, $id);
                            $stmt->execute();
                        } else {
                            echo "Login não encontrado na tabela 'ssh_accounts'.";
                            exit;
                        }
                    } else {
                        if ($tipo === "Validade") {
                            $dias1 = $dias;
                            $dias1 += 1;
                            $file = fopen("../../home/modulos/EditarUsuario.sh", "w");
                            $command = "./ExcluirExpiradoApi.sh " . $LoginConsultar . "\n./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias1 . " " . $LimiteConsultar . "\n";
                            fwrite($file, $command);
                            fclose($file);
                            $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
                            $resultadoServidores = $conexao->query($sqlServidores);
                            if (0 < $resultadoServidores->num_rows) {
                                $errors = [];
                                while ($row = $resultadoServidores->fetch_assoc()) {
                                    if ($row["subid"] == $categoriaid) {
                                        try {
                                            $connection = ssh2_connect($row["ip"], $row["porta"]);
                                            if (!$connection) {
                                                $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                                            } else {
                                                if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                                                    $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                                                    ssh2_disconnect($connection);
                                                } else {
                                                    if (!function_exists("ssh2_scp_send")) {
                                                        $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                                                        ssh2_disconnect($connection);
                                                    } else {
                                                        if (!ssh2_scp_send($connection, "../../home/modulos/EditarUsuario.sh", "EditarUsuario.sh", 493)) {
                                                            $errors[] = "Falha ao enviar o arquivo para o servidor";
                                                            ssh2_disconnect($connection);
                                                        } else {
                                                            $exec_command = "./EditarUsuario.sh >/dev/null 2>&1 &";
                                                            ssh2_exec($connection, $exec_command);
                                                            ssh2_disconnect($connection);
                                                        }
                                                    }
                                                }
                                            }
                                        } catch (ErrorException $e) {
                                            $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
                                        }
                                    }
                                }
                                if (!empty($errors)) {
                                    $_SESSION["error"] = implode("<br>", $errors);
                                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                                    exit;
                                }
                            }
                            $stmt = $conexao->prepare("UPDATE ssh_accounts SET senha=?, limite=?, expira=?, byid=?, mainid=?, categoriaid=?, login=? WHERE id=?");
                            $stmt->bind_param("sisiiisi", $senha, $LimiteConsultar, $data_expiracao, $iduser, $mainid, $categoriaid, $login, $id);
                            $stmt->execute();
                        } else {
                            echo "Login não encontrado na tabela 'ssh_accounts'.";
                            exit;
                        }
                    }
                }
            }
            $msg = [];
            $sql = "SELECT app FROM config WHERE byid = '" . $iduser . "'";
            $result = $conexao->query($sql);
            if (0 < $result->num_rows) {
                $row = $result->fetch_assoc();
                $link = $row["app"];
            }
            if ($stmt->execute()) {
                $stmtCategoria = $conexao->prepare("SELECT nome FROM categorias WHERE subid = ?");
                $stmtCategoria->bind_param("i", $categoriaid);
                $stmtCategoria->execute();
                $resultado = $stmtCategoria->get_result();
                $categoria = $resultado->fetch_assoc()["nome"];
                $stmtUsuario = $conexao->prepare("SELECT login, senha, limite, expira FROM ssh_accounts WHERE login = ?");
                $stmtUsuario->bind_param("s", $login);
                $stmtUsuario->execute();
                $resultadoUsuario = $stmtUsuario->get_result();
                $usuario = $resultadoUsuario->fetch_assoc();
                $logincp = $usuario["login"];
                $senhacp = $usuario["senha"];
                $limitecp = $usuario["limite"];
                $html = "<div>Usuário criado com sucesso.</div><br>";
                $html .= "<div>Login: <span id=\"login\">" . $logincp . "</span></div>";
                $html .= "<div>Senha: <span id=\"senha\">" . $senhacp . "</span></div>";
                $html .= "<div>Limite: <span id=\"limite\">" . $limitecp . "</span></div>";
                $html .= "<div>Validade: <span id=\"validade\">" . $dias . " Dias</span></div>";
                $html .= "<div>Categoria: <span id=\"categoria\">" . $categoria . "</span></div><br><br>";
                $html .= "<div class=\"button-container\">";
                $html .= "<button type=\"button\" class=\"btn btn-primary btn-sm\" style=\"background-color: #5e17eb;\" onclick=\"copyInformation()\">Copiar informações</button>";
                $html .= "</div><br>";
                $html .= "<div class=\"button-container\">";
                $html .= "<button type=\"button\" class=\"btn btn-primary btn-sm\" style=\"background-color: #5e17eb;\" onclick=\"sendToWhatsApp()\">Enviar para o WhatsApp</button>";
                $html .= "</div>";
                $msg = ["title" => "Sucesso!", "html" => $html, "icon" => "success", "showCloseButton" => true, "onClose" => "copyInformation()"];
                echo "<script>\r\n                var msg = " . json_encode($msg) . ";\r\n        \r\n                window.onload = function() {\r\n                    Swal.fire({\r\n                        title: msg.title,\r\n                        html: msg.html,\r\n                        icon: msg.icon,\r\n                        showCloseButton: msg.showCloseButton,\r\n                        onClose: msg.onClose,\r\n                        showConfirmButton: false // This line removes the \"OK\" button\r\n                    });\r\n                };\r\n        \r\n                function copyInformation() {\r\n                    var login = \"" . $logincp . "\";\r\n                    var senha = \"" . $senhacp . "\";\r\n                    var limite = \"" . $limitecp . "\";\r\n                    var validade = document.getElementById(\"validade\").innerText;\r\n                    var categoria = document.getElementById(\"categoria\").innerText;\r\n                    var link1 = \"Link do Aplicativo\";\r\n                    var link2 = \"" . $link . "\";\r\n        \r\n                    var information = \"Login: \" + login + \"\\n\" +\r\n                                      \"Senha: \" + senha + \"\\n\" +\r\n                                      \"Limite: \" + limite + \"\\n\" +\r\n                                      \"Validade: \" + validade + \"\\n\" +\r\n                                      \"Categoria: \" + categoria + \"\\n\\n\\n\" +\r\n                                      link1 + \"\\n\" + \r\n                                      link2;\r\n        \r\n                    if (navigator.clipboard && window.isSecureContext) {\r\n                        navigator.clipboard.writeText(information)\r\n                            .then(function() {\r\n                                Swal.fire({\r\n                                    title: \"Informações copiadas!\",\r\n                                    text: \"As informações foram copiadas para a área de transferência.\",\r\n                                    icon: \"success\",\r\n                                    timer: 2000,\r\n                                    showConfirmButton: false\r\n                                });\r\n                            })\r\n                            .catch(function(error) {\r\n                                Swal.fire({\r\n                                    title: \"Erro!\",\r\n                                    text: \"Ocorreu um erro ao copiar as informações: \" + error,\r\n                                    icon: \"error\"\r\n                                });\r\n                            });\r\n                    } else {\r\n                        var textArea = document.createElement(\"textarea\");\r\n                        textArea.value = information;\r\n                        document.body.appendChild(textArea);\r\n                        textArea.select();\r\n                        document.execCommand(\"copy\");\r\n                        document.body.removeChild(textArea);\r\n        \r\n                        Swal.fire({\r\n                            title: \"Informações copiadas!\",\r\n                            text: \"As informações foram copiadas para a área de transferência.\",\r\n                            icon: \"success\",\r\n                            timer: 2000,\r\n                            showConfirmButton: false\r\n                        });\r\n                    }\r\n                }\r\n        \r\n                function sendToWhatsApp() {\r\n                    var login = \"" . $logincp . "\";\r\n                    var senha = \"" . $senhacp . "\";\r\n                    var limite = \"" . $limitecp . "\";\r\n                    var validade = document.getElementById(\"validade\").innerText;\r\n                    var categoria = document.getElementById(\"categoria\").innerText;\r\n                    var link1 = \"Link do Aplicativo\";\r\n                    var link2 = \"" . $link . "\";\r\n        \r\n                    var information = \"Login: \" + login + \"\\n\" +\r\n                                      \"Senha: \" + senha + \"\\n\" +\r\n                                      \"Limite: \" + limite + \"\\n\" +\r\n                                      \"Validade: \" + validade + \"\\n\" +\r\n                                      \"Categoria: \" + categoria + \"\\n\\n\\n\" +\r\n                                      link1 + \"\\n\" + \r\n                                      link2;\r\n        \r\n                    var message = encodeURIComponent(information);\r\n                    var whatsappURL = \"https://wa.me/?text=\" + message;\r\n        \r\n                    window.open(whatsappURL);\r\n                }\r\n            </script>";
            }
        }
    } else {
        $_SESSION["error"] = "Usuário não encontrado.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
}
$stmt = $conexao->prepare("SELECT title FROM config WHERE byid = ?");
$stmt->bind_param("i", $iduser);
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
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
echo "\r\n\r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n        ";
include "../../menu.php";
echo "\r\n        <!-- Inicio -->\r\n\r\n        <center>\r\n            <div class=\"container-fluid py-5\">\r\n                <div class=\"col-lg-5\">\r\n                    <!-- Inicio -->\r\n\r\n                    <h2 class=\"card-title\">Editar Usuário</h2>\r\n                    <br>\r\n                    <br>\r\n\r\n                    <!-- HTML Form to get user data -->\r\n                    ";
if (isset($_SESSION["error"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["error"] . "' });</script>";
    unset($_SESSION["error"]);
}
echo "                    \r\n                    ";
if (isset($_SESSION["success"])) {
    echo "<script>Swal.fire({ icon: 'success', title: 'Sucesso!', html: '" . $_SESSION["success"] . "' });</script>";
    unset($_SESSION["success"]);
}
echo "                    \r\n                    ";
if (isset($_SESSION["resposta"])) {
    $resposta = $_SESSION["resposta"];
    $dataToCopy = "Dados atualizado com sucesso!\nLogin: " . $resposta["login"] . "\n" . "Senha: " . $resposta["senha"] . "\n" . "Limite: " . $resposta["limite"] . "\n" . "Validade: " . $resposta["dias"] . " Dias";
    echo "<script>\r\n                        function copyToClipboard() {\r\n                            var textToCopy = `" . $dataToCopy . "`;\r\n                            navigator.clipboard.writeText(textToCopy);\r\n                        }\r\n                    \r\n                        Swal.fire({\r\n                            title: 'Dados atualizado com sucesso!',\r\n                            html: `\r\n                                <h5>Login: " . $resposta["login"] . "</h5>\r\n                                <h5>Senha: " . $resposta["senha"] . "</h5>\r\n                                <h5>Limite: " . $resposta["limite"] . "</h5>\r\n                                <h5>Validade: " . $resposta["dias"] . " Dias</h5>\r\n                                <button class='btn btn-primary' onclick='copyToClipboard()'>Copiar Informações</button>\r\n                            `,\r\n                            icon: 'success',\r\n                            confirmButtonText: 'OK'\r\n                        });\r\n                        </script>";
    unset($_SESSION["resposta"]);
}
echo "\r\n\r\n\r\n                    <form class=\"login100-form validate-form\" action=\"\" method=\"post\" onsubmit=\"return validateForm();\">\r\n                        <!-- ... -->\r\n\r\n                        <h8 class=\"card-title\" style=\"font-family: Arial;\">Login</h8>\r\n                        <div class=\"form-group mb-4\">\r\n                            <div class=\"input-group\">\r\n                                <input class=\"form-control\" placeholder=\"Login\" type=\"text\" id=\"login\" name=\"login\" pattern=\"[a-zA-Z0-9]+\" title=\"Somente letras e números são permitidos.\" minlength=\"5\" maxlength=\"10\" required value=\"";
echo isset($LoginConsultar) ? $LoginConsultar : "";
echo "\">\r\n                            </div>\r\n                        </div>\r\n\r\n                        <h8 class=\"card-title\" style=\"font-family: Arial;\">Senha</h8>\r\n                        <div class=\"form-group mb-4\">\r\n                            <div class=\"input-group\">\r\n                                <input class=\"form-control\" placeholder=\"Senha\" type=\"password\" id=\"senha\" name=\"senha\" pattern=\"[a-zA-Z0-9]+\" title=\"Somente letras e números são permitidos.\" minlength=\"6\" maxlength=\"10\" required value=\"";
echo isset($SenhaConsultar) ? $SenhaConsultar : "";
echo "\">\r\n                            </div>\r\n                        </div>\r\n\r\n                        <h8 class=\"card-title\" style=\"font-family: Arial;\">Categorias</h8>\r\n                        <div class=\"form-group mb-4\">\r\n                            <div class=\"form-group\">\r\n                                <select class=\"form-control\" name=\"categoriaid\" id=\"categoriaid\">\r\n                                    ";
foreach ($categorias as $subid => $nomes) {
    echo "                                        ";
    foreach ($nomes as $nome) {
        echo "                                            <option value=\"";
        echo $subid;
        echo "\" ";
        if ($CategoriaConsultar == $subid) {
            echo "selected=\"selected\"";
        }
        echo ">\r\n                                                ";
        echo $nome;
        echo "                                            </option>\r\n                                        ";
    }
    echo "                                    ";
}
echo "                                </select>\r\n                            </div>\r\n\r\n                            <!-- ... -->\r\n\r\n                            <div class=\"row\">\r\n                                <div class=\"d-flex justify-content-between\">\r\n                                    <div class=\"col-md-6 mx-1\">\r\n                                  <button type=\"button\" class=\"btn btn-primary\" style=\"background-color: #5e17eb;\" data-toggle=\"modal\" data-target=\"#meuModal\">\r\n                                    Add dias e limite\r\n                                    </button>\r\n\r\n                                    </div>\r\n                                    <div class=\"col-md-6 mx-1\">\r\n                                        <button type=\"submit\" id=\"showAlertButton\" class=\"btn btn-primary\" name=\"submit\" style=\"background-color: #5e17eb;\">Editar Usuário</button>\r\n                                    </div>\r\n                                </div>\r\n                            </div>\r\n                            <div class=\"row mt-3\">\r\n                                <div class=\"col-md-12\">\r\n                                    <a href=\"excluirusuario.php?id=";
echo $id;
echo "\" class=\"btn btn-danger btn-md\" onclick=\"confirmarExclusao(event)\">Excluir Usuário</a>\r\n                                </div>\r\n                            </div>\r\n                    </form>\r\n                    \r\n                    <!-- Modal -->\r\n                    <div class=\"modal fade\" id=\"meuModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"meuModalLabel\" aria-hidden=\"true\">\r\n                        <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">\r\n                            <div class=\"modal-content\">\r\n                                <div class=\"modal-header text-center\">\r\n                                    <h4 class=\"modal-title w-100\" id=\"meuModalLabel\">Adicionar dias e limite</h4>\r\n                                    <button type=\"button\" class=\"btn-close\" style=\"background-color: #007bff; border: none;\" data-dismiss=\"modal\" aria-label=\"Close\"></button>\r\n                                </div>\r\n                                \r\n                                <div class=\"modal-body\">\r\n                                   <form id=\"meuForm\" method=\"GET\" action=\"editardias.php\" class=\"text-center\">\r\n                                        <input type=\"hidden\" name=\"id\" value=\"";
echo $id;
echo "\">\r\n                                    \r\n                                        <p class=\"card-title mt-4\" style=\"font-family: Arial;\">Limite</p>\r\n                                        <div class=\"form-group mb-4\">\r\n                                            <div class=\"input-group justify-content-center\">\r\n                                                <input class=\"form-control is-invalid\" style=\"width: 100px;\" placeholder=\"Limite\" type=\"number\" id=\"limite\"\r\n                                                    name=\"limite\" required value=\"";
echo isset($LimiteConsultar) ? $LimiteConsultar : "";
echo "\">\r\n                                                    <div class=\"invalid-feedback\">Por favor, insira um limite válido.</div>\r\n                                            </div>\r\n                                             \r\n                                        </div>\r\n                                    \r\n                                        <p class=\"card-title mt-4\" style=\"font-family: Arial;\">Dias de acesso</p>\r\n                                        <div class=\"form-group mb-4\">\r\n                                            <div class=\"input-group justify-content-center\">\r\n                                                <input class=\"form-control is-invalid\" style=\"width: 100px;\" placeholder=\"Dias\" type=\"number\" id=\"dias\"\r\n                                                    name=\"dias\" required value=\"";
echo isset($dias) ? $dias : "";
echo "\">\r\n                                                    <div class=\"invalid-feedback\">Por favor, insira um número válido de dias.</div>\r\n                                            </div>\r\n                                        </div>\r\n                                    \r\n                                        <div class=\"modal-footer justify-content-center\">\r\n                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Cancelar</button>\r\n                                            <button type=\"submit\" class=\"btn btn-primary\" style=\"background-color: #5e17eb;\">Salvar</button>\r\n                                        </div>\r\n                                    </form>\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n\r\n\r\n                \r\n\r\n                    <!-- Fim -->\r\n                </div>\r\n            </div>\r\n            </div>\r\n            </div>\r\n    </main>\r\n\r\n\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <!-- Adicione o script do Bootstrap no final do body -->\r\n    <script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\"></script>\r\n    <script src=\"https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js\"></script>\r\n    <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n        function validateForm() {\r\n            var login = document.getElementById(\"login\").value;\r\n            var senha = document.getElementById(\"senha\").value;\r\n\r\n            if (login === \"\" || senha === \"\") {\r\n                Swal.fire({\r\n                    icon: 'error',\r\n                    title: 'Erro!',\r\n                    text: 'Por favor, preencha todos os campos.',\r\n                    confirmButtonText: 'OK'\r\n                });\r\n                return false;\r\n            } else {\r\n                Swal.fire({\r\n                    title: 'Carregando...',\r\n                    html: '<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin fa-3x\"></i></div>',\r\n                    showCancelButton: false,\r\n                    showConfirmButton: false,\r\n                    allowOutsideClick: false,\r\n                    allowEscapeKey: false,\r\n                    allowEnterKey: false\r\n                });\r\n                return true;\r\n            }\r\n        }\r\n    </script>\r\n    <script>\r\n        function confirmarExclusao(event) {\r\n            event.preventDefault(); // Evita a execução padrão do link\r\n\r\n            Swal.fire({\r\n                title: 'Tem certeza que deseja excluir?',\r\n                text: 'Essa ação não pode ser desfeita.',\r\n                icon: 'warning',\r\n                showCancelButton: true,\r\n                confirmButtonColor: '#dc3545',\r\n                cancelButtonColor: '#6c757d',\r\n                confirmButtonText: 'Sim, excluir',\r\n                cancelButtonText: 'Cancelar'\r\n            }).then((result) => {\r\n                if (result.isConfirmed) {\r\n                    window.location.href = event.target.href; // Redireciona para a página de exclusão\r\n                }\r\n            });\r\n        }\r\n    </script>\r\n    \r\n\r\n    <script>\r\n        function confirmarExclusao(event) {\r\n            event.preventDefault();\r\n    \r\n            Swal.fire({\r\n                title: 'Tem certeza?',\r\n                text: 'Esta ação excluirá o usuário. Deseja continuar?',\r\n                icon: 'warning',\r\n                showCancelButton: true,\r\n                confirmButtonColor: '#d33',\r\n                cancelButtonColor: '#3085d6',\r\n                confirmButtonText: 'Sim, excluir!',\r\n                cancelButtonText: 'Cancelar'\r\n            }).then((result) => {\r\n                if (result.isConfirmed) {\r\n                    // If the user confirms the deletion, proceed with the deletion action\r\n                    window.location.href = event.target.href;\r\n                }\r\n            });\r\n        }\r\n    </script>\r\n    \r\n    <script>\r\n        \$(document).ready(function(){\r\n            var input = \$('#meuForm input');\r\n    \r\n            function checkInputValue(input){\r\n                var is_number = input.val();\r\n                if(is_number && is_number > 0){\r\n                    input.removeClass(\"is-invalid\").addClass(\"is-valid\");\r\n                } else{\r\n                    input.removeClass(\"is-valid\").addClass(\"is-invalid\");\r\n                }\r\n            }\r\n    \r\n            // check input value on document ready\r\n            checkInputValue(input);\r\n    \r\n            // check input value on input event\r\n            input.on('input', function() {\r\n                checkInputValue(\$(this));\r\n            });\r\n        });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>