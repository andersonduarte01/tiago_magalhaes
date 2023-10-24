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
$byid = $_SESSION["iduser"];
$mainid = $_SESSION["mainid"];
$query = "SELECT c.nome, c.subid \r\n              FROM atribuidos a \r\n              JOIN categorias c ON a.categoriaid = c.subid \r\n              WHERE a.userid = ?";
$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "s", $byid);
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
    mysqli_stmt_bind_param($stmt, "s", $byid);
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
    }
}
$_SESSION["LAST_ACTIVITY"] = time();
$resultado = mysqli_query($conexao, "SELECT maxcredit FROM config");
$minutos_maximos = mysqli_fetch_assoc($resultado)["maxcredit"];
$dias_minimos = 1;
$dias_maximos = $minutos_maximos;
if (isset($_POST["submit"])) {
    $login = mysqli_real_escape_string($conexao, $_POST["login"]);
    $senha = mysqli_real_escape_string($conexao, $_POST["senha"]);
    $limite = mysqli_real_escape_string($conexao, $_POST["limite"]);
    $dias = mysqli_real_escape_string($conexao, $_POST["dias"]);
    $categoriaid = mysqli_real_escape_string($conexao, $_POST["categoriaid"]);
    $sql = "SELECT maxtext FROM config WHERE byid = ?";
    $stmt = $conexao->prepare($sql);
    $byiduser = 1;
    $stmt->bind_param("i", $byiduser);
    $stmt->execute();
    $result = $stmt->get_result();
    if (0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        $maxtext = $row["maxtext"];
    } else {
        $maxtext = 255;
    }
    $stmt->close();
    if ($maxtext < strlen($login)) {
        $_SESSION["error"] = "O login excede o limite de caracteres permitido (" . $maxtext . " caracteres).";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($maxtext < strlen($senha)) {
        $_SESSION["error"] = "A senha excede o limite de caracteres permitido (" . $maxtext . " caracteres).";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($_SESSION["iduser"] != 1) {
        $query = "SELECT categoriaid, suspenso, tipo, expira FROM atribuidos WHERE userid = ?";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "s", $byid);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $categoriaEncontrada = false;
        while ($atribuido = mysqli_fetch_assoc($resultado)) {
            if ($atribuido["categoriaid"] == $categoriaid) {
                $categoriaEncontrada = true;
                if ($atribuido["suspenso"] == 1) {
                    $_SESSION["error"] = "A atribuição está suspensa temporariamente e não poderá criar novas contas. Por favor, entre em contato com o administrador para obter mais informações e resolver essa questão.";
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
    if ($dias === "") {
        $_SESSION["error"] = "Por favor, insira um valor de dias.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($atribuido["tipo"] == "Credito" && ($dias < $dias_minimos || $dias_maximos < $dias)) {
        if ($dias < $dias_minimos) {
            $_SESSION["error"] = "Limite mínimo de dias não é válido. Por favor, insira um valor mínimo de 1 dia.";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
        if ($dias_maximos < $dias) {
            $_SESSION["error"] = "Limite máximo de dias excedido. Por favor, insira um valor até " . $minutos_maximos . " dias.";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    $data_atual = new DateTime();
    $data_atual->setTimezone(new DateTimeZone("America/Sao_Paulo"));
    $data_expiracao = $data_atual->modify("+" . $dias . " days")->format("Y-m-d H:i:s");
    $sql_ssh = "SELECT * FROM ssh_accounts WHERE login=?";
    $stmt_ssh = $conexao->prepare($sql_ssh);
    $stmt_ssh->bind_param("s", $login);
    $stmt_ssh->execute();
    $result_ssh = $stmt_ssh->get_result();
    $sql_accounts = "SELECT * FROM accounts WHERE login=?";
    $stmt_accounts = $conexao->prepare($sql_accounts);
    $stmt_accounts->bind_param("s", $login);
    $stmt_accounts->execute();
    $result_accounts = $stmt_accounts->get_result();
    if (0 < $result_ssh->num_rows || 0 < $result_accounts->num_rows) {
        $_SESSION["error"] = "Usuário já existe.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    if ($_SESSION["iduser"] != 1) {
        $userid = $_SESSION["iduser"];
        $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = ? AND categoriaid = ?";
        $stmt_atribuidos = $conexao->prepare($sql_atribuidos);
        $stmt_atribuidos->bind_param("ii", $userid, $categoriaid);
        $stmt_atribuidos->execute();
        $result_atribuidos = $stmt_atribuidos->get_result();
        if (0 < $result_atribuidos->num_rows) {
            $atribuido = $result_atribuidos->fetch_assoc();
            if ($atribuido["tipo"] == "Validade") {
                $sql_limite = "SELECT limite FROM atribuidos WHERE byid = ?";
                $stmt_limite = $conexao->prepare($sql_limite);
                $stmt_limite->bind_param("i", $userid);
                $stmt_limite->execute();
                $result_limite = $stmt_limite->get_result();
                if (0 < $result_limite->num_rows) {
                    $limite_atribuidos = $result_limite->fetch_assoc()["limite"];
                    $sql_usuarios = "SELECT SUM(limite) AS total_limite FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
                    $stmt_usuarios = $conexao->prepare($sql_usuarios);
                    $stmt_usuarios->bind_param("ii", $userid, $categoriaid);
                    $stmt_usuarios->execute();
                    $result_usuarios = $stmt_usuarios->get_result();
                    $usuarios_criados = $result_usuarios->fetch_assoc()["total_limite"];
                    $total_limite = $usuarios_criados + $limite_atribuidos;
                    if ($atribuido["limite"] <= $total_limite) {
                        $_SESSION["error"] = "Limite de usuários excedido para a categoria selecionada.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                    if ($atribuido["limite"] < $total_limite + $limite) {
                        $_SESSION["error"] = "O limite fornecido excede o limite permitido para a categoria selecionada.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                    if ($limite_atribuidos < $limite) {
                        $_SESSION["error"] = "O limite fornecido excede o limite de crédito disponível para a categoria selecionada.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                } else {
                    $sql_usuarios = "SELECT SUM(limite) AS total_limite FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
                    $stmt_usuarios = $conexao->prepare($sql_usuarios);
                    $stmt_usuarios->bind_param("ii", $userid, $categoriaid);
                    $stmt_usuarios->execute();
                    $result_usuarios = $stmt_usuarios->get_result();
                    $usuarios_criados = $result_usuarios->fetch_assoc()["total_limite"];
                    if ($atribuido["limite"] <= $usuarios_criados) {
                        $_SESSION["error"] = "Limite de usuários excedido para a categoria selecionada.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                    if ($atribuido["limite"] < $usuarios_criados + $limite) {
                        $_SESSION["error"] = "O limite fornecido excede o limite permitido para a categoria selecionada.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                }
            } else {
                if ($atribuido["tipo"] == "Credito") {
                    if ($limite <= $atribuido["limite"]) {
                        $novo_limite_ssh = $atribuido["limite"] - $limite;
                        $sql_update = "UPDATE atribuidos SET limite = ? WHERE userid = ? AND categoriaid = ?";
                        $stmt_update = $conexao->prepare($sql_update);
                        $stmt_update->bind_param("iii", $novo_limite_ssh, $userid, $categoriaid);
                        $stmt_update->execute();
                        $stmt_update->close();
                    } else {
                        $_SESSION["error"] = "Você só possui apenas " . $atribuido["limite"] . " crédito(s) disponível(is) para criar esse acesso.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                }
            }
        }
    }
    $dias1 = $dias;
    $dias1 += 1;
    function createSSHUserFile($login, $senha, $dias1, $limite)
    {
        $file = fopen("../../home/modulos/CriarUsuarioSsh.sh", "w");
        $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias1 . " " . $limite . "\n";
        fwrite($file, $command);
        fclose($file);
    }
    function handleSSHConnection($row, $categoriaid)
    {
        $errors = [];
        if ($row["subid"] == $categoriaid) {
            try {
                $connection = ssh2_connect($row["ip"], $row["porta"]);
                if (!$connection) {
                    $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                    return $errors;
                }
                if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                    $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                    ssh2_disconnect($connection);
                    return $errors;
                }
                if (!function_exists("ssh2_scp_send")) {
                    $errors[] = "A função ssh2_scp_send não está disponível no servidor";
                    ssh2_disconnect($connection);
                    return $errors;
                }
                if (!ssh2_scp_send($connection, "../../home/modulos/CriarUsuarioSsh.sh", "CriarUsuarioSsh.sh", 493)) {
                    $errors[] = "Falha ao enviar o arquivo para o servidor";
                    ssh2_disconnect($connection);
                    return $errors;
                }
                $exec_command = "./CriarUsuarioSsh.sh >/dev/null 2>&1 &";
                ssh2_exec($connection, $exec_command);
                ssh2_disconnect($connection);
            } catch (ErrorException $e) {
                $errors[] = "Erro de conexão no servidor " . $row["ip"] . ": " . $e->getMessage();
            }
        }
        return $errors;
    }
    $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
    $resultadoServidores = $conexao->query($sqlServidores);
    if (0 < $resultadoServidores->num_rows) {
        $errors = [];
        createSSHUserFile($login, $senha, $dias1, $limite);
        while ($row = $resultadoServidores->fetch_assoc()) {
            $errors = array_merge($errors, handleSSHConnection($row, $categoriaid));
        }
        if (!empty($errors)) {
            $_SESSION["error"] = implode("<br>", $errors);
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    $stmt = $conexao->prepare("INSERT INTO ssh_accounts (login, senha, limite, expira, byid, mainid, categoriaid) \r\n    VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisisi", $login, $senha, $limite, $data_expiracao, $byid, $mainid, $categoriaid);
    $msg = [];
    $sql = "SELECT app FROM config WHERE byid = '" . $byid . "'";
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
        $html .= "<div>Validade: <span id=\"validade\">" . $dias . " Dia</span></div>";
        $html .= "<div>Categoria: <span id=\"categoria\">" . $categoria . "</span></div><br><br>";
        $html .= "<div class=\"button-container\">";
        $html .= "<button type=\"button\" class=\"btn btn-primary btn-sm\" style=\"background-color: #5e17eb;\" onclick=\"copyInformation()\">Copiar informações</button>";
        $html .= "</div><br>";
        $html .= "<div class=\"button-container\">";
        $html .= "<button type=\"button\" class=\"btn btn-primary btn-sm\" style=\"background-color: #5e17eb;\" onclick=\"sendToWhatsApp()\">Enviar para o WhatsApp</button>";
        $html .= "</div>";
        $msg = ["title" => "Sucesso!", "html" => $html, "icon" => "success", "showCloseButton" => true, "onClose" => "copyInformation()"];
        echo "<script>\r\n            var msg = " . json_encode($msg) . ";\r\n    \r\n            window.onload = function() {\r\n                Swal.fire({\r\n                    title: msg.title,\r\n                    html: msg.html,\r\n                    icon: msg.icon,\r\n                    showCloseButton: msg.showCloseButton,\r\n                    onClose: msg.onClose,\r\n                    showConfirmButton: false // This line removes the \"OK\" button\r\n                });\r\n            };\r\n    \r\n            function copyInformation() {\r\n                var login = \"" . $logincp . "\";\r\n                var senha = \"" . $senhacp . "\";\r\n                var limite = \"" . $limitecp . "\";\r\n                var validade = document.getElementById(\"validade\").innerText;\r\n                var categoria = document.getElementById(\"categoria\").innerText;\r\n                var link1 = \"Link do Aplicativo\";\r\n                var link2 = \"" . $link . "\";\r\n    \r\n                var information = \"Login: \" + login + \"\\n\" +\r\n                                  \"Senha: \" + senha + \"\\n\" +\r\n                                  \"Limite: \" + limite + \"\\n\" +\r\n                                  \"Validade: \" + validade + \"\\n\" +\r\n                                  \"Categoria: \" + categoria + \"\\n\\n\\n\" +\r\n                                  link1 + \"\\n\" + \r\n                                  link2;\r\n    \r\n                if (navigator.clipboard && window.isSecureContext) {\r\n                    navigator.clipboard.writeText(information)\r\n                        .then(function() {\r\n                            Swal.fire({\r\n                                title: \"Informações copiadas!\",\r\n                                text: \"As informações foram copiadas para a área de transferência.\",\r\n                                icon: \"success\",\r\n                                timer: 2000,\r\n                                showConfirmButton: false\r\n                            });\r\n                        })\r\n                        .catch(function(error) {\r\n                            Swal.fire({\r\n                                title: \"Erro!\",\r\n                                text: \"Ocorreu um erro ao copiar as informações: \" + error,\r\n                                icon: \"error\"\r\n                            });\r\n                        });\r\n                } else {\r\n                    var textArea = document.createElement(\"textarea\");\r\n                    textArea.value = information;\r\n                    document.body.appendChild(textArea);\r\n                    textArea.select();\r\n                    document.execCommand(\"copy\");\r\n                    document.body.removeChild(textArea);\r\n    \r\n                    Swal.fire({\r\n                        title: \"Informações copiadas!\",\r\n                        text: \"As informações foram copiadas para a área de transferência.\",\r\n                        icon: \"success\",\r\n                        timer: 2000,\r\n                        showConfirmButton: false\r\n                    });\r\n                }\r\n            }\r\n    \r\n            function sendToWhatsApp() {\r\n                var login = \"" . $logincp . "\";\r\n                var senha = \"" . $senhacp . "\";\r\n                var limite = \"" . $limitecp . "\";\r\n                var validade = document.getElementById(\"validade\").innerText;\r\n                var categoria = document.getElementById(\"categoria\").innerText;\r\n                var link1 = \"Link do Aplicativo\";\r\n                var link2 = \"" . $link . "\";\r\n    \r\n                var information = \"Login: \" + login + \"\\n\" +\r\n                                  \"Senha: \" + senha + \"\\n\" +\r\n                                  \"Limite: \" + limite + \"\\n\" +\r\n                                  \"Validade: \" + validade + \"\\n\" +\r\n                                  \"Categoria: \" + categoria + \"\\n\\n\\n\" +\r\n                                  link1 + \"\\n\" + \r\n                                  link2;\r\n    \r\n                var message = encodeURIComponent(information);\r\n                var whatsappURL = \"https://wa.me/?text=\" + message;\r\n    \r\n                window.open(whatsappURL);\r\n            }\r\n        </script>";
    }
}
$stmt = $conexao->prepare("SELECT title FROM config");
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
echo "<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n  \r\n<main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n    \r\n ";
include "../../menu.php";
echo "\r\n            \r\n<!-- Inicio -->\r\n\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-5\">\r\n                        <!-- Inicio -->\r\n\r\n                          <h2 class=\"card-title\">Gerar Usuário</h2>\r\n                                <br>\r\n                                <br>\r\n                              \r\n                               <!-- HTML Form to get user data -->\r\n                             ";
if (isset($_SESSION["error"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["error"] . "' });</script>";
    unset($_SESSION["error"]);
}
echo "                            <form class=\"login100-form validate-form\" action=\"\" method=\"post\" onsubmit=\"return validateForm();\">\r\n                               \r\n                                <h8 class=\"card-title\" style=\"font-family: Arial;\">Categorias</h8>\r\n                                <div class=\"form-group mb-4\">\r\n                                <div class=\"form-group\">\r\n                                    <select class=\"form-control\" name=\"categoriaid\" id=\"categoriaid\" required>\r\n                                        ";
foreach ($categorias as $subid => $nomes) {
    echo "                                            ";
    foreach ($nomes as $nome) {
        echo "                                                <option value=\"";
        echo $subid;
        echo "\" ";
        if ($atribuido["categoriaid"] == $subid) {
            echo "selected=\"selected\"";
        }
        echo ">\r\n                                                    ";
        echo $nome;
        echo "                                                </option>\r\n                                            ";
    }
    echo "                                        ";
}
echo "                                    </select>\r\n                                </div>\r\n                                \r\n                                <h8 class=\"card-title\" style=\"font-family: Arial;\">Login</h8>\r\n                                <div class=\"form-group mb-4\">\r\n                                    <div class=\"input-group\">\r\n                                        <input class=\"form-control\" placeholder=\"Login\" type=\"text\" id=\"login\"\r\n                                            name=\"login\" pattern=\"[a-zA-Z0-9]+\"\r\n                                            title=\"Somente letras e números são permitidos.\" minlength=\"5\"\r\n                                            maxlength=\"";
echo $maxtext;
echo "\" required>\r\n                                    </div>\r\n                                </div>\r\n                                \r\n                                <h8 class=\"card-title\" style=\"font-family: Arial;\">Senha</h8>\r\n                                <div class=\"form-group mb-4\">\r\n                                    <div class=\"input-group\">\r\n                                        <input class=\"form-control\" placeholder=\"Senha\" type=\"password\" id=\"senha\"\r\n                                            name=\"senha\" pattern=\"[a-zA-Z0-9]+\"\r\n                                            title=\"Somente letras e números são permitidos.\" minlength=\"6\"\r\n                                            maxlength=\"";
echo $maxtext;
echo "\" required>\r\n                                    </div>\r\n                                </div>\r\n\r\n                                   \r\n                                \r\n                                <h8 class=\"card-title\" style=\"font-family: Arial;\">Limite</h8>\r\n                                <div class=\"form-group mb-4\">\r\n                                    <div class=\"input-group\">\r\n                                        \r\n                                        <input class=\"form-control\" placeholder=\"Limite\" type=\"number\" id=\"limite\"\r\n                                            name=\"limite\" value=\"1\" required>\r\n                                    </div>\r\n                                </div>\r\n                                 <script>\r\n                                    var input = document.getElementById(\"limite\");\r\n                                    input.addEventListener(\"input\", function(event) {\r\n                                        var value = this.value;\r\n                                        var sanitizedValue = value.replace(/[^0-9]/g, \"\"); // Remove caracteres não numéricos\r\n                                        this.value = sanitizedValue;\r\n                                    });\r\n                                </script>\r\n                                \r\n                                <h8 class=\"card-title\" style=\"font-family: Arial;\">Dias de acesso</h8>\r\n                                <div class=\"form-group mb-4\">\r\n                                    <div class=\"input-group\">\r\n                                        \r\n                                        <input class=\"form-control\" placeholder=\"Dias de acesso\" type=\"number\" id=\"dias\"\r\n                                            name=\"dias\" value=\"1\" required>\r\n                                    </div>\r\n                                </div>\r\n                                 <script>\r\n                                    var input = document.getElementById(\"dias\");\r\n                                    input.addEventListener(\"input\", function(event) {\r\n                                        var value = this.value;\r\n                                        var sanitizedValue = value.replace(/[^0-9]/g, \"\"); // Remove caracteres não numéricos\r\n                                        this.value = sanitizedValue;\r\n                                    });\r\n                                </script>\r\n\r\n                                <button type=\"submit\" id=\"showAlertButton\" class=\"btn btn-primary\"\r\n                                    name=\"submit\" style=\"background-color: #5e17eb;\">Gerar Usuário</button><br><br>\r\n\r\n\r\n                            </form>\r\n                        <!-- Fim -->\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n   <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n     <script>\r\n      function validateForm() {\r\n        var login = document.getElementById(\"login\").value;\r\n        var senha = document.getElementById(\"senha\").value;\r\n    \r\n        if (login === \"\" || senha === \"\") {\r\n          Swal.fire({\r\n            icon: 'error',\r\n            title: 'Erro!',\r\n            text: 'Por favor, preencha todos os campos.',\r\n            confirmButtonText: 'OK'\r\n          });\r\n          return false;\r\n        } else {\r\n          Swal.fire({\r\n            title: 'Carregando...',\r\n            html: '<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin fa-3x\"></i></div>',\r\n            showCancelButton: false,\r\n            showConfirmButton: false,\r\n            allowOutsideClick: false,\r\n            allowEscapeKey: false,\r\n            allowEnterKey: false\r\n          });\r\n          return true;\r\n        }\r\n      }\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>