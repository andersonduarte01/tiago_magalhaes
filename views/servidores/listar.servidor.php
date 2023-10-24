<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../../config/conexao.php";
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
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
echo "\r\n<!DOCTYPE html>\r\n<html lang=\"en\">\r\n\r\n";
include "../../header.php";
echo "\r\n<!-- Modal -->\r\n<style>\r\n  .btn-separator {\r\n    margin-right: 30px;\r\n    /* Ajuste o valor conforme necessário */\r\n  }\r\n</style>\r\n<style>\r\n  .letra-icon {\r\n    font-size: 28px;\r\n    border-radius: 50%;\r\n    border: 1px solid #d9dbdf;\r\n    background-color: #d9dbdf;\r\n    /* Defina a cor de fundo desejada */\r\n    display: inline-block;\r\n    /* Garante que o background-color abranja todo o espaço do ícone */\r\n    width: 44px;\r\n    /* Define a largura igual à font-size para tornar o ícone circular */\r\n    height: 45px;\r\n    /* Define a altura igual à font-size para tornar o ícone circular */\r\n    text-align: center;\r\n    /* Centraliza o conteúdo do ícone verticalmente */\r\n    line-height: 45px;\r\n    /* Centraliza o conteúdo do ícone horizontalmente */\r\n    color: #1c1c1d;\r\n    /* Define a cor do texto do ícone */\r\n  }\r\n\r\n  .espaco {\r\n    width: 20px;\r\n  }\r\n\r\n  .cor-escura {\r\n    background-color: #dee5ee;\r\n    /* Defina a cor escura desejada */\r\n  }\r\n</style>\r\n <style>\r\n              .overlay {\r\n                position: fixed;\r\n                top: 0;\r\n                left: 0;\r\n                width: 100%;\r\n                height: 100%;\r\n                background-color: rgba(0, 0, 0, 0.5);\r\n                display: none;\r\n                z-index: 9999; /* Valor alto para ficar acima de tudo */\r\n              }\r\n            \r\n              .overlay-content {\r\n                position: absolute;\r\n                top: 50%;\r\n                left: 50%;\r\n                transform: translate(-50%, -50%);\r\n                background-color: #fff;\r\n                padding: 40px;\r\n                border-radius: 5px;\r\n                z-index: 1000000; /* Set a higher z-index value */\r\n              }\r\n            \r\n              .overlay-title {\r\n                margin-top: 0;\r\n              }\r\n            \r\n              .btn-close {\r\n                position: absolute;\r\n                top: 10px;\r\n                right: 10px;\r\n                background-color: transparent;\r\n                border: none;\r\n                font-size: 20px;\r\n                color: #000;\r\n                cursor: pointer;\r\n              }\r\n              .my-swal-container {\r\n                    z-index: 100001 !important;\r\n                  }\r\n\r\n            \r\n              .my-swal-popup {\r\n                z-index: 1100000 !important; /* Set a higher z-index value */\r\n              }\r\n            </style>\r\n\r\n<body class=\"g-sidenav-show  bg-gray-100\">\r\n\r\n  <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n\r\n    ";
include "../../menu.php";
echo "\r\n      <center>\r\n        <div class=\"container-fluid py-5\">\r\n          <div class=\"col-lg-15\">\r\n            <!-- Inicio -->\r\n            <h2 class=\"card-title\">Lista de Servidores</h2><br><br>\r\n\r\n\r\n            ";
if (isset($_SESSION["servdor1"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["servdor1"] . "' });</script>";
    unset($_SESSION["servdor1"]);
}
echo "\r\n            ";
if (isset($_SESSION["servdor2"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["servdor2"] . "' });</script>";
    unset($_SESSION["servdor2"]);
}
echo "            \r\n             <div class=\"d-grid gap-2 d-md-flex justify-content-md-end\">\r\n                <a class=\"btn btn-primary btn-san mx-2\" style=\"background-color: #007bff; border: none;\" href=\"adicionar_servidor.php\" role=\"button\">Novo Servidor</a>\r\n                <a class=\"btn btn-primary btn-san mx-2\" style=\"background-color: #007bff; border: none;\" href=\"listar_categorias.php\" role=\"button\">Categorias</a>\r\n            </div>\r\n            <br>\r\n\r\n            <form class=\"form-inline my-3\" action=\"\" method=\"GET\">\r\n              <div class=\"input-group\">\r\n                <input type=\"text\" id=\"searchInput\" class=\"form-control\" placeholder=\"Buscar por nome\">\r\n                ";
if (isset($_GET["showAll"])) {
    echo "                  <div class=\"input-group-append\">\r\n                    <a href=\"?searchName=";
    echo $searchName;
    echo "&page=1\" class=\"btn btn-secondary\">Deslistar Todos</a>\r\n                  </div>\r\n                ";
} else {
    echo "                  <div class=\"input-group-append\">\r\n                    <a href=\"?searchName=&page=1&showAll\" class=\"btn btn-primary\">Listar Todos</a>\r\n                  </div>\r\n                ";
}
echo "              </div>\r\n            </form>\r\n            <div class=\"col-12 mb-3\">\r\n                <div class=\"card card-sm\">\r\n                    <div class=\"card-body1 p-2\">\r\n\r\n             ";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;
$resultsPerPage = isset($_GET["showAll"]) ? 1000 : 10;
$startFrom = ($page - 1) * $resultsPerPage;
$iduser = $_SESSION["iduser"];
$sql = "SELECT s.*, c.nome as categoria_nome FROM servidores s INNER JOIN categorias c ON s.subid = c.subid LIMIT " . $startFrom . ", " . $resultsPerPage;
$resultado = mysqli_query($conexao, $sql);
$total_servidores = mysqli_num_rows($resultado);
if (0 < $total_servidores) {
    while ($servidor = mysqli_fetch_assoc($resultado)) {
        $nome = $servidor["nome"];
        $categoria_nome = $servidor["categoria_nome"];
        $ip = $servidor["ip"];
        $id = $servidor["id"];
        $porta = $servidor["porta"];
        $usuario = $servidor["usuario"];
        $senha = $servidor["senha"];
        $subid = $servidor["subid"];
        echo "                    <a class=\"btn-show-modal\" data-ip=\"";
        echo $ip;
        echo "\" data-id=\"";
        echo $id;
        echo "\">\r\n                        <div class=\"col-12\">\r\n                            <div class=\"card mb-2 cor-escura\">\r\n                                <div class=\"card-body d-flex align-items-center p-1\">\r\n                                    <div class=\"informacoes col-10\">\r\n                                        <div class=\"d-flex\">\r\n                                            <i class=\"material-icons\">dns</i>\r\n                                            <h6 class=\"card-title\">";
        echo $ip;
        echo "</h6>\r\n                                        </div>\r\n                                        <div class=\"d-flex\">\r\n                                            <i class=\"material-icons\">public</i>\r\n                                            <h6 class=\"card-text\">";
        echo $categoria_nome;
        echo "</h6>\r\n                                        </div>\r\n                                    </div>\r\n                                    <img src=\"https://cdn-icons-png.flaticon.com/512/6797/6797464.png\" style=\"position: absolute; height: 45px; right: 15px; top: 13px;\">\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </a>\r\n                    ";
    }
} else {
    echo "<p>Nenhum servidor cadastrado.</p>";
}
echo "\r\n\r\n\r\n            <div class=\"overlay\" id=\"overlay\">\r\n              <div class=\"overlay-content\">\r\n                <h4 class=\"overlay-title\">Configuração</h4>\r\n                <div class=\"d-flex flex-column\">\r\n                  <a href=\"#\" class=\"btn btn-info btn-thin btn-dm btn-sync mb-2\">Sincronizar</a>\r\n                  <a href=\"#\" class=\"btn btn-info btn-thin btn-dm btn-modules mb-2\">Módulos</a>\r\n                  <a href=\"#\" class=\"btn btn-primary btn-dm btn-edit mb-2\">Editar</a>\r\n                  <a href=\"#\" class=\"btn btn-danger btn-dm btn-delete\" onclick=\"return confirm('Tem certeza que deseja excluir?')\">Excluir</a>\r\n                </div>\r\n                <button class=\"btn-close\" style=\"background-color: #007bff; border: none;\" onclick=\"closeOverlay()\"></button>\r\n              </div>\r\n            </div>\r\n            <!-- Fim -->\r\n          </div>\r\n        </div>\r\n    </div>\r\n          <nav aria-label=\"Paginação\">\r\n              <ul class=\"pagination justify-content-center my-3\">\r\n                  ";
$sqlCount = "SELECT COUNT(*) AS total FROM servidores";
if (!empty($searchName)) {
    $sqlCount .= " AND login LIKE '%" . $searchName . "%'";
}
$resultCount = $conexao->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalResults = $rowCount["total"];
$totalPages = ceil($totalResults / $resultsPerPage);
$maxVisibleButtons = 5;
$firstVisibleButton = max(1, $page - floor($maxVisibleButtons / 2));
$lastVisibleButton = min($totalPages, $firstVisibleButton + $maxVisibleButtons - 1);
$showAll = isset($_GET["showAll"]) ? true : false;
echo "                ";
if (1 < $page) {
    echo "                  <li class=\"page-item mx-4\">\r\n                    <a class=\"page-link\" href=\"?page=";
    echo $page - 1;
    echo "\">\r\n                      Anterior\r\n                    </a>\r\n                  </li>\r\n                ";
}
for ($i = 1; $i <= $totalPages; $i++) {
    echo "                  <li class=\"page-item ";
    echo $i == $page ? "active" : "";
    echo "\">\r\n                    <a class=\"page-link\" href=\"?page=";
    echo $i;
    echo "\">";
    echo $i;
    echo "</a>\r\n                  </li>\r\n                ";
}
if ($page < $totalPages) {
    echo "                  <li class=\"page-item mx-4\">\r\n                    <a class=\"page-link\" href=\"?page=";
    echo $page + 1;
    echo "\">\r\n                      Próxima\r\n                    </a>\r\n                  </li>\r\n                ";
}
echo "              </ul>\r\n            </nav>\r\n    </div>\r\n  </main>\r\n\r\n\r\n\r\n  <!--   Core JS Files   -->\r\n  <script src=\"../../assets/js/core/bootstrap.min.js\"></script>\r\n  <script src=\"../../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n  <script src=\"../../assets/js/menu.js\"></script>\r\n  <script src=\"../../assets/js/page.js\"></script>\r\n  <!-- Inclua o arquivo JavaScript do jQuery -->\r\n  <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n  <script>\r\n    \$(document).ready(function() {\r\n      // Adicione a funcionalidade de busca na tabela\r\n      \$('#searchInput').on('keyup', function() {\r\n        var value = \$(this).val().toLowerCase();\r\n        \$('#usuario tbody tr').filter(function() {\r\n          var rowText = \$(this).text().toLowerCase();\r\n          var searchTerms = value.split(' ');\r\n          var found = true;\r\n          for (var i = 0; i < searchTerms.length; i++) {\r\n            if (rowText.indexOf(searchTerms[i]) === -1) {\r\n              found = false;\r\n              break;\r\n            }\r\n          }\r\n          \$(this).toggle(found);\r\n        });\r\n      });\r\n    });\r\n  </script>\r\n\r\n<script>\r\n  \$(document).ready(function() {\r\n    \$('.btn-show-modal').click(function() {\r\n      var ip = \$(this).data('ip');\r\n      var id = \$(this).data('id');\r\n      // Definir os links dos botões dentro da janela modal\r\n      \$('.btn-sync').attr('href', 'syncronizar.php?ip=' + ip);\r\n      \$('.btn-modules').attr('href', 'modulos.php?ip=' + ip);\r\n      \$('.btn-edit').attr('href', 'editar_servidor.php?id=' + id);\r\n      \$('.btn-delete').attr('href', 'excluir_servidor.php?id=' + id);\r\n\r\n      // Abrir a janela modal\r\n      \$('#myModal').modal('show');\r\n    });\r\n\r\n    // Fechar a janela modal quando o botão de fechar é clicado\r\n    \$('#myModal').on('hidden.bs.modal', function() {\r\n      \$(this).find('.btn-sync').attr('href', '#');\r\n      \$(this).find('.btn-modules').attr('href', '#');\r\n      \$(this).find('.btn-edit').attr('href', '#');\r\n      \$(this).find('.btn-delete').attr('href', '#');\r\n    });\r\n\r\n    // Adicionar evento de clique aos botões\r\n    \$('.btn-sync, .btn-modules, .btn-edit, .btn-delete').click(function() {\r\n      showLoadingMessage();\r\n    });\r\n  });\r\n\r\n  function showLoadingMessage() {\r\n    Swal.fire({\r\n      title: 'Carregando...',\r\n      html: '<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin fa-3x\"></i></div>',\r\n      showCancelButton: false,\r\n      showConfirmButton: false,\r\n      allowOutsideClick: false,\r\n      allowEscapeKey: false,\r\n      allowEnterKey: false,\r\n      customClass: {\r\n        container: 'my-swal-container',\r\n        popup: 'my-swal-popup'\r\n      }\r\n    });\r\n  }\r\n</script>\r\n  <script>\r\n    function openOverlay() {\r\n      document.getElementById(\"overlay\").style.display = \"block\";\r\n    }\r\n\r\n    function closeOverlay() {\r\n      document.getElementById(\"overlay\").style.display = \"none\";\r\n    }\r\n\r\n    var buttons = document.getElementsByClassName(\"btn-show-modal\");\r\n    for (var i = 0; i < buttons.length; i++) {\r\n      buttons[i].addEventListener(\"click\", function() {\r\n        openOverlay();\r\n      });\r\n    }\r\n  </script>\r\n\r\n\r\n\r\n</body>\r\n\r\n</html>";

?>