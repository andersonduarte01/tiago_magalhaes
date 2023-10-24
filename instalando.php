<?php

session_start();
    $fp = fopen("config/conexao.php", "w");
    fwrite($fp, "<?php \n");
    fwrite($fp, "?>");
    fclose($fp);

if (isset($_POST["hostdb"]) && isset($_POST["usuariodb"]) && isset($_POST["senhadb"]) && isset($_POST["bancodb"])) {
    $server = $_POST["hostdb"];
    $user = $_POST["usuariodb"];
    $pass = $_POST["senhadb"];
    $db = $_POST["bancodb"];
    $fp = fopen("config/conexao.php", "a");
    fwrite($fp, "\n<?php \n");
    fwrite($fp, "\$server = '" . $server . "';\n");
    fwrite($fp, "\$user = '" . $user . "';\n");
    fwrite($fp, "\$pass = '" . $pass . "';\n");
    fwrite($fp, "\$db = '" . $db . "';\n");
    fwrite($fp, "?>");
    fclose($fp);
    $conn = new mysqli($server, $user, $pass, $db);
    if ($conn->connect_error) {
        exit("Connection failed: " . $conn->connect_error);
    }
    if ($conn->query("DROP TABLE IF EXISTS pagamentos") === true) {
        $sql = "CREATE TABLE pagamentos (\r\n                      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                      login VARCHAR(50) NOT NULL,\r\n                      valor DECIMAL(10, 2) NOT NULL,\r\n                      data_pagamento DATETIME NOT NULL,\r\n                      payment_id VARCHAR(255) NOT NULL,\r\n                      byid INT(11) NOT NULL,\r\n                      iduser INT(11) NOT NULL,\r\n                      status VARCHAR(50) NOT NULL\r\n                    )";
        if ($conn->query($sql) !== true) {
            exit("Erro ao criar tabela pagamentos: " . $conn->error);
        }
        if ($conn->query("DROP TABLE IF EXISTS tabela_bloqueio") === true) {
            $conn->query("DROP TABLE IF EXISTS links");
            $conn->query("DROP TABLE IF EXISTS config");
            $conn->query("DROP TABLE IF EXISTS api_online");
            $conn->query("DROP TABLE IF EXISTS accounts");
            $conn->query("DROP TABLE IF EXISTS cupom");
            $conn->query("DROP TABLE IF EXISTS atribuidos");
            $conn->query("DROP TABLE IF EXISTS categorias");
            $conn->query("DROP TABLE IF EXISTS servidores");
            $conn->query("DROP TABLE IF EXISTS ssh_accounts");
            $conn->query("DROP TABLE IF EXISTS miracle_deviceid");
            $sql_create_tabela_bloqueio = "CREATE TABLE tabela_bloqueio (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                byid INT(11) NOT NULL,\r\n                ip VARCHAR(255) NOT NULL,\r\n                data_bloqueio DATETIME NOT NULL\r\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            if ($conn->query($sql_create_tabela_bloqueio) !== true) {
                exit("Erro ao criar tabela tabela_bloqueio: " . $conn->error);
            }
            $sql_create_links = "CREATE TABLE links (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                link_id VARCHAR(255) NOT NULL,\r\n                link VARCHAR(255) NOT NULL,\r\n                short_link VARCHAR(255) NOT NULL,\r\n                byid INT(11) NOT NULL,\r\n                mainid INT(11) NOT NULL,\r\n                link_gerado TINYINT(1) NOT NULL DEFAULT 0,\r\n                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n            )";
            if ($conn->query($sql_create_links) !== true) {
                exit("Erro ao criar tabela links: " . $conn->error);
            }
            $sql_create_config = "CREATE TABLE config (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                byid INT(11) NOT NULL,\r\n                app VARCHAR(255) NOT NULL,\r\n                title VARCHAR(255) NOT NULL,\r\n                maxtest VARCHAR(255) NOT NULL,\r\n                maxcredit VARCHAR(255) NOT NULL,\r\n                logo VARCHAR(500) NOT NULL\r\n            )";
            if ($conn->query($sql_create_config) !== true) {
                exit("Erro ao criar tabela config: " . $conn->error);
            }
            $sql_insert_config = "INSERT INTO config (byid, app, title, maxtest, maxcredit, logo)\r\n            VALUES (1, 'seu link do aplicativo', 'PAINEL WEB PRO', '120', '30', 'https://i.imgur.com/mqpTJPZ.png')";
            if ($conn->query($sql_insert_config) !== true) {
                exit("Erro ao inserir registro na tabela config: " . $conn->error);
            }
            $sql_create_api_online = "CREATE TABLE api_online (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                byid INT(11) NOT NULL,\r\n                login VARCHAR(250) NOT NULL,\r\n                limite VARCHAR(250) NOT NULL,\r\n                start_time DATETIME NOT NULL,\r\n                online DATETIME NOT NULL\r\n            )";
            if ($conn->query($sql_create_api_online) !== true) {
                exit("Erro ao criar tabela api_online: " . $conn->error);
            }
            $sql_create_accounts = "CREATE TABLE accounts (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                nome VARCHAR(255),\r\n                contato VARCHAR(255),\r\n                login VARCHAR(255) NOT NULL,\r\n                token VARCHAR(255) NOT NULL,\r\n                mb VARCHAR(255) NOT NULL,\r\n                senha VARCHAR(255) NOT NULL,\r\n                byid INT(11) NOT NULL,\r\n                mainid INT(11) NOT NULL,\r\n                accesstoken VARCHAR(255) NOT NULL DEFAULT 0,\r\n                valorrevenda DECIMAL(10, 2) NOT NULL DEFAULT 0,\r\n                valorusuario DECIMAL(10, 2) NOT NULL DEFAULT 0,\r\n                nivel INT(11) NOT NULL DEFAULT 2\r\n            )";
            if ($conn->query($sql_create_accounts) !== true) {
                exit("Erro ao criar tabela accounts: " . $conn->error);
            }
            $sql_insert_admin = "INSERT INTO accounts (nome, contato, login, token, mb, senha, byid, mainid, accesstoken, valorrevenda, valorusuario, nivel)\r\n                VALUES ('Admin', '', 'admin', '', '', '123456', 0, 0, '', 0.0, 0.0, 3)";
            if ($conn->query($sql_insert_admin) !== true) {
                exit("Erro ao inserir registro inicial: " . $conn->error);
            }
            $sql_create_cupom = "CREATE TABLE cupom (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                codigo VARCHAR(255) NOT NULL,\r\n                tipo VARCHAR(255) NOT NULL,\r\n                valor DECIMAL(10, 2) NOT NULL,\r\n                data_validade DATETIME NOT NULL,\r\n                usos_maximos INT(11) NOT NULL,\r\n                usos_restantes INT(11) NOT NULL,\r\n                byid INT(11) NOT NULL\r\n            )";
            if ($conn->query($sql_create_cupom) !== true) {
                exit("Erro ao criar tabela cupom: " . $conn->error);
            }
            $sql_create_atribuidos = "CREATE TABLE atribuidos (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                valor VARCHAR(255),\r\n                categoriaid INT(11) NOT NULL,\r\n                userid INT(11) NOT NULL,\r\n                byid INT(11) NOT NULL,\r\n                limite INT(11) NOT NULL,\r\n                limitetest INT(11),\r\n                tipo VARCHAR(255) NOT NULL,\r\n                expira DATETIME,\r\n                subrev VARCHAR(255) NOT NULL,\r\n                suspenso VARCHAR(255) NOT NULL\r\n            )";
            if ($conn->query($sql_create_atribuidos) !== true) {
                exit("Erro ao criar tabela atribuidos: " . $conn->error);
            }
            $sql_create_categorias = "CREATE TABLE categorias (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                subid INT(11),\r\n                nome VARCHAR(255)\r\n            )";
            if ($conn->query($sql_create_categorias) !== true) {
                exit("Erro ao criar tabela categorias: " . $conn->error);
            }
            $sql_create_servidores = "CREATE TABLE servidores (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                subid INT(11) NOT NULL,\r\n                nome VARCHAR(255) NOT NULL,\r\n                porta VARCHAR(255) NOT NULL,\r\n                usuario VARCHAR(255) NOT NULL,\r\n                senha VARCHAR(255) NOT NULL,\r\n                ip VARCHAR(255) NOT NULL,\r\n                servercpu VARCHAR(255) NOT NULL DEFAULT 0,\r\n                serverram VARCHAR(255) NOT NULL DEFAULT 0,\r\n                onlines INT(11) NOT NULL DEFAULT 0,\r\n                lastview DATETIME\r\n            )";
            if ($conn->query($sql_create_servidores) !== true) {
                exit("Erro ao criar tabela servidores: " . $conn->error);
            }
            $sql_create_ssh_accounts = "CREATE TABLE ssh_accounts (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                byid INT(11) NOT NULL,\r\n                categoriaid INT(11) NOT NULL,\r\n                limite INT(11) NOT NULL,\r\n                bycredit INT(11) NOT NULL,\r\n                login VARCHAR(255) NOT NULL,\r\n                senha VARCHAR(255) NOT NULL,\r\n                mainid INT(11) NOT NULL,\r\n                expira DATETIME,\r\n                lastview DATETIME,\r\n                nivel INT(11) NOT NULL DEFAULT 1\r\n            )";
            if ($conn->query($sql_create_ssh_accounts) !== true) {
                exit("Erro ao criar tabela ssh_accounts: " . $conn->error);
            }
            $sql_create_miracle_deviceid = "CREATE TABLE miracle_deviceid (\r\n                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n                userid INT(11) NOT NULL,\r\n                byid INT(11) NOT NULL,\r\n                device VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL\r\n            )";
            if ($conn->query($sql_create_miracle_deviceid) !== true) {
                exit("Erro ao criar tabela miracle_deviceid: " . $conn->error);
            }
        } else {
            exit("Erro ao apagar tabela tabela_bloqueio: " . $conn->error);
        }
    } else {
        exit("Erro ao apagar tabela pagamentos: " . $conn->error);
    }
}
$conn->close();
$_SESSION["loginsucesso"] = "<div>Painel instalado com sucesso!!</div>";
header("Location: index.php");
exit;

?>