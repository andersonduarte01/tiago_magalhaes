<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "../config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if (!$conexao) {
    exit("Conexão falhou: " . mysqli_connect_error());
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
$sql = "SELECT * FROM atribuidos WHERE userid = '" . $_SESSION["iduser"] . "'";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION["validade"] = $row["expira"];
        $_SESSION["limite"] = $row["limite"];
        $_SESSION["tipo"] = $row["tipo"];
    }
}
$data_validade = $_SESSION["validade"];
$data_validade_formatada = date("d/m/Y", strtotime($data_validade));
$limite = $_SESSION["limite"];
$_SESSION["limite"] = $limite;
$_SESSION["valor"] = $valor;
if ($_SESSION["tipo"] == "Validade") {
    $data_atual = time();
    $data_validade_timestamp = strtotime($data_validade);
    $dias_restantes = floor(($data_validade_timestamp - $data_atual) / 86400);
} else {
    $dias_restantes = "Nunca";
}
$sql = "SELECT COUNT(*) as total FROM ssh_accounts WHERE byid = " . $_SESSION["iduser"];
$result = $conexao->query($sql);
$row = $result->fetch_assoc();
$total_users = $row["total"];
$sql = "SELECT COUNT(*) as total1 FROM accounts WHERE byid = " . $_SESSION["iduser"];
$result = $conexao->query($sql);
$row = $result->fetch_assoc();
$total1_users = $row["total1"];
$query = "SELECT * FROM ssh_accounts";
$stmt = $conexao->prepare($query);
$result = $stmt->execute();
if (!$result) {
    exit("Erro ao buscar usuários: " . $stmt->error);
}
$result = $stmt->get_result();
$num_users = $result->num_rows;
$_SESSION["num_users"] = $num_users;
$query = "SELECT * FROM accounts WHERE id <> 1";
$stmt = $conexao->prepare($query);
$result = $stmt->execute();
if (!$result) {
    exit("Erro ao buscar revendas: " . $stmt->error);
}
$result = $stmt->get_result();
$num_rev = $result->num_rows;
$_SESSION["num_rev"] = $num_rev;
$id_usuario = $_SESSION["iduser"];
$sql = "SELECT SUM(valor) AS total_aprovado FROM pagamentos WHERE status = 'Aprovado' AND byid = '" . $id_usuario . "'";
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    $row = $result->fetch_assoc();
    $total_aprovado = $row["total_aprovado"];
}
$currentMonth = date("m");
$sql = "SELECT * FROM pagamentos WHERE byid = " . $_SESSION["iduser"] . " AND MONTH(data_pagamento) = " . $currentMonth . " AND status = 'Aprovado'";
$total = 0;
$result = $conexao->query($sql);
if (0 < $result->num_rows) {
    while ($row = $result->fetch_assoc()) {
        $total += $row["valor"];
    }
}
$iduser = $_SESSION["iduser"];
if (isset($_POST["gerar"])) {
    $sql = "SELECT id, mainid FROM accounts WHERE id = " . $iduser;
    $result = mysqli_query($conexao, $sql);
    if (0 < mysqli_num_rows($result)) {
        $row = mysqli_fetch_assoc($result);
        $id = $row["id"];
        $mainid = $row["mainid"];
        $sql_link_existente = "SELECT link_id FROM links WHERE byid = " . $iduser . " LIMIT 1";
        $result_link_existente = mysqli_query($conexao, $sql_link_existente);
        if (0 < mysqli_num_rows($result_link_existente)) {
            $_SESSION["link2"] = "<div>Já existe um link Gerado.. <br> Exclua o link e tente novamente.</div>";
        } else {
            $domain = $_SERVER["HTTP_HOST"];
            $new_link_id = generate_random_code();
            $link = "https://" . $domain . "/home/criar_teste.php?id=" . $new_link_id . "&byid=" . $id . "&mainid=" . $mainid;
            $sql_links = "INSERT INTO links (link, short_link, byid, mainid, link_id, link_gerado) VALUES ('" . $link . "', '" . $link . "', '" . $id . "', '" . $mainid . "', '" . $new_link_id . "', 1)";
            if (mysqli_query($conexao, $sql_links) === true) {
                $_SESSION["link3"] = "<div>Link gerado com sucesso!</div>";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            $_SESSION["link4"] = "<div>Erro ao salvar o link: " . mysqli_error($conexao) . "</div>";
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    } else {
        echo "Não foi possível recuperar o id e o mainid da tabela accounts";
        mysqli_close($conexao);
        exit;
    }
}
$sql_links = "SELECT * FROM links WHERE byid = " . $iduser . " ORDER BY created_at DESC";
$result_links2 = mysqli_query($conexao, $sql_links);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["liberar"])) {
    $iduser = $_SESSION["iduser"];
    $sql = "DELETE FROM tabela_bloqueio WHERE byid = " . $iduser;
    $resultado = mysqli_query($conexao, $sql);
    if ($resultado) {
        $_SESSION["link1"] = "<div>Agora todos os clientes que foram bloqueados pode fazer o teste novamente.</div>";
    } else {
        $msg = "Erro ao excluir registros: " . mysqli_error($conexao);
    }
}
$sql = "SELECT COUNT(*) AS num_registros FROM ssh_accounts WHERE byid = '" . $_SESSION["iduser"] . "'";
$result = mysqli_query($conexao, $sql);
$row = mysqli_fetch_assoc($result);
$num_registros = $row["num_registros"];
$sql_online = "SELECT * FROM api_online";
$resultado_online = mysqli_query($conexao, $sql_online);
$totalRegistros = mysqli_num_rows($resultado_online);
if ($_SESSION["iduser"] == 1) {
    $totalRegistros = $totalRegistros;
} else {
    $registrosFiltrados = 0;
    while ($row = mysqli_fetch_assoc($resultado_online)) {
        if ($row["byid"] == $_SESSION["iduser"]) {
            $registrosFiltrados++;
        }
    }
    $totalRegistros = $registrosFiltrados;
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
include "../header.php";
echo "\r\n<!-- Modal -->\r\n\r\n\r\n\r\n<body class=\"g-sidenav-show\">\r\n    \r\n    \r\n <main class=\"main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg \">\r\n     \r\n       ";
include "../menu.php";
echo "       \r\n            <div class=\"row\">\r\n\r\n                <!--INICIO-->\r\n\r\n                <div class=\"col-xl-12 col-sm-6 mb-xl-0 mb-4\"><br></div>\r\n                <b>Faturamento</b>\r\n\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                        <a style=\"cursor: pointer;\" href=\"../nivel.php\">\r\n                            <div class=\"card-body p-3\">\r\n                                <div class=\"row\">\r\n                                    <div class=\"col-10\">\r\n                                        <div class=\"numbers\">\r\n                                            <p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Aprovado</p>\r\n                                            <h5 class=\"font-weight-bolder mb-0\">\r\n                                                ";
echo "R\$: " . number_format($total_aprovado, 2, ",", ".");
echo "                                            </h5>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                   <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/aprovado.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n\r\n                                </div>\r\n                            </div>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                        <a style=\"cursor: pointer;\" href=\"../nivel.php\">\r\n                            <div class=\"card-body p-3\">\r\n                                <div class=\"row\">\r\n                                    <div class=\"col-10\">\r\n                                        <div class=\"numbers\">\r\n                                            <p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Mês atual</p>\r\n                                            <h5 class=\"font-weight-bolder mb-0\">\r\n                                                ";
echo "R\$: " . number_format($total, 2, ",", ".");
echo "                                            </h5>\r\n                                        </div>\r\n                                    </div>\r\n\r\n                                      <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/mes.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n\r\n                                </div>\r\n                            </div>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n\r\n\r\n                <!--INICIO-->\r\n                <div class=\"col-xl-12 col-sm-6 mb-xl-0 mb-4\"><br></div>\r\n\r\n                <b>Informações</b>\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n\r\n\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                     <a style=\"cursor: pointer;\" href=\"";
echo $_SESSION["nivel"] == 3 ? "../views/online/listaronlineglobal.php" : "../views/online/listaronline.php";
echo "\">\r\n                        <div class=\"card-body p-3\">\r\n                            <div class=\"row\">\r\n                                <div class=\"col-10\">\r\n                                    <div class=\"numbers\">\r\n                                        <p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Online</p>\r\n                                        <h5 class=\"font-weight-bolder mb-0\">\r\n                                            ";
echo $totalRegistros;
echo "                                        </h5>\r\n                                    </div>\r\n                                </div>\r\n                                <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/online.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                        <div class=\"card-body p-3\">\r\n                            <div class=\"row\">\r\n                                <div class=\"col-10\">\r\n                                  <div class=\"numbers\">\r\n                                        <h5 class=\"text-sm mb-0 text-capitalize font-weight-bold\">Versão do sistema</h5>\r\n                                        <p class=\"font-weight-bolder mb-0\">\r\n                                            1.1.2\r\n                                            ";
if ($_SESSION["nivel"] == 3) {
    echo "                                             <a onclick=\"exibirMensagem()\" href=\"../update.php\" class=\"btn btn-primary btn-sm\" style=\"background-color: #5e17eb; font-size: 10px; padding: 4px 9px;\">Atualizar sistema</a>\r\n\r\n                                                ";
}
echo "                                        </p>\r\n                                    </div>\r\n\r\n                                </div>\r\n                                <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                    <img src=\"../assets/img/versao.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n                                <div class=\"col-2\">\r\n                                   \r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n\r\n\r\n                <hr class=\"horizontal dark mt-3\" style=\"margin: 0px; border-top: 10%;  #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0px; border-top: 10%;  #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0px; border-top: 10%;  #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0px; border-top: 10%;  #191c24;\">\r\n\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                        <a style=\"cursor: pointer;\" href=\"../views/usuarios/listarusuarios.php\">\r\n                        <div class=\"card-body p-3\">\r\n                            <div class=\"row\">\r\n                                <div class=\"col-10\">\r\n                                    <div class=\"numbers\">\r\n                                        <h3 class=\"text-sm mb-0 text-capitalize font-weight-bold\">Usuarios</h3>\r\n                                        <h5 class=\"font-weight-bolder mb-0\">\r\n                                            ";
echo $total_users;
echo "                                        </h5>\r\n                                    </div>\r\n                                </div>\r\n                                <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/usuario.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n                        </a>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\">\r\n                    <div class=\"card\">\r\n                       <a style=\"cursor: pointer;\" href=\"../views/revendas/listarrev.php\">\r\n                        <div class=\"card-body p-3\">\r\n                            <div class=\"row\">\r\n                                <div class=\"col-10\">\r\n                                    <div class=\"numbers\">\r\n                                        <h6 class=\"text-sm mb-0 text-capitalize font-weight-bold\">Revendedores</h6>\r\n                                        <h5 class=\"font-weight-bolder mb-0\">\r\n                                            ";
echo $total1_users;
echo "                                        </h5>\r\n                                    </div>\r\n                                </div>\r\n\r\n                               <div class=\"bg-gradient-primary shadow text-center border-radius-md\">\r\n                                  <img src=\"../assets/img/revenda.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                </div>\r\n                                </a>\r\n                            </div>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n                <!--FIM-->\r\n\r\n                      ";
$iduser_get = $_SESSION["iduser"];
$userLevel = $_SESSION["nivel"];
if ($userLevel == 2) {
    echo "<div class=\"col-xl-12 col-sm-6 mb-xl-0 mb-4\"><br></div><b>Servidores</b><hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\"><hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\"> <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\"> <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">";
    $sql = "SELECT id, categoriaid FROM atribuidos WHERE userid = '" . $iduser_get . "'";
    $resultado = mysqli_query($conexao, $sql);
    if (0 < mysqli_num_rows($resultado)) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $atribuidosId = $row["id"];
            $categoriaid = $row["categoriaid"];
            $sqlAtribuidos = "SELECT atribuidos.id, atribuidos.valor, atribuidos.limite, atribuidos.limitetest, atribuidos.tipo, atribuidos.expira, categorias.nome, accounts.login, atribuidos.userid, accounts.id \r\n                                                        FROM atribuidos \r\n                                                        INNER JOIN categorias ON atribuidos.categoriaid = categorias.subid \r\n                                                        INNER JOIN accounts ON atribuidos.userid = accounts.id \r\n                                                        WHERE atribuidos.id = '" . $atribuidosId . "'";
            $resultadoAtribuidos = mysqli_query($conexao, $sqlAtribuidos);
            while ($atribuidos = mysqli_fetch_assoc($resultadoAtribuidos)) {
                $userid = $atribuidos["userid"];
                $accountid = $atribuidos["id"];
                $countQuery = "SELECT SUM(limite) AS totalLimite FROM ssh_accounts WHERE byid = '" . $iduser_get . "' AND categoriaid = '" . $categoriaid . "'";
                $countResult = mysqli_query($conexao, $countQuery);
                $countRow = mysqli_fetch_assoc($countResult);
                $userCountSshAccounts = $countRow["totalLimite"];
                $countQueryAtribuidos = "SELECT SUM(limite) AS totalLimite FROM atribuidos WHERE byid = '" . $iduser_get . "' AND categoriaid = '" . $categoriaid . "'";
                $countResultAtribuidos = mysqli_query($conexao, $countQueryAtribuidos);
                $countRowAtribuidos = mysqli_fetch_assoc($countResultAtribuidos);
                $userCountAtribuidos = $countRowAtribuidos["totalLimite"];
                $userCountSshAccounts = $userCountSshAccounts === NULL ? 0 : $userCountSshAccounts;
                $userCountAtribuidos = $userCountAtribuidos === NULL ? 0 : $userCountAtribuidos;
                $userCount = $userCountSshAccounts + $userCountAtribuidos;
                echo "                                        <div class=\"col-12\">\r\n                                            <div class=\"card mb-2 cor-escura\">\r\n                                                <div class=\"card-body d-flex align-items-center p-2\">\r\n                        \r\n                                                    <div class=\"informacoes\">\r\n                                                        ";
                if ($atribuidos["tipo"] == "Credito") {
                    echo "<div class=\"d-flex\">";
                    echo "<h6 class=\"card-title\">" . $atribuidos["nome"] . "</h6>";
                    echo "</div><div class=\"d-flex\">";
                    echo "<span class=\"card-text\">Creditos disponíveis: " . $atribuidos["limite"] . "</span>";
                    echo "</div><div class=\"d-flex\">";
                    echo "<span class=\"card-text\">Testes disponíveis: " . $atribuidos["limitetest"] . "</span>";
                    echo "</div>";
                } else {
                    echo "<div class=\"d-flex\">";
                    echo "<h6 class=\"card-title\">" . $atribuidos["nome"] . "</h6>";
                    echo "</div><div class=\"d-flex\">";
                    echo "<span class=\"card-text\">Limite usado: " . $userCount . " de " . $atribuidos["limite"] . "</span>";
                    echo "</div><div class=\"d-flex\">";
                    if ($atribuidos["tipo"] == "Validade") {
                        $hoje = new DateTime();
                        $expira = new DateTime($atribuidos["expira"]);
                        $diferenca = $hoje->diff($expira);
                        if ($expira < $hoje) {
                            echo "<span class=\"card-text\">Expirado</span>";
                        } else {
                            if ($diferenca->days == 0) {
                                echo "<span class=\"card-text\">Expira hoje</span>";
                            } else {
                                $format = "%a dias, %h horas, %i minutos";
                                echo "<span class=\"card-text\">Expira em: " . $diferenca->format($format) . "</span>";
                            }
                        }
                    }
                    echo "</div>";
                }
                echo "                                                        <img src=\"../assets/img/servidor.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\">\r\n                                                    </div>\r\n                                                </div>\r\n                                            </div>\r\n                                        </div>\r\n                        ";
            }
        }
    } else {
        echo "<p>Nenhuma revenda cadastrada.</p>";
    }
}
echo "\r\n\r\n                <!--INICIO-->\r\n\r\n\r\n                <div class=\"col-xl-12 col-sm-6 mb-xl-0 mb-4\"><br></div>\r\n\r\n                ";
if ($_SESSION["nivel"] == 3) {
    echo "<b>Informações Gerais</b>                \r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <hr class=\"horizontal dark mt-0\" style=\"margin: 0; border-top: 10px solid #191c24;\">\r\n                <div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\"><div class=\"card\"><a style=\"cursor: pointer;\" href=\"../views/usuarios/listarusuarioglobal.php\"><div class=\"card-body p-3\"><div class=\"row\"><div class=\"col-10\"><div class=\"numbers\"><p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Usuarios Globais</p><h5 class=\"font-weight-bolder mb-0\">";
    echo "<span style=\"color: #850B0BFF;\">" . $num_users . "</span>";
    echo "</h5></div></div><div class=\"bg-gradient-primary shadow text-center border-radius-md\"><img src=\"../assets/img/usuario.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\"></div></div></div></a></div></div><div class=\"col-xl-6 col-sm-6 mb-xl-0 mb-4\"><div class=\"card\"><a style=\"cursor: pointer;\" href=\"#\"><div class=\"card-body p-3\"><div class=\"row\"><div class=\"col-10\"><div class=\"numbers\"><p class=\"text-sm mb-0 text-capitalize font-weight-bold\">Revendedores Globais</p><h5 class=\"font-weight-bolder mb-0\">";
    echo "<span style=\"color: #850B0BFF;\">" . $num_rev . "</span>";
    echo "</h5></div></div><div class=\"bg-gradient-primary shadow text-center border-radius-md\"><img src=\"../assets/img/revenda.png\" style=\"height: 55px;position: absolute;right: 7px;top: 13px;\"></div></div></div></a></div></div>";
}
echo "            </div>\r\n            <br>\r\n\r\n            <!--INICIO-->\r\n\r\n            <div class=\"row\">\r\n\r\n                <div class=\"col-12 col-md-6 mb-2\">\r\n                    <form method=\"post\" class=\"text-center\">\r\n                        <button type=\"submit\" class=\"btn btn-primary w-100\" style=\"background-color: #5e17eb;\" name=\"gerar\">Gerar Novo Link</button>\r\n                    </form>\r\n                </div>\r\n                <div class=\"col-12 col-md-6 mb-3\">\r\n                    <form method=\"post\" class=\"text-center\">\r\n                        <button type=\"submit\" class=\"btn btn-primary w-100\" style=\"background-color: #5e17eb;\" name=\"liberar\">Limpa IPS Bloqueado</button>\r\n                    </form>\r\n                </div>\r\n\r\n\r\n                ";
if (isset($_SESSION["link1"])) {
    echo "<script>Swal.fire({ icon: 'success', title: 'Os ips foram liberados!', html: '" . $_SESSION["link1"] . "' });</script>";
    unset($_SESSION["link1"]);
}
echo "                ";
if (isset($_SESSION["link3"])) {
    echo "<script>Swal.fire({ icon: 'success', html: '" . $_SESSION["link3"] . "' });</script>";
    unset($_SESSION["link3"]);
}
echo "\r\n                ";
if (isset($_SESSION["link2"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["link2"] . "' });</script>";
    unset($_SESSION["link2"]);
}
echo "                ";
if (isset($_SESSION["link4"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["link4"] . "' });</script>";
    unset($_SESSION["link4"]);
}
echo "\r\n\r\n                ";
if (0 < $result_links2->num_rows) {
    echo "<div class=\"table-responsive links-table\"><table class=\"table table-striped table-hover\"><thead class=\"thead-dark\"><tr><th>Link para gerar teste</th><th>Ações</th></tr></thead><tbody>";
    while ($row_links = $result_links2->fetch_assoc()) {
        echo "<tr>";
        echo "<td class=\"link-cell\"><a href=\"" . $row_links["link"] . "\">" . $row_links["link"] . "</a></td>";
        echo "<td><button class=\"btn btn-secondary\" style=\"background-color: #5e17eb;\" data-link=\"" . $row_links["link"] . "\" onclick=\"copiarLink(this)\">Copiar</button></td>";
        echo "<td><button class=\"btn btn-danger\" onclick=\"excluirLink(" . $row_links["id"] . ")\">Excluir</button></td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
}
echo "\r\n            </div>\r\n        </div>\r\n\r\n    </main>\r\n\r\n   <!--   Core JS Files   -->\r\n    <script src=\"../assets/js/core/bootstrap.min.js\"></script>\r\n    <script src=\"../assets/js/soft-ui-dashboard.min.js?v=1.0.6\"></script>\r\n    <script src=\"https://code.jquery.com/jquery-3.6.0.min.js\"></script>\r\n    <script src=\"../assets/js/menu.js\"></script>\r\n    <script src=\"../assets/js/page.js\"></script>\r\n    <script>\r\n    function exibirMensagem() {\r\n      Swal.fire({\r\n        title: 'Carregando...',\r\n        html: '<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin fa-3x\"></i></div>',\r\n        showCancelButton: false,\r\n        showConfirmButton: false,\r\n        allowOutsideClick: false,\r\n        allowEscapeKey: false,\r\n        allowEnterKey: false\r\n      });\r\n    }\r\n  </script>\r\n  \r\n\r\n  \r\n  \r\n</body>\r\n\r\n</html>";
function generate_random_code()
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $code = "";
    for ($i = 0; $i < 3; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

?>