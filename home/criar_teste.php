<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$resultado = mysqli_query($conexao, "SELECT maxtest FROM config WHERE byid = 1");
$minutos = mysqli_fetch_assoc($resultado)["maxtest"];
$horas = floor($minutos / 60);
$data_expiracao_unix = strtotime("+" . $horas . " hours");
$data_expiracao_mysql = date("Y-m-d H:i:s", $data_expiracao_unix);
$data_expiracao_formatada = date("Y-m-d H:i:s", $data_expiracao_unix);
$byid = $_GET["byid"];
$sql = "SELECT logo FROM config WHERE byid = 1 ";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    $row = $result->fetch_assoc();
    $logo = $row["logo"];
}
$stmt = $conexao->prepare("SELECT app FROM config WHERE byid = ?");
$stmt->bind_param("s", $byid);
$stmt->execute();
$stmt->bind_result($app);
$stmt->fetch();
$stmt->close();
$link = $app;
$query = "SELECT c.nome, c.subid \r\n          FROM atribuidos a \r\n          JOIN categorias c ON a.categoriaid = c.subid \r\n          WHERE a.userid = '" . $byid . "'";
$resultado = mysqli_query($conexao, $query);
if (0 < mysqli_num_rows($resultado)) {
    $categorias = [];
    while ($categoria = mysqli_fetch_assoc($resultado)) {
        $subid = $categoria["subid"];
        $nome = $categoria["nome"];
        $categorias[$subid][] = $nome;
    }
} else {
    $query = "SELECT * FROM accounts WHERE id = '" . $byid . "'";
    $resultado = mysqli_query($conexao, $query);
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
$id = $_GET["id"];
$sql = "SELECT * FROM links WHERE link_id='" . $id . "'";
$result = $conexao->query($sql);
if ($result->num_rows == 0) {
    exit("Link inválido.");
}
$_SESSION["LAST_ACTIVITY"] = time();
if (isset($_POST["submit"])) {
    $login = $_POST["login"];
    $senha = $_POST["senha"];
    $limite = $_POST["limite"];
    $dias = $_POST["dias"];
    $byid = $_POST["byid"];
    $mainid = $_POST["mainid"];
    $categoriaid = $_POST["categoriaid"];
    $sql = "SELECT * FROM ssh_accounts WHERE login='" . $login . "'";
    $result = $conexao->query($sql);
    if (0 < $result->num_rows) {
        $_SESSION["error"] = "Usuário já existe.";
        header("Location: criar_teste.php?id=" . $id . "&byid=" . $byid . "&mainid=" . $mainid . "&categoriaid=" . $categoriaid);
        exit;
    }
    $ip = $_SERVER["REMOTE_ADDR"];
    $userAgent = $_SERVER["HTTP_USER_AGENT"];
    if (isset($_COOKIE["identificadorDispositivo"])) {
        $identificadorDispositivo = $_COOKIE["identificadorDispositivo"];
    } else {
        $identificadorDispositivo = md5($ip . $userAgent);
        setcookie("identificadorDispositivo", $identificadorDispositivo, time() + 630720000, "/");
    }
    $sql = "SELECT * FROM tabela_bloqueio WHERE ip = '" . $identificadorDispositivo . "'";
    $result = $conexao->query($sql);
    if (0 < $result->num_rows) {
        $_SESSION["error"] = "Você atingiu o limite de criação de contas para o seu dispositivo.";
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    $sql = "INSERT INTO tabela_bloqueio (ip, data_bloqueio, byid) VALUES ('" . $identificadorDispositivo . "', NOW(), '" . $byid . "')";
    if ($conexao->query($sql) === false) {
        echo "Erro ao inserir o identificador do dispositivo no banco de dados: " . $conexao->error;
    }
    $sql = "SELECT * FROM tabela_bloqueio WHERE ip = '" . $ip . "'";
    $result = $conexao->query($sql);
    if (0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        $bloqueio = strtotime($row["data_bloqueio"]);
        $agora = time();
        $diferenca = $agora - $bloqueio;
        if ($diferenca < 86400) {
            $_SESSION["error"] = "Você atingiu o limite de criação de contas para o seu dispositivo.";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    if ($byid != 1) {
        $userid = $byid;
        $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = ? AND categoriaid = ?";
        $stmt_atribuidos = $conexao->prepare($sql_atribuidos);
        $stmt_atribuidos->bind_param("ii", $userid, $categoriaid);
        $stmt_atribuidos->execute();
        $result_atribuidos = $stmt_atribuidos->get_result();
        if (0 < $result_atribuidos->num_rows) {
            $atribuido = $result_atribuidos->fetch_assoc();
            if ($atribuido["tipo"] == "Validade") {
                $sql_usuarios = "SELECT COUNT(*) AS total FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
                $stmt_usuarios = $conexao->prepare($sql_usuarios);
                $stmt_usuarios->bind_param("ii", $userid, $categoriaid);
                $stmt_usuarios->execute();
                $result_usuarios = $stmt_usuarios->get_result();
                $usuarios_criados = $result_usuarios->fetch_assoc()["total"];
                if ($atribuido["limite"] <= $usuarios_criados) {
                    $_SESSION["error"] = "Limite de usuários excedido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
                if ($atribuido["limite"] < $limite) {
                    $_SESSION["error"] = "O limite fornecido excede o limite permitido para a categoria selecionada.";
                    header("Location: " . $_SERVER["HTTP_REFERER"]);
                    exit;
                }
            } else {
                if ($atribuido["tipo"] == "Credito") {
                    if (1 <= $atribuido["limitetest"]) {
                        $limite_ssh = $atribuido["limitetest"] - $limite;
                        if ($atribuido["limitetest"] < $limite) {
                            $_SESSION["error"] = "O limite fornecido excede o limite de Creditos disponíveis para a categoria selecionada.";
                            header("Location: " . $_SERVER["HTTP_REFERER"]);
                            exit;
                        }
                        $sql_update = "UPDATE atribuidos SET limitetest = ? WHERE userid = ? AND categoriaid = ?";
                        $stmt_update = $conexao->prepare($sql_update);
                        $stmt_update->bind_param("iii", $limite_ssh, $userid, $categoriaid);
                        $stmt_update->execute();
                        $stmt_update->close();
                    } else {
                        $_SESSION["error"] = "Usuário possui apenas " . $atribuido["limitetest"] . " Credito(s) disponível(is) para criar esse acesso.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                }
            }
        }
    }
    $file = fopen("../home/modulos/CriarTesteSsh.sh", "w");
    $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $dias . " 1\n";
    fwrite($file, $command);
    fclose($file);
    $sqlServidores = "SELECT * FROM servidores WHERE subid = " . $categoriaid;
    $resultadoServidores = $conexao->query($sqlServidores);
    $errors = [];
    if (0 < $resultadoServidores->num_rows) {
        while ($row = $resultadoServidores->fetch_assoc()) {
            if ($row["subid"] == $categoriaid) {
                $connection = ssh2_connect($row["ip"], $row["porta"]);
                if (!$connection) {
                    $errors[] = "Não foi possível conectar ao servidor " . $row["ip"];
                } else {
                    if (!ssh2_auth_password($connection, $row["usuario"], $row["senha"])) {
                        $errors[] = "Usuário ou senha do servidor " . $row["ip"] . " estão incorretos";
                    } else {
                        if (!ssh2_scp_send($connection, "../home/modulos/CriarTesteSsh.sh", "CriarTesteSsh.sh", 493)) {
                            $errors[] = "Falha ao enviar o arquivo para o servidor " . $row["ip"];
                        } else {
                            $exec_command = "./CriarTesteSsh.sh >/dev/null 2>&1 &";
                            ssh2_exec($connection, $exec_command);
                        }
                        ssh2_disconnect($connection);
                    }
                }
            }
        }
    }
    if (!empty($errors)) {
        $_SESSION["error"] = implode("<br>", $errors);
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }
    $sql = "INSERT INTO tabela_bloqueio (ip, byid, data_bloqueio) VALUES ('" . $ip . "', " . $byid . ", NOW())";
    $conexao->query($sql);
    $sql = "INSERT INTO ssh_accounts (login, senha, limite, expira, byid, mainid, categoriaid) \r\n        VALUES ('" . $login . "', '" . $senha . "', 1, '" . $data_expiracao_mysql . "', '" . $byid . "', '" . $mainid . "', '" . $categoriaid . "')";
    $msg = [];
    if ($conexao->query($sql) === true) {
        $resultado = mysqli_query($conexao, "SELECT nome FROM categorias WHERE subid = " . $categoriaid);
        $categoria = mysqli_fetch_assoc($resultado)["nome"];
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
        $html .= "<div>Validade: <span id=\"validade\">" . $horas . " Horas</span></div>";
        $html .= "<div>Categoria: <span id=\"categoria\">" . $categoria . "</span></div><br><br>";
        $html .= "<button type=\"button\" class=\"btn btn-primary btn-sm\" onclick=\"copyInformation()\">Copiar informações</button>";
        $html .= "</div><br>";
        $html .= "<div class=\"button-container\">";
        $html .= "<button type=\"button\" class=\"btn btn-primary btn-sm\" onclick=\"sendToWhatsApp()\">Enviar para o WhatsApp</button>";
        $html .= "</div>";
        $msg = ["title" => "Sucesso!", "html" => $html, "icon" => "success", "showCloseButton" => true, "onClose" => "copyInformation()"];
        echo "<script>\r\n        var msg = " . json_encode($msg) . ";\r\n\r\n        window.onload = function() {\r\n            Swal.fire({\r\n                title: msg.title,\r\n                html: msg.html,\r\n                icon: msg.icon,\r\n                showCloseButton: msg.showCloseButton,\r\n                onClose: msg.onClose,\r\n                showConfirmButton: false // This line removes the \"OK\" button\r\n            });\r\n        };\r\n\r\n        function copyInformation() {\r\n            var login = \"" . $logincp . "\";\r\n            var senha = \"" . $senhacp . "\";\r\n            var limite = \"" . $limitecp . "\";\r\n            var validade = document.getElementById(\"validade\").innerText;\r\n            var categoria = document.getElementById(\"categoria\").innerText;\r\n            var link1 = \"Link do Aplicativo\";\r\n            var link2 = \"" . $link . "\";\r\n\r\n            var information = \"Login: \" + login + \"\\n\" +\r\n                              \"Senha: \" + senha + \"\\n\" +\r\n                              \"Limite: \" + limite + \"\\n\" +\r\n                              \"Validade: \" + validade + \"\\n\" +\r\n                              \"Categoria: \" + categoria + \"\\n\\n\\n\" +\r\n                              link1 + \"\\n\" + \r\n                              link2;\r\n\r\n            if (navigator.clipboard && window.isSecureContext) {\r\n                navigator.clipboard.writeText(information)\r\n                    .then(function() {\r\n                        Swal.fire({\r\n                            title: \"Informações copiadas!\",\r\n                            text: \"As informações foram copiadas para a área de transferência.\",\r\n                            icon: \"success\",\r\n                            timer: 2000,\r\n                            showConfirmButton: false\r\n                        });\r\n                    })\r\n                    .catch(function(error) {\r\n                        Swal.fire({\r\n                            title: \"Erro!\",\r\n                            text: \"Ocorreu um erro ao copiar as informações: \" + error,\r\n                            icon: \"error\"\r\n                        });\r\n                    });\r\n            } else {\r\n                var textArea = document.createElement(\"textarea\");\r\n                textArea.value = information;\r\n                document.body.appendChild(textArea);\r\n                textArea.select();\r\n                document.execCommand(\"copy\");\r\n                document.body.removeChild(textArea);\r\n\r\n                Swal.fire({\r\n                    title: \"Informações copiadas!\",\r\n                    text: \"As informações foram copiadas para a área de transferência.\",\r\n                    icon: \"success\",\r\n                    timer: 2000,\r\n                    showConfirmButton: false\r\n                });\r\n            }\r\n        }\r\n\r\n        function sendToWhatsApp() {\r\n            var login = \"" . $logincp . "\";\r\n            var senha = \"" . $senhacp . "\";\r\n            var limite = \"" . $limitecp . "\";\r\n            var validade = document.getElementById(\"validade\").innerText;\r\n            var categoria = document.getElementById(\"categoria\").innerText;\r\n            var link1 = \"Link do Aplicativo\";\r\n            var link2 = \"" . $link . "\";\r\n\r\n            var information = \"Login: \" + login + \"\\n\" +\r\n                              \"Senha: \" + senha + \"\\n\" +\r\n                              \"Limite: \" + limite + \"\\n\" +\r\n                              \"Validade: \" + validade + \"\\n\" +\r\n                              \"Categoria: \" + categoria + \"\\n\\n\\n\" +\r\n                              link1 + \"\\n\" + \r\n                              link2;\r\n\r\n            var message = encodeURIComponent(information);\r\n            var whatsappURL = \"https://wa.me/?text=\" + message;\r\n\r\n            window.open(whatsappURL);\r\n        }\r\n    </script>";
    }
}
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n<head>\r\n    <meta charset=\"utf-8\" />\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">\r\n    <link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"../assets/img/apple-icon.png\">\r\n    <link rel=\"icon\" type=\"image/png\" href=\"../assets/img/favicon.png\">\r\n    <title>\r\n        ";
echo $titulo;
echo "    </title>\r\n    <link rel=\"stylesheet\" href=\"../assets/css/login.css\">\r\n    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\" />\r\n    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.min.css\">\r\n    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.all.min.js\"></script>\r\n</head>\r\n\r\n\r\n<body>\r\n\r\n    <style>\r\n        .progress {\r\n            height: 20px;\r\n            background-color: #f5f5f5;\r\n            border-radius: 4px;\r\n            overflow: hidden;\r\n        }\r\n\r\n        .progress-bar {\r\n            width: 100%;\r\n            background-color: #337ab7;\r\n            animation: progress-bar-animation 1s linear infinite;\r\n        }\r\n\r\n        @keyframes progress-bar-animation {\r\n            0% {\r\n                width: 0%;\r\n            }\r\n\r\n            100% {\r\n                width: 100%;\r\n            }\r\n        }\r\n    </style>\r\n\r\n    <div class=\"content\">\r\n        <div class=\"text\">\r\n            <a class=\"navbar m-0\">\r\n                <img src=\"";
echo $logo;
echo "\" alt=\"Logo\" width=\"auto\" height=\"150\">\r\n            </a>\r\n        </div>\r\n        <h3 class=\"card-title\" style=\"font-family: Arial;\">Crie seu teste agora mesmo. O teste é grátis.</h3><br>\r\n\r\n        ";
if (isset($_SESSION["error"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["error"] . "', showConfirmButton: false, timer: 4000 });</script>";
    unset($_SESSION["error"]);
}
echo "\r\n        ";
$randomString = generaterandomstring(6);
$login = $randomString;
$senha = $randomString;
echo "\r\n        <form class=\"login100-form validate-form\" action=\"\" method=\"post\" onsubmit=\"return validateForm();\">\r\n\r\n            <input type=\"hidden\" name=\"byid\" value=\"";
echo htmlspecialchars($_GET["byid"]);
echo "\">\r\n            <input type=\"hidden\" name=\"mainid\" value=\"";
echo htmlspecialchars($_GET["mainid"]);
echo "\">\r\n\r\n            <div class=\"form-group mb-4 container form-field\">\r\n                <div class=\"row\">\r\n                    <div class=\"col\">\r\n                        <div class=\"form-group\">\r\n                            <select class=\"form-control form-select\" name=\"categoriaid\" id=\"categoriaid\">\r\n                                ";
foreach ($categorias as $subid => $nomes) {
    echo "                                    ";
    foreach ($nomes as $nome) {
        echo "                                        <option value=\"";
        echo $subid;
        echo "\" ";
        if ($atribuido["categoriaid"] == $subid) {
            echo "selected=\"selected\"";
        }
        echo ">\r\n                                            ";
        echo $nome;
        echo "                                        </option>\r\n                                    ";
    }
    echo "                                ";
}
echo "                            </select>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n\r\n            <div class=\"field\">\r\n                <input class=\"form-control\" placeholder=\"Login\" type=\"text\" id=\"login\" name=\"login\" pattern=\"[a-zA-Z0-9]+\" title=\"Somente letras e números são permitidos.\" minlength=\"4\" maxlength=\"";
echo $maxtext;
echo "\" required value=\"";
echo $login;
echo "\">\r\n                <span class=\"fas fa-user\"></span>\r\n            </div><br>\r\n\r\n            <div class=\"field\">\r\n                <input class=\"form-control\" placeholder=\"Senha\" type=\"password\" id=\"senha\" name=\"senha\" pattern=\"[a-zA-Z0-9]+\" title=\"Somente letras e números são permitidos.\" minlength=\"4\" maxlength=\"";
echo $maxtext;
echo "\" required value=\"";
echo $senha;
echo "\">\r\n                <span class=\"fas fa-lock\"></span>\r\n            </div>\r\n\r\n            <input class=\"form-control\" type=\"number\" id=\"limite\" name=\"limite\" value=\"1\" hidden>\r\n            <input class=\"form-control\" type=\"number\" id=\"dias\" name=\"dias\" value=\"1\" hidden>\r\n\r\n            <button type=\"submit\" id=\"showAlertButton\" name=\"submit\" class=\"btn btn-primary\">CRIAR TESTE</button>\r\n\r\n            <div class=\"sign-up\">\r\n                Já é membro?\r\n                <a href=\"../index.php\" role=\"button\">ENTRAR</a>\r\n            </div>\r\n\r\n        </form>\r\n\r\n\r\n        <h3 class=\"card-title\" style=\"font-family: Arial;\">Baixe nosso aplicativo.</h3><br>\r\n        <!-- Insere a imagem e o link no HTML -->\r\n        <a href=\"";
echo $link;
echo "\">\r\n            <button><i class=\"fas fa-download\" style=\"font-size: 1.5em;\"></i>Download</button>\r\n        </a>\r\n\r\n    </div>\r\n\r\n    <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/popper.min.js\"></script>\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/plugins/perfect-scrollbar.min.js\"></script>\r\n    <script src=\"../assets/js/plugins/smooth-scrollbar.min.js\"></script>\r\n    <script src=\"../assets/js/plugins/chartjs.min.js\"></script>\r\n    <script async defer src=\"https://buttons.github.io/buttons.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script>\r\n        function validateForm() {\r\n            var login = document.getElementById(\"login\").value;\r\n            var senha = document.getElementById(\"senha\").value;\r\n\r\n            if (login === \"\" || senha === \"\") {\r\n                Swal.fire({\r\n                    icon: 'error',\r\n                    title: 'Erro!',\r\n                    text: 'Por favor, preencha todos os campos.',\r\n                    confirmButtonText: 'OK'\r\n                });\r\n                return false;\r\n            } else {\r\n                Swal.fire({\r\n                    title: 'Carregando...',\r\n                    html: '<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin fa-3x\"></i></div>',\r\n                    showCancelButton: false,\r\n                    showConfirmButton: false,\r\n                    allowOutsideClick: false,\r\n                    allowEscapeKey: false,\r\n                    allowEnterKey: false\r\n                });\r\n                return true;\r\n            }\r\n        }\r\n    </script>\r\n\r\n\r\n\r\n</body>\r\n\r\n</html>";
function generateRandomString($length)
{
    $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $numbers = "0123456789";
    $randomString = "";
    for ($i = 0; $i < 3; $i++) {
        $randomString .= $letters[rand(0, strlen($letters) - 1)];
    }
    for ($i = 0; $i < 3; $i++) {
        $randomString .= $numbers[rand(0, strlen($numbers) - 1)];
    }
    return $randomString;
}

?>