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
if (!isset($_GET["id"]) || !isset($_GET["userid"]) || !isset($_GET["categoriaid"])) {
    echo "<script> window.location.href='listarrev.php'; </script>";
    exit;
}
$id = $_GET["id"];
$userid = $_GET["userid"];
$categoriaid1 = $_GET["categoriaid"];
$iduser = $_SESSION["iduser"];
$query = "SELECT * FROM atribuidos WHERE id = " . $id;
$resultado = mysqli_query($conexao, $query);
if (mysqli_num_rows($resultado) == 0) {
    echo "<script> window.location.href = 'listarrev.php'; </script>";
    exit;
}
$atribuido = mysqli_fetch_assoc($resultado);
$categoriaid = $atribuido["categoriaid"];
$query = "SELECT * FROM categorias";
$resultado = mysqli_query($conexao, $query);
$categorias = [];
while ($categoria = mysqli_fetch_assoc($resultado)) {
    $categorias[$categoria["subid"]] = $categoria["nome"];
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST["valor"];
    $limite = $_POST["limite"];
    $limitetest = $_POST["limitetest"];
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
            if ($atribuido["categoriaid"] == $categoriaid) {
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
    $expira = (new DateTime())->add(new DateInterval("P" . $dias . "D"))->add(new DateInterval("PT24H"))->format("Y-m-d H:i:s");
    if ($_SESSION["iduser"] != 1) {
        $sql_atribuidos = "SELECT * FROM atribuidos WHERE userid = ? AND categoriaid = ?";
        $stmt_atribuidos = $conexao->prepare($sql_atribuidos);
        $stmt_atribuidos->bind_param("ii", $iduser, $categoriaid);
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
                            $_SESSION["erroredit"] = "O limite fornecido excede o limite de créditos disponíveis para a categoria selecionada.";
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
                        $stmt_update->bind_param("iiii", $new_limit, $new_limit_test, $iduser, $categoriaid);
                        $stmt_update->execute();
                        $stmt_update->close();
                    } else {
                        $_SESSION["erroredit"] = "Usuário não possui créditos suficientes para adicionar.";
                        header("Location: " . $_SERVER["HTTP_REFERER"]);
                        exit;
                    }
                }
            }
        }
    }
    if ($atribuido["tipo"] !== "Credito") {
        $sql_usuarios = "SELECT SUM(limite) AS total_limite FROM ssh_accounts WHERE byid = ? AND categoriaid = ?";
        $stmt_usuarios = $conexao->prepare($sql_usuarios);
        $stmt_usuarios->bind_param("ii", $userid, $categoriaid);
        $stmt_usuarios->execute();
        $result_usuarios = $stmt_usuarios->get_result();
        $usuarios_criados = $result_usuarios->fetch_assoc()["total_limite"];
        if ($limite < $usuarios_criados) {
            $_SESSION["erroredit"] = "Limite não pode ser diminuído, a atribuição possui mais usuários criados do que o limite fornecido.";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    $query = "SELECT limite, limitetest, tipo FROM atribuidos WHERE id = " . $id;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Não foi possível obter os dados existentes: " . mysqli_error($conexao));
    }
    $row = mysqli_fetch_assoc($resultado);
    $limiteAtual = $row["limite"];
    $limitetestAtual = $row["limitetest"];
    $tipo = $row["tipo"];
    if ($tipo == "Credito") {
        $limiteNovo = $limiteAtual + $limite;
        $limitetestNovo = $limitetestAtual + $limitetest;
    } else {
        $limiteNovo = $limite;
        $limitetestNovo = $limitetest;
    }
    $query = "UPDATE atribuidos SET valor = '" . $valor . "', limite = '" . $limiteNovo . "', limitetest = '" . $limitetestNovo . "', expira = '" . $expira . "', subrev = '" . $subrev . "' WHERE id = " . $id;
    $resultado = mysqli_query($conexao, $query);
    if (!$resultado) {
        exit("Não foi possível atualizar os dados: " . mysqli_error($conexao));
    }
    $sql = "SELECT suspenso FROM atribuidos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $suspenso = $resultado->fetch_assoc()["suspenso"];
    $stmt->close();
    if ($suspenso == 1) {
        $sql = "UPDATE atribuidos SET suspenso = 0 WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $stmt->close();
        $sql = "SELECT * FROM ssh_accounts WHERE byid = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows == 0) {
            $_SESSION["revenda"] = "<div>Atribuição editada com sucesso!</div>";
            header("Location: listarrev.php");
            exit;
        }
        $sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores WHERE subid = " . $categoriaid;
        $stmt_servidor = $conexao->prepare($sql_servidor);
        $stmt_servidor->execute();
        $result_servidor = $stmt_servidor->get_result();
        $servidores = [];
        while ($row = $result_servidor->fetch_assoc()) {
            $servidores[] = $row;
        }
        $conta_excluida = false;
        $user_list = "";
        while ($row = $resultado->fetch_assoc()) {
            $expira = new DateTime($row["expira"]);
            $dataAtual = new DateTime();
            if ($expira >= $dataAtual) {
                $diasRestantes = $dataAtual->diff($expira)->days;
                $login = $row["login"];
                $senha = $row["senha"];
                $limite = $row["limite"];
                $user_list .= $login . "|" . $senha . "|" . $limite . "|" . $diasRestantes . " ";
                var_dump($user_list);
                $conta_excluida = true;
            }
        }
        $tasks = [];
        foreach ($servidores as $server) {
            if (isset($server["ip"]) && isset($server["porta"]) && isset($server["usuario"]) && isset($server["senha"])) {
                $tasks[] = function () use($server, $user_list) {
                    $connection = ssh2_connect($server["ip"], $server["porta"]);
                    if (!$connection) {
                        echo "Falha ao conectar ao servidor SSH2: " . $server["ip"];
                    } else {
                        $login_result = ssh2_auth_password($connection, $server["usuario"], $server["senha"]);
                        if (!$login_result) {
                            echo "Falha na autenticação SSH2 para o servidor: " . $server["ip"];
                        } else {
                            $usuarios = explode(" ", $user_list);
                            foreach ($usuarios as $usuario) {
                                $dados = explode("|", $usuario);
                                list($login, $senha, $limite, $diasRestantes) = $dados;
                                $command = "./SshturboMakeAccount.sh " . $login . " " . $senha . " " . $diasRestantes . " " . $limite;
                                $stream = ssh2_exec($connection, $command);
                                stream_set_blocking($stream, true);
                                $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                                $output = stream_get_contents($stream_out);
                                fclose($stream);
                                if (strpos($output, "Error:") !== false) {
                                    echo "Erro ao executar o comando SSH no servidor: " . $server["ip"] . " - " . $output;
                                }
                            }
                            return NULL;
                        }
                    }
                };
            }
        }
        foreach ($tasks as $task) {
            $task();
        }
    }
    $_SESSION["atribuicao"] = "<div>Atribuição editada com sucesso!</div>";
    header("Location: atribuicao.php?id=" . $userid);
    exit;
} else {
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
    echo "\r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
    include "../../header.php";
    echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n  <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n    ";
    include "../../menu.php";
    echo "\r\n\r\n      <center>\r\n        <div class=\"container-fluid py-6\">\r\n          <div class=\"col-lg-5\">\r\n            <!-- Inicio -->\r\n            <center>\r\n              <h2 class=\"card-title py-6\">Editar Atribuicão</h2>\r\n            </center>\r\n\r\n            ";
    if (isset($_SESSION["erroredit"])) {
        echo "<script>Swal.fire({\r\n                icon: 'error',\r\n                title: 'Erro!',\r\n                html: '" . $_SESSION["erroredit"] . "',\r\n                showConfirmButton: true,\r\n                confirmButtonText: 'OK'\r\n              });</script>";
        unset($_SESSION["erroredit"]);
    }
    echo "\r\n\r\n\r\n            <div class=\"card-body\">\r\n\r\n              <!-- Inicio -->\r\n              <center>\r\n                <form method=\"post\" action=\"\">\r\n\r\n                  <h8 class=\"card-title\" style=\"font-family: Arial;\">Permitir Sub-revenda?</h8>\r\n                  <div class=\"form-group\">\r\n                    <select class=\"form-control\" name=\"subrev\" id=\"subrev\">\r\n                      <option value=\"1\" ";
    if ($atribuido["subrev"] == "1") {
        echo "selected=\"selected\"";
    }
    echo ">Sim\r\n                      </option>\r\n                      <option value=\"0\" ";
    if ($atribuido["subrev"] == "0") {
        echo "selected=\"selected\"";
    }
    echo ">Não\r\n                      </option>\r\n                    </select>\r\n                  </div>\r\n                  \r\n                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Limite</h8>\r\n                    <div class=\"form-group\">\r\n                      ";
    if ($atribuido["tipo"] == "Credito") {
        echo "                        <input type=\"number\" class=\"form-control\" name=\"limite\" id=\"limite\" value=\"0\">\r\n                      ";
    } else {
        echo "                        <input type=\"number\" class=\"form-control\" name=\"limite\" id=\"limite\" value=\"";
        echo $atribuido["limite"];
        echo "\">\r\n                      ";
    }
    echo "                    </div>\r\n                    <script>\r\n                      var input = document.getElementById(\"limite\");\r\n                      input.addEventListener(\"input\", function(event) {\r\n                        var value = this.value;\r\n                        var sanitizedValue = value.replace(/[^0-9]/g, \"\"); // Remove caracteres não numéricos\r\n                        this.value = sanitizedValue;\r\n                      });\r\n                    </script>\r\n                    \r\n                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Limite de teste</h8>\r\n                    ";
    if ($atribuido["tipo"] == "Credito") {
        echo "                      <!-- Não mostra o campo limitetest para o tipo \"Crédito\" -->\r\n                      <div class=\"form-group\">\r\n                        <input type=\"number\" class=\"form-control\" name=\"limitetest\" id=\"limitetest\" value=\"0\">\r\n                      </div>\r\n                    ";
    } else {
        echo "                      <div class=\"form-group\">\r\n                        <input type=\"number\" class=\"form-control\" name=\"limitetest\" id=\"limitetest\" value=\"";
        echo $atribuido["limitetest"];
        echo "\">\r\n                      </div>\r\n                    ";
    }
    echo "                    <script>\r\n                      var input = document.getElementById(\"limitetest\");\r\n                      input.addEventListener(\"input\", function(event) {\r\n                        var value = this.value;\r\n                        var sanitizedValue = value.replace(/[^0-9]/g, \"\"); // Remove caracteres não numéricos\r\n                        this.value = sanitizedValue;\r\n                      });\r\n                    </script>\r\n\r\n\r\n\r\n\r\n\r\n                  ";
    if ($atribuido["tipo"] == "Validade") {
        echo "                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Dias</h8>\r\n                    <div class=\"form-group\">\r\n                      ";
        $expira = $atribuido["expira"];
        $dataAtual = new DateTime();
        $dataExpiracao = new DateTime($expira);
        $diferenca = $dataExpiracao->diff($dataAtual);
        $valorDias = $diferenca->days;
        if ($valorDias < 0) {
            $valorDias = 0;
        }
        echo "<input type=\"number\" class=\"form-control\" id=\"dias\" name=\"dias\" value=\"" . $valorDias . "\">";
        echo "                    </div>\r\n                  ";
    } else {
        echo "                    ";
        $valorDias = 999;
        echo "<input type=\"number\" class=\"form-control\" id=\"dias\" name=\"dias\" value=\"" . $valorDias . "\" style=\"display: none;\">";
        echo "                  ";
    }
    echo "                   <script>\r\n                        var input = document.getElementById(\"dias\");\r\n                        input.addEventListener(\"input\", function(event) {\r\n                            var value = this.value;\r\n                            var sanitizedValue = value.replace(/[^0-9]/g, \"\"); // Remove caracteres não numéricos\r\n                            this.value = sanitizedValue;\r\n                        });\r\n                    </script>\r\n\r\n\r\n                    <script src=\"https://cdn.jsdelivr.net/npm/autonumeric@4.1.0/dist/autoNumeric.min.js\"></script>\r\n                    \r\n                    <h8 class=\"card-title\" style=\"font-family: Arial;\">Valor</h8>\r\n                    <div class=\"form-group\">\r\n                        <input type=\"text\" class=\"form-control\" name=\"valor\" id=\"valor\" value=\"";
    echo number_format($atribuido["valor"], 2, ",", ".");
    echo "\">\r\n                    </div>\r\n                    <script>\r\n                      // Obtém o elemento de entrada de texto pelo ID\r\n                      var input = document.getElementById('valor');\r\n                      \r\n                      // Inicializa o AutoNumeric no campo de entrada\r\n                      new AutoNumeric(input, {\r\n                        decimalPlaces: 2,\r\n                        decimalCharacter: ',',\r\n                        digitGroupSeparator: '.'\r\n                      });\r\n                    </script>\r\n\r\n                  <div class=\"row\">\r\n                    <div class=\"d-flex justify-content-between\">\r\n                      <div class=\"col-md-6 mx-1\">\r\n                        <a class=\"btn btn-secondary\" href=\"atribuicao.php?id=";
    $id1 = $atribuido["userid"];
    echo $id1;
    echo "\">Cancelar</a>\r\n                      </div>\r\n                      <div class=\"col-md-6 mx-1\">\r\n                        <button type=\"submit\" style=\"background-color: #007bff; border: none;\" class=\"btn btn-primary\" name=\"submit\">Salvar</button>\r\n                      </div>\r\n                    </div>\r\n                  </div>\r\n\r\n                    <div class=\"row mt-3\">\r\n                        <div class=\"col-md-12\">\r\n                            <a href=\"#\" id=\"delete-button\" class=\"btn btn-danger btn-md\">Remover Atribuição</a>\r\n                        </div>\r\n                    </div>\r\n                  \r\n                </form>\r\n              </center>\r\n              <!--FIM-->\r\n            </div>\r\n          </div>\r\n        </div>\r\n    </div>\r\n    </div>\r\n  </main>\r\n\r\n\r\n\r\n  <!--   Core JS Files   -->\r\n  <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n  <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n  <script src=\"../../assets/js/menu.js\"></script>\r\n  <script src=\"../../assets/js/page.js\"></script>\r\n  <!-- Inclua o arquivo JavaScript do jQuery -->\r\n  <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n  <script>\r\n    \$(document).ready(function() {\r\n      // Adicione a funcionalidade de busca na tabela\r\n      \$('#searchInput').on('keyup', function() {\r\n        var value = \$(this).val().toLowerCase();\r\n        \$('#usuario tbody tr').filter(function() {\r\n          var rowText = \$(this).text().toLowerCase();\r\n          var searchTerms = value.split(' ');\r\n          var found = true;\r\n          for (var i = 0; i < searchTerms.length; i++) {\r\n            if (rowText.indexOf(searchTerms[i]) === -1) {\r\n              found = false;\r\n              break;\r\n            }\r\n          }\r\n          \$(this).toggle(found);\r\n        });\r\n      });\r\n    });\r\n  </script>\r\n  \r\n  <script>\r\ndocument.querySelector(\"#delete-button\").addEventListener('click', function (e) {\r\n    e.preventDefault(); // Previne o comportamento padrão do clique no link\r\n\r\n    Swal.fire({\r\n        title: 'Tem certeza?',\r\n        text: \"Você não poderá reverter isso!\",\r\n        icon: 'warning',\r\n        showCancelButton: true,\r\n        confirmButtonColor: '#3085d6',\r\n        cancelButtonColor: '#d33',\r\n        confirmButtonText: 'Sim, excluir!'\r\n    }).then((result) => {\r\n        if (result.isConfirmed) {\r\n            // Caso o usuário confirme a exclusão, redireciona para a página de exclusão\r\n            window.location.href = \"excluiratribuicao.php?id=";
    echo $id;
    echo "&userid=";
    echo $userid;
    echo "&categoriaid=";
    echo $categoriaid1;
    echo "\";\r\n        }\r\n    })\r\n});\r\n</script>\r\n\r\n</body>\r\n\r\n</html>";
}

?>