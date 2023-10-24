<?php

echo "        ";
$logo = getlogo($conexao);
echo "    \r\n       <div class=\"sidebar-container\">\r\n            <input type=\"checkbox\" id=\"check\">\r\n            <label class=\"button bars check-button\" for=\"check\"><i class=\"fas fa-bars\"></i></label>\r\n\r\n            <div class=\"side_bar\">\r\n\r\n            <div class=\"title\">\r\n                <label class=\"button cancel\" for=\"check\"><i class=\"fas fa-times\"></i></label>\r\n                <a class=\"navbar-brand m-0\">\r\n                    <img src=\"";
echo $logo;
echo "\" alt=\"Logo\" width=\"auto\" height=\"110\">\r\n                </a>\r\n            </div>\r\n\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 2px solid #191c24;\">\r\n                <ul class=\"nav\">\r\n                    <li class=\"nav-item\">\r\n                        <a class=\"nav-link\" href=\"../../nivel.php\">\r\n                            <span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-home menu-icon\"></i>\r\n                                Página inicial</span>\r\n                        </a>\r\n                    </li>\r\n                    <li class=\"nav-item\">\r\n                        <a class=\"nav-link\" data-bs-toggle=\"collapse\" href=\"#ui-basic\" aria-expanded=\"false\" aria-controls=\"ui-basic\">\r\n                            <span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi mdi-account menu-icon\"></i>\r\n                                Usuarios</span>\r\n                            <i class=\"menu-arrow\"></i>\r\n                        </a>\r\n                        <div class=\"collapse\" id=\"ui-basic\">\r\n                            <ul class=\"nav flex-column sub-menu\">\r\n                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/usuarios/criarusuario.php\" style=\"font-size: 14px;\">Criar Usuarios</a>\r\n                                </li>\r\n                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/usuarios/criarteste.php\" style=\"font-size: 14px;\">Criar Teste</a>\r\n                                </li>\r\n                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/usuarios/listarusuarios.php\" style=\"font-size: 14px;\">Listar Usuarios</a>\r\n                                </li>\r\n                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/usuarios/listarusuarioexpirado.php\" style=\"font-size: 14px;\">Usuarios Expirados</a>\r\n                                </li>\r\n                                ";
if ($_SESSION["nivel"] == 3) {
    echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/usuarios/listarusuarioglobal.php\" style=\"font-size: 14px;\">Usuarios Global</a></li>";
}
echo "                            </ul>\r\n                        </div>\r\n                    </li>\r\n                                 ";
if ($_SESSION["iduser"] == 1) {
    echo "                                <li class=\"nav-item\">\r\n                                    <a class=\"nav-link\" data-bs-toggle=\"collapse\" href=\"#ui-basic2\" aria-expanded=\"false\" aria-controls=\"ui-basic2\">\r\n                                        <span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-account-multiple menu-icon\"></i>\r\n                                            Revendedores</span>\r\n                                        <i class=\"menu-arrow\"></i>\r\n                                    </a>\r\n                                 <div class=\"collapse\" id=\"ui-basic2\">\r\n                                        <ul class=\"nav flex-column sub-menu\">\r\n                                            <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/revendas/criarrevendas.php\" style=\"font-size: 14px;\">Criar Revenda</a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/revendas/listarrev.php\" style=\"font-size: 14px;\">Listar Revenda</a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a class=\"nav-link\" href=\"../../views/revendas/listarrevexpirado.php\" style=\"font-size: 14px;\">Atribuições Vencidas</a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a class=\"nav-link\" href=\"../../views/revendas/revendasuspensa.php\" style=\"font-size: 14px;\">Atribuições Suspensa</a>\r\n                                            </li>\r\n                                        </ul>\r\n                                    </div>\r\n                                </li>\r\n                                ";
} else {
    $sql_verifica_permissao = "SELECT COUNT(*) FROM atribuidos WHERE subrev = 1 AND userid = " . $_SESSION["iduser"];
    $resultado_verifica_permissao = mysqli_query($conexao, $sql_verifica_permissao);
    $linha_verifica_permissao = mysqli_fetch_row($resultado_verifica_permissao);
    if (0 < $linha_verifica_permissao[0]) {
        echo "                                <li class=\"nav-item\">\r\n                                    <a class=\"nav-link\" data-bs-toggle=\"collapse\" href=\"#ui-basic2\" aria-expanded=\"false\" aria-controls=\"ui-basic2\">\r\n                                        <span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-account-multiple menu-icon\"></i>\r\n                                            Revendedores</span>\r\n                                        <i class=\"menu-arrow\"></i>\r\n                                    </a>\r\n                                 <div class=\"collapse\" id=\"ui-basic2\">\r\n                                        <ul class=\"nav flex-column sub-menu\">\r\n                                            <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/revendas/criarrevendas.php\" style=\"font-size: 14px;\">Criar Revenda</a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/revendas/listarrev.php\" style=\"font-size: 14px;\">Listar Revenda</a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a class=\"nav-link\" href=\"../../views/revendas/listarrevexpirado.php\" style=\"font-size: 14px;\">Atribuições Vencidas</a>\r\n                                            </li>\r\n                                            <li class=\"nav-item\">\r\n                                                <a class=\"nav-link\" href=\"../../views/revendas/revendasuspensa.php\" style=\"font-size: 14px;\">Atribuições Suspensa</a>\r\n                                            </li>\r\n                                        </ul>\r\n                                    </div>\r\n                                </li>\r\n                                ";
    }
}
echo "\r\n\r\n                                ";
if ($_SESSION["nivel"] == 3) {
    echo "<li class=\"nav-item\"><a class=\"nav-link\" data-bs-toggle=\"collapse\" href=\"#ui-basic5\" aria-expanded=\"false\" aria-controls=\"ui-basic5\">\r\n                                        <span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-account-multiple menu-icon\"></i>\r\n                                            Online</span>\r\n                                        <i class=\"menu-arrow\"></i>\r\n                                    </a><div class=\"collapse\" id=\"ui-basic5\"><ul class=\"nav flex-column sub-menu\"><li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/online/listaronline.php\" style=\"font-size: 14px;\">Online</a></li><li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/online/listaronlineglobal.php\" style=\"font-size: 14px;\">Global</a></li><li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/online/configuraronline.php\" style=\"font-size: 14px;\">Configurar</a></li></ul></div></li>";
}
echo "                     ";
if ($_SESSION["nivel"] == 2) {
    echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"../../views/online/listaronline.php\"><span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-account-multiple menu-icon\"></i>Online</span></a></li>";
}
echo "\r\n                    <li class=\"nav-item\">\r\n                        <a class=\"nav-link\" data-bs-toggle=\"collapse\" href=\"#general-pages\" aria-expanded=\"false\" aria-controls=\"general-pages\">\r\n                            <span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi mdi-bank menu-icon\"></i>\r\n                                Pagamentos</span>\r\n                            <i class=\"menu-arrow\"></i>\r\n                        </a>\r\n                        <div class=\"collapse\" id=\"general-pages\">\r\n                            <ul class=\"nav flex-column sub-menu\">\r\n                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/configuracao_pagamento/config.php\" style=\"font-size: 14px;\">Configurar pagamento</a>\r\n                                </li>\r\n                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/configuracao_pagamento/listarpagamento.php\" style=\"font-size: 14px;\">Listar pagamentos</a>\r\n                                </li>\r\n                                ";
if ($_SESSION["nivel"] == 3) {
    echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/configuracao_pagamento/listarpagamentoglobal.php\" style=\"font-size: 14px;\">Todos Pagamento</a></li>";
}
echo "                                <li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/cupons/listar_cupons.php\" style=\"font-size: 14px;\">Cupom</a>\r\n                                </li>\r\n\r\n                                ";
if ($_SESSION["nivel"] == 2) {
    echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/pagamento.rev/renov.php\" style=\"font-size: 14px;\">Renovar Painel</a></li>";
}
echo "                                ";
if ($_SESSION["nivel"] == 2) {
    echo "<li class=\"nav-item\"> <a class=\"nav-link\" href=\"../../views/pagamento.rev/add.php\" style=\"font-size: 14px;\">Comprar Creditos</a></li>";
}
echo "                            </ul>\r\n                        </div>\r\n                    </li>\r\n                    ";
if ($_SESSION["nivel"] == 3) {
    echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"../../views/servidores/listar.servidor.php\"><span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-server-network menu-icon\"></i> Servidores</span></a></li>";
}
echo "                    <li class=\"nav-item\">\r\n                        <a class=\"nav-link\" href=\"../../home/configuracao.php\">\r\n                            <span class=\"menu-title\" style=\"font-size: 18px; width: 150px;\"><i class=\"mdi mdi-settings meu-icone\"></i>\r\n                                Configuração</span>\r\n                        </a>\r\n                    </li>\r\n                   ";
if ($_SESSION["nivel"] == 3 || $_SESSION["nivel"] == 2) {
    echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"../../home/conta.php\"><i class=\"mdi mdi-settings menu-icon\"></i><span class=\"menu-title\" style=\"font-size: 18px;\">Conta</span></a></li>";
}
echo "                    ";
if ($_SESSION["nivel"] == 3) {
    echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"../../home/checkuser.php\"><span class=\"menu-title\" style=\"font-size: 18px;\"><i class=\"mdi mdi-account-check menu-icon\"></i>CheckUser</span></a></li>";
}
echo "                    \r\n\r\n\r\n                    <li class=\"nav-item\">\r\n                        <a class=\"nav-link\" href=\"../../logout.php\">\r\n                            <span class=\"menu-title\" style=\"font-size: 18px; width: 150px;\"><i class=\"mdi mdi-logout meu-icone\"></i>\r\n                                Sair</span>\r\n                        </a>\r\n                    </li>\r\n\r\n                </ul>\r\n                <br>\r\n                <br>\r\n                <br>\r\n\r\n            </div>\r\n        </div>\r\n       \r\n        \r\n                  <!-- Navbar -->\r\n        <div class=\"container-fluid py-5\">\r\n            <a class=\"navbar-brand\">\r\n                <img id=\"logo-img\" class=\"logo-img\" src=\"";
echo $logo;
echo "\" alt=\"Logo\">\r\n            </a><br>\r\n\r\n           <h5 class=\"modal-tit\" style=\"font-size: 17px;\">\r\n                Olá,\r\n                ";
echo $saudacao;
echo "                ";
echo $_SESSION["login"];
echo "            </h5>\r\n";
function getLogo($conexao)
{
    $stmt = $conexao->prepare("SELECT logo FROM config WHERE byid = ?");
    $stmt->bind_param("i", $byid);
    $byid = 1;
    $stmt->execute();
    $result = $stmt->get_result();
    if (0 < $result->num_rows) {
        $row = $result->fetch_assoc();
        return $row["logo"];
    }
    return NULL;
}

?>