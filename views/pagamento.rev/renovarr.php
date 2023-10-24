<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
$valor_add = $_SESSION["valoradd"];
include "../../verificar.php";
include "../../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] != 2 && $_SESSION["nivel"] != 3) {
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
$validade = [];
$_SESSION["LAST_ACTIVITY"] = time();
$limite = $_SESSION["limite"];
$valor = $_SESSION["valor"];
$sql4 = "SELECT * FROM accounts WHERE id = '" . $_SESSION["byid"] . "'";
$result4 = $conexao->query($sql4);
if (0 < $result4->num_rows) {
    while ($row4 = $result4->fetch_assoc()) {
        $_SESSION["valorlogin"] = $row4["valorrevenda"];
        $valorlogin = $_SESSION["valorlogin"];
    }
}
$valoradd = $_SESSION["valoradd"];
$limite = $_SESSION["limite"];
$valor = $limite * $valorlogin;
$data = $_SESSION["validade"];
$data = date("d/m/Y", strtotime($data));
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
echo "\r\n            <center>\r\n                <div class=\"container-fluid py-5\">\r\n                    <div class=\"col-lg-12\">\r\n                        <!-- Inicio -->\r\n\r\n                        <div class=\"bloco\">\r\n                            <h1>INFORMAÇÕES</h1>\r\n                            <b>N° Pedido:</b>\r\n                            ";
echo $_SESSION["payment_id"];
echo "<br>\r\n                            <b>Valor:</b> R\$\r\n                            ";
echo $valoradd;
echo "<br><br>\r\n                            <center>\r\n                                <div id=\"timer\"></div>\r\n\r\n                                <div class=\"teste\"></div>\r\n                                <img class=\"qr_code\" src=\"data:image/png;base64,";
echo $_SESSION["qr_code_base64"];
echo "\" style=\"text-align: center; width: 210px; height: 210px;\">\r\n                                <br>\r\n                                <br>\r\n                                <input type=\"text\" id=\"foo\" value=\"";
echo $_SESSION["qr_code"];
echo "\"><button\r\n                                    class=\"btn-copy\"><i class=\"fa fa-copy\" data-clipboard-target=\"#foo\"></i></button>\r\n                                <div id=\"snackbar\">Copiado com sucesso!</div>\r\n                            </center>\r\n                            <br><br>\r\n\r\n                            <p class=\"info-text\">Atenção, não feche esta aba antes de fazer o pagamento!</p>\r\n                        </div>\r\n                        <a class=\"btn btn-danger\" href=\"add.php\">Cancelar</a>\r\n                    </div>\r\n                    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js\"\r\n                        integrity=\"sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==\"\r\n                        crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\"></script>\r\n\r\n                        <script>\r\n\r\n                            \$pasta = \$_SERVER['REQUEST_URI'];\r\n                            \$pasta = explode(\$pasta, '/');\r\n                            \$pasta = \$pasta[1];\r\n                            setTimeout(() => {\r\n                                window.location = \"../../nivel.php\";\r\n    \r\n                            }, 602000);\r\n                            var timer2 = \"10:01\";\r\n                            var interval = setInterval(function () {\r\n\r\n\r\n                            var timer = timer2.split(':');\r\n                            //by parsing integer, I avoid all extra string processing\r\n                            var minutes = parseInt(timer[0], 10);\r\n                            var seconds = parseInt(timer[1], 10);\r\n                            --seconds;\r\n                            minutes = (seconds < 0) ? --minutes : minutes;\r\n                            if (minutes < 0) clearInterval(interval);\r\n                            seconds = (seconds < 0) ? 59 : seconds;\r\n                            seconds = (seconds < 10) ? '0' + seconds : seconds;\r\n                            //minutes = (minutes < 10) ?  minutes : minutes;\r\n                            \$('#timer').html(minutes + ':' + seconds);\r\n                            timer2 = minutes + ':' + seconds;\r\n                        }, 1000);\r\n                        error_reporting(0);\r\n\r\n\r\n\r\n\r\n                    </script>\r\n                    <script type=\"text/javascript\">\r\n\r\n                        //Calling function\r\n                        repeatAjax();\r\n\r\n\r\n                        function repeatAjax() {\r\n                            jQuery.ajax({\r\n                                type: \"POST\",\r\n                                url: 'verifyr.php',\r\n                                dataType: 'text',\r\n                                success: function (resp) {\r\n                                    if (resp == 'Aprovado') {\r\n                                        \$(\".qr_code\").attr('src', 'https://www.pngplay.com/wp-content/uploads/2/Approved-PNG-Photos.png');\r\n                                        window.location = \"aprovado.php\";\r\n\r\n                                        jQuery('.teste').html(resp);\r\n                                    }\r\n\r\n                                },\r\n                                complete: function () {\r\n                                    setTimeout(repeatAjax, 1000); //After completion of request, time to redo it after a second\r\n                                }\r\n                            });\r\n                        }\r\n                    </script>\r\n                    <script type=\"text/javascript\">\r\n                        \$(\".btn-copy\").click(() => {\r\n                            var copyText = document.getElementById(\"foo\");\r\n\r\n                            /* Select the text field */\r\n                            copyText.select();\r\n                            copyText.setSelectionRange(0, 99999); /* For mobile devices */\r\n\r\n                            /* Copy the text inside the text field */\r\n                            navigator.clipboard.writeText(copyText.value);\r\n\r\n                            /* Alert the copied text */\r\n                            toastText()\r\n                        });\r\n\r\n\r\n                        function toastText() {\r\n                            // Get the snackbar DIV\r\n                            var x = document.getElementById(\"snackbar\");\r\n\r\n                            // Add the \"show\" class to DIV\r\n                            x.className = \"show\";\r\n\r\n                            // After 3 seconds, remove the show class from DIV\r\n                            setTimeout(function () { x.className = x.className.replace(\"show\", \"\"); }, 3000);\r\n                        }\r\n                    </script>\r\n\r\n                </div>\r\n        </div>\r\n\r\n        <!-- Fim -->\r\n        </div>\r\n        </div>\r\n        </div>\r\n        </div>\r\n    </main>\r\n\r\n\r\n\r\n         <!--   Core JS Files   -->\r\n    <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"../../assets/js/menu.js\"></script>\r\n    <script src=\"../../assets/js/page.js\"></script>\r\n    <!-- Inclua o arquivo JavaScript do jQuery -->\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script>\r\n      \$(document).ready(function() {\r\n        // Adicione a funcionalidade de busca na tabela\r\n        \$('#searchInput').on('keyup', function() {\r\n          var value = \$(this).val().toLowerCase();\r\n          \$('#usuario tbody tr').filter(function() {\r\n            var rowText = \$(this).text().toLowerCase();\r\n            var searchTerms = value.split(' ');\r\n            var found = true;\r\n            for (var i = 0; i < searchTerms.length; i++) {\r\n              if (rowText.indexOf(searchTerms[i]) === -1) {\r\n                found = false;\r\n                break;\r\n              }\r\n            }\r\n            \$(this).toggle(found);\r\n          });\r\n        });\r\n      });\r\n    </script>\r\n</body>\r\n\r\n</html>";

?>