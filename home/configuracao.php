<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if (mysqli_connect_errno()) {
    exit("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    echo "<script> window.location.href='../logout.php'; </script>";
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    echo "<script> window.location.href = '../logout.php'; </script>";
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
$sql = "SELECT COLUMN_NAME\r\n        FROM INFORMATION_SCHEMA.COLUMNS\r\n        WHERE TABLE_NAME = 'config' AND COLUMN_NAME = 'maxtext'";
$result = $conexao->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE config\r\n            ADD maxtext VARCHAR(255) DEFAULT '12'";
    if ($conexao->query($sql) !== true) {
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $app = $_POST["app"];
    $title = $_POST["title"];
    $logo = $_POST["logo"];
    $stmt = mysqli_prepare($conexao, "SELECT COUNT(*) FROM config WHERE byid=?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["iduser"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if (0 < $count) {
        $stmt = mysqli_prepare($conexao, "UPDATE config SET app=?, title=?, byid=? WHERE byid=?");
        mysqli_stmt_bind_param($stmt, "ssii", $app, $title, $_SESSION["iduser"], $_SESSION["iduser"]);
    } else {
        $stmt = mysqli_prepare($conexao, "INSERT INTO config (app, title, byid) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $app, $title, $_SESSION["iduser"]);
    }
    if (mysqli_stmt_execute($stmt)) {
        if ($_SESSION["nivel"] == 3) {
            $maxtest = $_POST["maxtest"];
            $maxcredit = $_POST["maxcredit"];
            $maxtext = $_POST["maxtext"];
            $stmt_additional = mysqli_prepare($conexao, "SELECT COUNT(*) FROM config WHERE byid=?");
            mysqli_stmt_bind_param($stmt_additional, "i", $_SESSION["iduser"]);
            mysqli_stmt_execute($stmt_additional);
            mysqli_stmt_bind_result($stmt_additional, $count_additional);
            mysqli_stmt_fetch($stmt_additional);
            mysqli_stmt_close($stmt_additional);
            if (0 < $count_additional) {
                $stmt_additional = mysqli_prepare($conexao, "UPDATE config SET maxtest=?, maxcredit=?, maxtext=? WHERE byid=?");
                mysqli_stmt_bind_param($stmt_additional, "sssi", $maxtest, $maxcredit, $maxtext, $_SESSION["iduser"]);
            } else {
                $stmt_additional = mysqli_prepare($conexao, "INSERT INTO config (maxtest, maxcredit, maxtext, byid) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt_additional, "sssi", $maxtest, $maxcredit, $maxtext, $_SESSION["iduser"]);
            }
            if (!mysqli_stmt_execute($stmt_additional)) {
                echo "Erro ao atualizar dados adicionais: " . mysqli_error($conexao);
            }
            mysqli_stmt_close($stmt_additional);
        }
        $_SESSION["config"] = "Dados atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar dados: " . mysqli_error($conexao);
    }
    mysqli_close($conexao);
}
echo "\r\n\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n    <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n \r\n    ";
include "../menu.php";
echo "\r\n                  <div class=\"container-fluid py-4\">\r\n                      <div class=\"col-lg-14\">\r\n                        <!-- Inicio -->\r\n                    <center>\r\n                        <div class=\"card card-plain mt-4\">\r\n                            <h3>Configuração do Painel</h3>\r\n                            <br>\r\n                  \r\n                  \r\n                                ";
if (isset($_SESSION["config"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["config"] . "' });</script>";
    unset($_SESSION["config"]);
}
echo "                \r\n              \r\n                \r\n\r\n             ";
$iduser = $_SESSION["iduser"];
$sql_titulo = "SELECT title FROM config";
$resultado_titulo = mysqli_query($conexao, $sql_titulo);
$sql_app = "SELECT app FROM config WHERE byid = '" . $iduser . "'";
$resultado_app = mysqli_query($conexao, $sql_app);
if (0 < mysqli_num_rows($resultado_titulo)) {
    $row_titulo = mysqli_fetch_assoc($resultado_titulo);
}
if (0 < mysqli_num_rows($resultado_app)) {
    $row_app = mysqli_fetch_assoc($resultado_app);
}
$sql_maxtest = "SELECT maxtest FROM config WHERE byid = ?";
$stmt_maxtest = mysqli_prepare($conexao, $sql_maxtest);
mysqli_stmt_bind_param($stmt_maxtest, "i", $iduser);
mysqli_stmt_execute($stmt_maxtest);
mysqli_stmt_bind_result($stmt_maxtest, $maxtest);
mysqli_stmt_fetch($stmt_maxtest);
mysqli_stmt_close($stmt_maxtest);
$sql_maxcredit = "SELECT maxcredit FROM config WHERE byid = ?";
$stmt_maxcredit = mysqli_prepare($conexao, $sql_maxcredit);
mysqli_stmt_bind_param($stmt_maxcredit, "i", $iduser);
mysqli_stmt_execute($stmt_maxcredit);
mysqli_stmt_bind_result($stmt_maxcredit, $maxcredit);
mysqli_stmt_fetch($stmt_maxcredit);
mysqli_stmt_close($stmt_maxcredit);
$sql_maxtext = "SELECT maxtext FROM config WHERE byid = ?";
$stmt_maxtext = mysqli_prepare($conexao, $sql_maxtext);
mysqli_stmt_bind_param($stmt_maxtext, "i", $iduser);
mysqli_stmt_execute($stmt_maxtext);
mysqli_stmt_bind_result($stmt_maxtext, $maxtext);
mysqli_stmt_fetch($stmt_maxtext);
mysqli_stmt_close($stmt_maxtext);
echo "              <center>\r\n            <div class=\"card-body\">  \r\n                <form class=\"login100-form validate-form\" action=\"\" method=\"post\">\r\n                    <h6>Titulo do Painel</h6>\r\n                  <div class=\"form-group mb-4\">\r\n                    <div class=\"input-group\">\r\n                        <input type=\"text\" class=\"form-control\" name=\"title\" placeholder=\"Titulo do Painel\" value=\"";
echo $row_titulo["title"];
echo "\" ";
echo $_SESSION["nivel"] == 3 ? "" : "readonly";
echo " required>\r\n                    </div>\r\n                </div>\r\n\r\n                    \r\n                    <h6>Link do Aplicativo</h6>\r\n                    <div class=\"form-group mb-4\">\r\n                        <div class=\"input-group\">\r\n                            <input type=\"text\" class=\"form-control\" name=\"app\" placeholder=\"Link do Aplicativo\" value=\"";
echo $row_app["app"];
echo "\" required>\r\n                        </div>\r\n                    </div>\r\n                    \r\n                  ";
if ($_SESSION["nivel"] == 3) {
    echo "                 \r\n                        <h6>Limite de minutos para teste</h6>\r\n                        <div class=\"form-group mb-4\">\r\n                            <div class=\"input-group\">\r\n                                <input type=\"number\" class=\"form-control\" name=\"maxtest\" placeholder=\"Limite de minutos para teste\" value=\"";
    echo $maxtest;
    echo "\" required>\r\n                            </div>\r\n                        </div>\r\n                \r\n                        <h6>Limite de dias crédito</h6>\r\n                        <div class=\"form-group mb-4\">\r\n                            <div class=\"input-group\">\r\n                                <input type=\"number\" class=\"form-control\" name=\"maxcredit\" placeholder=\"Limite de dias crédito\" value=\"";
    echo $maxcredit;
    echo "\" required>\r\n                            </div>\r\n                        </div>\r\n                        \r\n                        <h6>Limite para o login e senha</h6>\r\n                        <div class=\"form-group mb-4\">\r\n                            <div class=\"input-group\">\r\n                                <input type=\"number\" class=\"form-control\" name=\"maxtext\" placeholder=\"Limite de caracteres para o login e senha\" value=\"";
    echo $maxtext;
    echo "\" required>\r\n                            </div>\r\n                        </div>\r\n                    ";
}
echo "\r\n\r\n                \r\n                    <button class=\"btn btn-primary\" style=\"background-color: #5e17eb;\" border: none;\" name=\"atualizar\" id=\"atualizar\" value=\"atualizar\">\r\n                        Atualizar\r\n                    </button>\r\n                </form>\r\n                </center>\r\n\r\n\r\n                            </div>\r\n                        </div>\r\n                        <!--FIM-->\r\n                    </div>\r\n                </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n   <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    </script>\r\n   \r\n</body>\r\n\r\n</html>";

?>