<?php

$sql = "SELECT logo FROM config WHERE byid = 1";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    $row = $result->fetch_assoc();
    $logo = $row["logo"];
}
echo "       \r\n        <div class=\"sidebar-container\">\r\n            <input type=\"checkbox\" id=\"check\">\r\n            <label class=\"button bars check-button\" for=\"check\"><i class=\"fas fa-bars\"></i></label>\r\n\r\n            <div class=\"side_bar\">\r\n\r\n                <div class=\"title\">\r\n                    <label class=\"button cancel\" for=\"check\"><i class=\"fas fa-times\"></i></label>\r\n                    <a class=\"navbar-brand m-0\">\r\n                        <img src=\"";
echo $logo;
echo "\" alt=\"Logo\" width=\"auto\" height=\"110\">\r\n                    </a>\r\n\r\n                </div>\r\n\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 2px solid #191c24;\">\r\n                                      <ul class=\"nav\">\r\n                            <li class=\"nav-item\">\r\n                                <a class=\"nav-link\" href=\"../nivel.php\">\r\n                                    <span class=\"menu-title\" style=\"font-size: 18px;\"><i\r\n                                            class=\"mdi mdi-home menu-icon\"></i>\r\n                                        Página inicial</span>\r\n                                </a>\r\n                            </li>\r\n                            <li class=\"nav-item\">\r\n                                <a class=\"nav-link\" href=\"pagamentos.php\">\r\n                                    <span class=\"menu-title\" style=\"font-size: 18px;\"><i\r\n                                            class=\"mdi mdi mdi-bank menu-icon\"></i>\r\n                                        Pagamentos</span>\r\n                                </a>\r\n                            </li>\r\n                             ";
if ($_SESSION["nivel"] == 3 || $_SESSION["nivel"] == 1) {
    echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"../../home/contauser.php\"><i class=\"mdi mdi-settings menu-icon\"></i><span class=\"menu-title\" style=\"font-size: 18px;\">Conta</span></a></li>";
}
echo "                        \r\n                            <li class=\"nav-item\">\r\n                                <a class=\"nav-link\" href=\"../logout.php\">\r\n                                    <span class=\"menu-title\" style=\"font-size: 18px;\"><i\r\n                                            class=\"mdi mdi-logout meu-icone\"></i> Sair</span>\r\n                                </a>\r\n                            </li>\r\n                        </ul>\r\n\r\n            </div>\r\n        </div>     \r\n        \r\n                   <!-- Navbar -->\r\n        <div class=\"container-fluid py-5\">\r\n            <a class=\"navbar-brand\">\r\n                <img id=\"logo-img\" class=\"logo-img\" src=\"";
echo $logo;
echo "\" alt=\"Logo\">\r\n            </a><br>\r\n\r\n           <h5 class=\"modal-title\" style=\"font-size: 17px;\">\r\n                Olá,\r\n                ";
echo $saudacao;
echo "                ";
echo $_SESSION["login"];
echo "            </h5>\r\n";

?>