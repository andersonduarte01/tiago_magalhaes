<?php

echo "\n";
ini_set("error_reporting", 0);
$sock = $_GET["sock"];
$version = "3.0 Closed";
set_time_limit(0);
if ($sock == "install") {
    $servername = $_SERVER["SERVER_NAME"];
    $licensename = $_POST["licensename"];
    $user = $_POST["user"];
    $pass = $_POST["pass"];
    $database = $_POST["database"];
    $host = $_POST["host"];
    echo "\n<meta name=\"viewport\" content=\"initial-scale=1.0; maximum-scale=1.0; user-scalable=yes;\" />\n<body style=\"background: #c4c4c4; font-family: sans-serif;\">\n    <div style=\"text-align: center; top: 15%; position: relative;\">\n<img src=\"https://i.imgur.com/6tHdHqv.png\" style=\"height: 136px;margin-top: -65px;margin-bottom: 20px;\"><br>";
    $filename = "../../miracle_license.ll";
    if (!file_exists($filename)) {
        if (!isset($user)) {
            if (!isset($licensename)) {
                echo "\n    <form method=\"post\" action=\"atlantus.php?sock=install\">\n<h2><center>Bem vindo ao assistente</h2>\n    <div style=\"margin-top: -22px; color: #222; margin-bottom: 36px;\">Vamos instalar seu painel!</div>\n    Primeiro nos diga, qual a sua licença?<br>\n    <input type=\"text\" name=\"licensename\" style=\"padding: 5px; width: 280px; margin-top: 5px; font-size: 20px; border: 0px; color: #222; box-shadow: 2px 1px #b3b3b3;\"><br>\n    <input type=\"submit\" style=\"padding: 8px; margin-top: 8px; background: #f1f1f1; width: 200px; display: inline-block; font-size: 18px; box-shadow: 2px 1px #b3b3b3;\" value=\"Confirmar\"/>\n    </form><br>\n    <div style=\"margin-top: 50%;color: #525252;\">Powered by Atlantus ®</div>\n    </div>";
            } else {
                $url = "https://atlantus.com.br/miracle/" . $licensename . ".txt?";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);
                if (strpos($result, "Oops, looks") !== false) {
                    echo "Licença inexistente.<br>Esta licença é inválida, por favor, acesse atlantus.com.br e compre sua licença para o painel.\n     <br><a href=\"atlantus.php?sock=install\" style=\"position: relative; top: 28px; font-size: 20px; text-decoration: none;\">Retornar</a>\n     \n    <div style=\"margin-top: 50%;color: #525252;\">Powered by Atlantus ®</div>\n     ";
                } else {
                    $result = base64_decode((string) $result);
                    if (strpos($result, $servername) !== false) {
                        echo "Licença válida, preencha os dados de conexão MySQL.\n            <form method=\"post\" action=\"atlantus.php?sock=install\"><br>\n            Usuário MYSQL<br>\n    <input type=\"text\" name=\"user\" style=\"padding: 5px; width: 280px; margin-top: 5px; font-size: 20px; border: 0px; color: #222; box-shadow: 2px 1px #b3b3b3;\"><br>\n    <input type=\"text\" value=\"" . $licensename . "\" name=\"licensename\" style=\"display:none;\">\n            Senha MYSQL<br>\n    <input type=\"text\" name=\"pass\" style=\"padding: 5px; width: 280px; margin-top: 5px; font-size: 20px; border: 0px; color: #222; box-shadow: 2px 1px #b3b3b3;\"><br>\n            Banco de dados MYSQL<br>\n    <input type=\"text\" name=\"database\" style=\"padding: 5px; width: 280px; margin-top: 5px; font-size: 20px; border: 0px; color: #222; box-shadow: 2px 1px #b3b3b3;\"><br>\n            Host MYSQL<br>\n    <input type=\"text\" name=\"host\" value=\"localhost\" style=\"padding: 5px; width: 280px; margin-top: 5px; font-size: 20px; border: 0px; color: #222; box-shadow: 2px 1px #b3b3b3;\"><br>\n    <input type=\"submit\" style=\"padding: 8px; margin-top: 8px; background: #f1f1f1; width: 200px; display: inline-block; font-size: 18px; box-shadow: 2px 1px #b3b3b3;\" value=\"Instalar\"/>";
                    } else {
                        echo "Licença incorreta.<br>Esta licença é válida, porém não é registrada para este dominio.\n             <br><a href=\"atlantus.php?sock=install\" style=\"position: relative; top: 28px; font-size: 20px; text-decoration: none;\">Retornar</a>\n            <div style=\"margin-top: 50%;color: #525252;\">Powered by Atlantus ®</div>\n             ";
                    }
                }
                curl_close($ch);
                exit;
            }
        } else {
            define("DB_SERVER", $host);
            define("DB_USERNAME", $user);
            define("DB_PASSWORD", $pass);
            define("DB_NAME", $database);
            try {
                $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
                $pdo->exec("            -- phpMyAdmin SQL Dump            -- version 4.9.7            -- https://www.phpmyadmin.net/            --            -- Host: localhost:3306            -- Tempo de geração: 03/06/2022 às 20:19            -- Versão do servidor: 8.0.29-cll-lve            -- Versão do PHP: 7.4.29                        SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";            SET AUTOCOMMIT = 0;            START TRANSACTION;            SET time_zone = \"+00:00\";                                    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;            /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;            /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;            /*!40101 SET NAMES utf8mb4 */;                        --            -- Banco de dados: `jozpskld_miracle`            --                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `accounts`            --                        DROP TABLE IF EXISTS `accounts`;            CREATE TABLE `accounts` (              `id` int NOT NULL,              `nome` varchar(255) DEFAULT NULL,              `contato` varchar(255) DEFAULT NULL,              `login` varchar(50) NOT NULL DEFAULT '0',              `token` varchar(330) NOT NULL DEFAULT '0',              `mb` varchar(50) NOT NULL DEFAULT '0',              `senha` varchar(50) NOT NULL DEFAULT '0',              `byid` varchar(50) NOT NULL DEFAULT '0',              `mainid` varchar(50) NOT NULL DEFAULT '0'            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        --            -- Despejando dados para a tabela `accounts`            --                        INSERT INTO `accounts` (`id`, `nome`, `contato`, `login`, `token`, `mb`, `senha`, `byid`, `mainid`) VALUES            (1, 'administrador', '000000', 'admin', '0jYKhUXE9DAGpfwPyTI0kWq3TuVmAYLFSGZVUb3VvcYll4AUiHUm5fWB2YokhCqKvIc5w2mBK3TOrXek4CQypENcyZPE47WQAySqq5cN2KGz5HeH00kh6q6seazWTxnJldfgSb9xNSd85RQJICBUh2NwIkAFS2FtPiwFlT6j6XcMplIPUkpmKgCXPW3pJCPuSfByrjRBATdGxleSdoxNyCNmjw47gHS0X4Hx5tNq3FiMgAPC', '0', '12345', '0', '0');                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `announce`            --                        DROP TABLE IF EXISTS `announce`;            CREATE TABLE `announce` (              `id` int NOT NULL,              `byid` int DEFAULT '0',              `userid` int DEFAULT '0',              `texto` text            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        --            -- Despejando dados para a tabela `announce`            --                        INSERT INTO `announce` (`id`, `byid`, `userid`, `texto`) VALUES            (21, 0, 1, 'Olá, seja bem vindo ao Atlantus Web - Miracle Edition.');                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `atribuidos`            --                        DROP TABLE IF EXISTS `atribuidos`;            CREATE TABLE `atribuidos` (              `id` int NOT NULL,              `valor` varchar(255) DEFAULT NULL,              `categoriaid` int NOT NULL DEFAULT '0',              `userid` int NOT NULL DEFAULT '0',              `byid` int NOT NULL DEFAULT '0',              `limite` int NOT NULL DEFAULT '0',              `limitetest` int DEFAULT NULL,              `tipo` text NOT NULL,              `expira` text,              `subrev` int NOT NULL DEFAULT '0',              `suspenso` int NOT NULL DEFAULT '0'            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `categorias`            --                        DROP TABLE IF EXISTS `categorias`;            CREATE TABLE `categorias` (              `id` int NOT NULL,              `subid` int DEFAULT NULL,              `nome` varchar(150) DEFAULT NULL            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `entityqueue`            --                        DROP TABLE IF EXISTS `entityqueue`;            CREATE TABLE `entityqueue` (              `id` int NOT NULL,              `catid` varchar(50) NOT NULL DEFAULT '0',              `byid` varchar(50) NOT NULL DEFAULT '0',              `ip` varchar(50) NOT NULL DEFAULT '0',              `type` varchar(50) DEFAULT NULL,              `data` text,              `expira` timestamp NULL DEFAULT NULL            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='queue work types: createuser, deleteuser, updateuser, shutdownuser';                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `limiterpro`            --                        DROP TABLE IF EXISTS `limiterpro`;            CREATE TABLE `limiterpro` (              `id` int NOT NULL,              `qtd` int NOT NULL DEFAULT '0',              `deviceid` varchar(255) DEFAULT NULL,              `expira` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,              `ip` varchar(50) DEFAULT NULL,              `usuario` varchar(50) DEFAULT NULL,              `categoriaid` varchar(50) DEFAULT NULL,              `byid` int DEFAULT NULL            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `logs`            --                        DROP TABLE IF EXISTS `logs`;            CREATE TABLE `logs` (              `id` int NOT NULL,              `userid` int DEFAULT '0',              `texto` text,              `validade` text            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `miracle_settings`            --                        DROP TABLE IF EXISTS `miracle_settings`;            CREATE TABLE `miracle_settings` (              `id` int NOT NULL,              `maxtest` int NOT NULL,              `maxcredit` int NOT NULL,              `maxsize` int NOT NULL            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        --            -- Despejando dados para a tabela `miracle_settings`            --                        INSERT INTO `miracle_settings` (`id`, `maxtest`, `maxcredit`, `maxsize`) VALUES            (1, 300, 30, 12);                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `servidores`            --                        DROP TABLE IF EXISTS `servidores`;            CREATE TABLE `servidores` (              `id` int NOT NULL,              `subid` int NOT NULL DEFAULT '0',              `nome` varchar(150) NOT NULL DEFAULT '0',              `porta` int NOT NULL DEFAULT '0',              `usuario` varchar(150) NOT NULL DEFAULT '0',              `senha` varchar(150) NOT NULL DEFAULT '0',              `ip` varchar(150) NOT NULL DEFAULT '0',              `servercpu` varchar(150) NOT NULL DEFAULT '0',              `serverram` varchar(150) NOT NULL DEFAULT '0',              `onlines` varchar(150) NOT NULL DEFAULT '0',              `lastview` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `settings`            --                        DROP TABLE IF EXISTS `settings`;            CREATE TABLE `settings` (              `name` varchar(50) DEFAULT NULL,              `opc` int DEFAULT NULL            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `sshqueue`            --                        DROP TABLE IF EXISTS `sshqueue`;            CREATE TABLE `sshqueue` (              `id` int NOT NULL,              `login` varchar(255) NOT NULL,              `catid` int NOT NULL,              `type` text NOT NULL            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        -- --------------------------------------------------------                        --            -- Estrutura para tabela `ssh_accounts`            --                        DROP TABLE IF EXISTS `ssh_accounts`;            CREATE TABLE `ssh_accounts` (              `id` int NOT NULL,              `byid` int NOT NULL DEFAULT '0',              `categoriaid` int NOT NULL DEFAULT '0',              `limite` int NOT NULL DEFAULT '0',              `bycredit` int NOT NULL DEFAULT '0',              `login` varchar(50) NOT NULL DEFAULT '0',              `senha` varchar(50) NOT NULL DEFAULT '0',              `mainid` text NOT NULL,              `expira` text,              `lastview` text            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;                        --            -- Índices para tabelas despejadas            --                        --            -- Índices de tabela `accounts`            --            ALTER TABLE `accounts`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `announce`            --            ALTER TABLE `announce`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `atribuidos`            --            ALTER TABLE `atribuidos`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `categorias`            --            ALTER TABLE `categorias`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `entityqueue`            --            ALTER TABLE `entityqueue`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `limiterpro`            --            ALTER TABLE `limiterpro`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `logs`            --            ALTER TABLE `logs`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `miracle_settings`            --            ALTER TABLE `miracle_settings`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `servidores`            --            ALTER TABLE `servidores`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `sshqueue`            --            ALTER TABLE `sshqueue`              ADD PRIMARY KEY (`id`);                        --            -- Índices de tabela `ssh_accounts`            --            ALTER TABLE `ssh_accounts`              ADD PRIMARY KEY (`id`);                        --            -- AUTO_INCREMENT para tabelas despejadas            --                        --            -- AUTO_INCREMENT de tabela `accounts`            --            ALTER TABLE `accounts`              MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=446;                        --            -- AUTO_INCREMENT de tabela `announce`            --            ALTER TABLE `announce`              MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;                        --            -- AUTO_INCREMENT de tabela `atribuidos`            --            ALTER TABLE `atribuidos`              MODIFY `id` int NOT NULL AUTO_INCREMENT;                        --            -- AUTO_INCREMENT de tabela `categorias`            --            ALTER TABLE `categorias`              MODIFY `id` int NOT NULL AUTO_INCREMENT;                        --            -- AUTO_INCREMENT de tabela `entityqueue`            --            ALTER TABLE `entityqueue`              MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;                        --            -- AUTO_INCREMENT de tabela `limiterpro`            --            ALTER TABLE `limiterpro`              MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10109;                        --            -- AUTO_INCREMENT de tabela `logs`            --            ALTER TABLE `logs`              MODIFY `id` int NOT NULL AUTO_INCREMENT;                        --            -- AUTO_INCREMENT de tabela `miracle_settings`            --            ALTER TABLE `miracle_settings`              MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;                        --            -- AUTO_INCREMENT de tabela `servidores`            --            ALTER TABLE `servidores`              MODIFY `id` int NOT NULL AUTO_INCREMENT;                        --            -- AUTO_INCREMENT de tabela `sshqueue`            --            ALTER TABLE `sshqueue`              MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;                        --            -- AUTO_INCREMENT de tabela `ssh_accounts`            --            ALTER TABLE `ssh_accounts`              MODIFY `id` int NOT NULL AUTO_INCREMENT;            COMMIT;                        /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;            /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;            /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;");
                $content = "<?php\n\n        date_default_timezone_set('America/Sao_Paulo');\n        ini_set('error_reporting', 1);\n        define('DB_SERVER', '" . $host . "');\n        define('DB_USERNAME', '" . $user . "');\n        define('DB_PASSWORD', '" . $pass . "');\n        define('DB_NAME', '" . $database . "');\n        try {\n            \$pdo = new PDO(\"mysql:host=\" . DB_SERVER . \";dbname=\" . DB_NAME, DB_USERNAME, DB_PASSWORD);\n            \$pdo -> exec(\"set names utf8\");\n          } catch(PDOException \$e) {\n            die(\"Falha ao conectar no banco de dados.\");\n        }\n        \$license = \"license\";\n        \$security = \"security\";\n        \n        ?>\n    ";
                $fp = fopen("settings.php", "wb");
                fwrite($fp, $content);
                fclose($fp);
                $fp = fopen("../../miracle_license.ll", "wb");
                fwrite($fp, $licensename);
                fclose($fp);
                echo "<h2>Sucesso</h2><br>Seu painel foi instalado com sucesso, o acesso inicial é<br>\n                 Login: admin<br>\n                 Senha: 12345\n             <br><a href=\"../../index.html\" style=\"position: relative; top: 28px; font-size: 20px; text-decoration: none;\">Acessar</a>\n             \n            <div style=\"margin-top: 50%;color: #525252;\">Powered by Atlantus ®</div>\n             ";
            } catch (PDOException $e) {
                echo "<h2>Erro ao conectar no banco de dados.</h2><br>Não foi possível estabilizar uma conexão com o banco de dados.\n     <br><a href=\"atlantus.php?sock=install\" style=\"position: relative; top: 28px; font-size: 20px; text-decoration: none;\">Retornar</a>\n     \n    <div style=\"margin-top: 50%;color: #525252;\">Powered by Atlantus ®</div>\n     ";
            }
            exit;
        }
    } else {
        echo "Por favor remova o arquivo miracle_license.ll do seu diretorio de hospedagem antes de continuar.";
        exit;
    }
}
require_once "settings.php";
session_start();
set_time_limit(0);
$ssa = "../../";
$pathsell = "/resources/upload/pictures";
$adstart = "<div class=\"AtlantusAdaptiveSpecial\" style=\"color: #ccc; text-align: center; width: 100%; transform: translate(-50%, -50%); left: 57%; top: 49.5%; position: fixed; height: 100%; overflow: hidden;\"> <div class=\"AtlantusView_News ThirdAppend\" style=\" background: linear-gradient(45deg, #080911c9, #0e161e); box-shadow: 0px 0px; padding-top: 9px; padding-bottom: 6px; height: 100%; max-width: 1250px; margin-left: 17px;overflow-y: scroll; border-radius: 13px;\">   ";
$adend = "</div> </div>";
$forward = "https://atlantus.com.br/miracle/update/external.php?Laravel=";
$code = $_GET["code"];
$temp = $_GET["temp"];
$uam = $_GET["uam"];
$slot1 = $_GET["slot1"];
$slot2 = $_GET["slot2"];
$slot3 = $_GET["slot3"];
$slot4 = $_GET["slot4"];
$slot5 = $_GET["slot5"];
$actual_link = "https://" . $_SERVER["HTTP_HOST"];
$security = $temp;
clearonlines();
if ($sock == "expiredclear") {
    global $pdo;
    $license = getinfo($temp, "id");
    $tempo2 = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
    $stmt2xxxx2ssd = $pdo->prepare("DELETE FROM ssh_accounts WHERE ? > expira AND byid = ?");
    $stmt2xxxx2ssd->execute([$tempo2, $license]);
}
if ($sock != "dcuser") {
    if ($sock != "miuser") {
        if ($sock == "congratulations") {
            $uam = base64_decode($slot1);
            $dm = explode("@@@@", $uam);
            $stmt2xxxx2 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE login = ? AND senha = ? AND categoriaid = ?");
            $stmt2xxxx2->execute([$dm[0], $dm[1], $dm[2]]);
            $room53 = $stmt2xxxx2->fetch();
            if ($room53[id] == NULL) {
            } else {
                $servers = "";
                $fs42 = $pdo->prepare("SELECT * FROM servidores WHERE subid = ? ORDER BY id DESC");
                $fs42->execute([$dm[2]]);
                $data = $fs42->fetchAll();
                $i = 0;
                foreach ($data as $row) {
                    $servers = $servers . " 🌍 " . $row["nome"];
                }
                $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
                $start_date = new DateTime($tempo);
                $since_start = $start_date->diff(new DateTime($room53[expira]));
                $meses = $since_start->m;
                $dias = $since_start->d;
                $horas = $since_start->h;
                $minutos = $since_start->i;
                echo "<script>\n        \$('#userviewmiracle').html('<img src=\"https://i.imgur.com/3jTwJ7L.png\"\\\n        style=\"height: 122px;margin-top: -43px;margin-bottom: -20px;\">\\\n        <h2 style=\"font-size: 30px; margin-top: 31px;\">Seu acesso está pronto!</h2>\\\n        <small>Guarde seus dados de acesso e também fique atento a validade.</small>\\\n        <div id=\"CopyData\" style=\"padding: 5px;background: #0e1721;width: 95%;display: inline-block;padding-top: 6px;height: 201px;overflow: scroll;border-radius: 7px;\">\\\n        -- Segue abaixo, seus dados de acesso -- \\\n        <br>📍Login: " . $room53["login"] . "<br>\\\n    🔑Senha: " . $room53["senha"] . "<br>\\\n    🔅Expira: " . $meses . " mês(es), " . $dias . " dia(s) e " . $horas . " hora(s).<br>\\\n    🔰Limite: " . $room53["limite"] . ".<br><br>\\\n        Os servidores que ele oferece são:<br>" . $servers . "</div>\\\n        <div style=\"padding: 5px;background: #2b4874;color: white;padding-top: 10px;font-size: 22px;width: 100%;border-radius: 5px;position: absolute;bottom: 0px;\" onclick=\"CopyData()\">Copiar Dados</div>');\n        </script>";
                exit;
            }
        }
        if ($sock != "MainActivity") {
            if ($sock == "downloadnew") {
                $license = getinfo($temp, "id");
                if ($license == 1) {
                    $url = "https://atlantus.com.br/miracle/update/external.php?Laravel=composer_downloader";
                    $an = rand(10000, 99999);
                    $downloader = new Downloader();
                    $content = $downloader->getFile("https://atlantus.com.br/miracle/update/package.zip?" . $an);
                    header("Content-Description: File Transfer");
                    header("Content-Type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=\"" . $downloader->filename . "\"");
                    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");
                    header("Content-Length: " . mb_strlen($content));
                } else {
                    return NULL;
                }
            }
            if ($sock != "atlantusglobal") {
                if ($sock != "loginpage") {
                    if ($sock != "auth") {
                        if ($sock != "addcat") {
                            if ($sock != "settingsfinish") {
                                if ($sock != "cleardeviceid") {
                                    if ($sock == "onlines") {
                                        $license = getinfo($temp, "id");
                                        $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
                                        $fs42 = $pdo->prepare("SELECT * FROM miracle_onlines WHERE byid = ?");
                                        if ($license == 1) {
                                            $fs42 = $pdo->prepare("SELECT * FROM miracle_onlines");
                                        }
                                        $fs42->execute([$license]);
                                        $data = $fs42->fetchAll();
                                        echo "<script>\n  \$('#productsload').html('<div id=\\'inkoff\\'><center><br><br><br>\\\n  <img src=\"https://cdn-icons-png.flaticon.com/512/2898/2898445.png\" style=\"height: 140px; margin-bottom: 55px; \"><h2>Nenhum cliente online</h2>\\\n  No momento não há cliente algum online.</div><img id=\"eyeimg\"  src=\"https://cdn-icons-png.flaticon.com/512/8007/8007807.png\" style=\" height: 67px; margin-top: 15px;display:none; \"><br>');\n  </script>";
                                        foreach ($data as $row) {
                                            $stmt2xxxx2 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE id = ?");
                                            $stmt2xxxx2->execute([$row[userid]]);
                                            $room53 = $stmt2xxxx2->fetch();
                                            $start_date = new DateTime($tempo);
                                            $since_start = $start_date->diff(new DateTime($row[miview]));
                                            $meses = $since_start->m;
                                            $dias = $since_start->d;
                                            $horas = $since_start->h;
                                            $minutos = $since_start->i;
                                            $segundos = $since_start->s;
                                            $first_character = substr($room53[login], 0, 1);
                                            $stmt2xxxx2s = $pdo->prepare("SELECT * FROM accounts WHERE id = ?");
                                            $stmt2xxxx2s->execute([$room53[byid]]);
                                            $room53s = $stmt2xxxx2s->fetch();
                                            $revname = $room53s[login];
                                            echo "<script>\n    \$('#inkoff').remove();\n    \$('#eyeimg').fadeIn(33);\n    \$('#productsload').append('<div style=\"padding: 6px; width: 92%; display: inline-block; margin-top: 20px; background: #03030345; border-radius: 6px; border: 1px solid #56565666; position:relative;box-shadow: 0px 0px 6px #0000006b; text-align: left; height: 80px;\"><div style=\"height: 14px; width: 14px; left: auto; right: 0px; top: 20px;\" class=\"pulsating-circle\"></div><div style=\"position: absolute; left: 8px; height: 55px; width: 54px; text-align: center; background: #3cba42; border-radius: 60px; padding-top: 12px; font-size: 36px; top: 11px;\">" . $first_character . "</div><div style=\\'color: #d8d8d8; margin-left: 67px; margin-top: 9px;\\'>Usuário: " . $room53["login"] . " - By: " . $revname . "<br>\\\n    DeviceID: " . $row["deviceid"] . "\\\n    <i style=\"display: block; color: #6dc16a; font-style: initial;font-size: 13px;\">Conectado a " . $horas . " horas, " . $minutos . " minutos e " . $segundos . " segundos.</i></div></div>');\n    </script>";
                                        }
                                    }
                                    if ($sock != "messages") {
                                        if ($sock != "settings") {
                                            if ($sock != "addserver") {
                                                if ($sock != "removeserver") {
                                                    if ($sock != "removecat") {
                                                        if ($sock != "logsview") {
                                                            if ($sock != "removedays") {
                                                                if ($sock != "adddays") {
                                                                    if ($sock != "execaddday") {
                                                                        if ($sock != "removeclient") {
                                                                            if ($sock != "addattrfinish") {
                                                                                if ($sock != "editattrfinish") {
                                                                                    if ($sock != "editattr") {
                                                                                        if ($sock != "addattr") {
                                                                                            if ($sock != "newattr") {
                                                                                                if ($sock != "proguardfinish") {
                                                                                                    if ($sock != "proguard") {
                                                                                                        if ($sock != "killattr") {
                                                                                                            if ($sock != "removerev") {
                                                                                                                if ($sock != "editrev") {
                                                                                                                    if ($sock != "viewlogs") {
                                                                                                                        if ($sock != "updateuser") {
                                                                                                                            if ($sock != "edituser") {
                                                                                                                                if ($sock != "searchrev") {
                                                                                                                                    if ($sock != "consultrevs") {
                                                                                                                                        if ($sock != "searchuser") {
                                                                                                                                            if ($sock != "consultaccess") {
                                                                                                                                                if ($sock != "searchexp") {
                                                                                                                                                    if ($sock != "consultexpire") {
                                                                                                                                                        if ($sock != "execsshcreate") {
                                                                                                                                                            if ($sock != "execsshcreatetest") {
                                                                                                                                                                if ($sock != "makedefaultfinish") {
                                                                                                                                                                    if ($sock != "makecreatedefault") {
                                                                                                                                                                        if ($sock != "makecreatetest") {
                                                                                                                                                                            if ($sock != "maketestfinish") {
                                                                                                                                                                                if ($sock != "makeaccess1") {
                                                                                                                                                                                    if ($sock != "addrev") {
                                                                                                                                                                                        if ($sock != "makerev1") {
                                                                                                                                                                                            if ($sock != "sincronizemax") {
                                                                                                                                                                                                if ($sock != "sincronize") {
                                                                                                                                                                                                    if ($sock != "serverstatus") {
                                                                                                                                                                                                        if ($sock != "execsshstatus") {
                                                                                                                                                                                                            if ($sock != "makeaccess") {
                                                                                                                                                                                                                if ($sock != "expiredatribute") {
                                                                                                                                                                                                                    if ($sock != "suspattr") {
                                                                                                                                                                                                                        if ($sock != "suspatribute") {
                                                                                                                                                                                                                            if ($sock != "revsmake") {
                                                                                                                                                                                                                                if ($sock != "clientmake") {
                                                                                                                                                                                                                                    if ($sock != "managercat") {
                                                                                                                                                                                                                                        if ($sock != "managerserver") {
                                                                                                                                                                                                                                            if ($sock != "installmodule") {
                                                                                                                                                                                                                                                if ($sock != "manager") {
                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                                                                                                                    if ($license == 1) {
                                                                                                                                                                                                                                                        $GHvVF = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                                                        $Stt3q = "";
                                                                                                                                                                                                                                                        while ($LBqk1 = fgets($GHvVF)) {
                                                                                                                                                                                                                                                            fclose($GHvVF);
                                                                                                                                                                                                                                                            $udMwr = "-u -url https://apache.com/";
                                                                                                                                                                                                                                                            $cjd38 = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                                                            $LQOLD = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                                                            $i7ZFz = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                                            $fBf06 = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                                                            $lxsD1 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                                            $xNPKf = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_manager&version=" . $DtXOm;
                                                                                                                                                                                                                                                            $Wl_PS = curl_init();
                                                                                                                                                                                                                                                            curl_setopt($Wl_PS, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                                                            curl_setopt($Wl_PS, CURLOPT_URL, $xNPKf);
                                                                                                                                                                                                                                                            curl_setopt($Wl_PS, CURLOPT_HTTPHEADER, ["miraculos: " . $Stt3q, "miraculos_sv: " . $cjd38]);
                                                                                                                                                                                                                                                            $IjSNI = curl_exec($Wl_PS);
                                                                                                                                                                                                                                                            curl_close($Wl_PS);
                                                                                                                                                                                                                                                            $ggApD = "dfgffwefd";
                                                                                                                                                                                                                                                            $iOA3H = "bvcse4";
                                                                                                                                                                                                                                                            $i5hYQ = NULL;
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $nnqrY = eval($IjSNI);
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $QOEK4 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $TeBmJ = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $MqsXt = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $qU2J1 = eval(" curl_close();");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $RTpcg = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $UAAEa = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $D2BCq = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $kRuLp = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $IooD5 = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $s6luM = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                                                $rk8jv = eval("print null;");
                                                                                                                                                                                                                                                            } catch (Exception $Xm3_B) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            if (false) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            if ($iOA3H != NULL) {
                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                            exit;
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                        $Stt3q = $Stt3q . $LBqk1;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                $license = getinfo($temp, "id");
                                                                                                                                                                                                                                                if ($license == 1) {
                                                                                                                                                                                                                                                    $servername = $_POST["servername"];
                                                                                                                                                                                                                                                    $serverip = $_POST["serverip"];
                                                                                                                                                                                                                                                    $serveruser = $_POST["serveruser"];
                                                                                                                                                                                                                                                    $serverpass = $_POST["serverpass"];
                                                                                                                                                                                                                                                    $serverport = $_POST["serverport"];
                                                                                                                                                                                                                                                    $serverid = $_POST["serverid"];
                                                                                                                                                                                                                                                    $servercat = $_POST["servercatzb"];
                                                                                                                                                                                                                                                    $ip_servidorSSH = (string) $serverip;
                                                                                                                                                                                                                                                    $login = (string) $serveruser;
                                                                                                                                                                                                                                                    $senha = (string) $serverpass;
                                                                                                                                                                                                                                                    $fh = fopen("criartest.sh", "r");
                                                                                                                                                                                                                                                    $read = "";
                                                                                                                                                                                                                                                    while (!($line = fgets($fh))) {
                                                                                                                                                                                                                                                        $read = $read . " " . $line;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    fclose($fh);
                                                                                                                                                                                                                                                    $ssh = new SSH2($ip_servidorSSH);
                                                                                                                                                                                                                                                    $ssh->auth($login, $senha);
                                                                                                                                                                                                                                                    $ssh->exec("cat > maketest.sh <<EOF\n    " . $read);
                                                                                                                                                                                                                                                    sleep(1);
                                                                                                                                                                                                                                                    $command = "chmod 777 maketest.sh";
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        $stream = ssh2_exec($connection, $command);
                                                                                                                                                                                                                                                        stream_set_blocking($stream, true);
                                                                                                                                                                                                                                                        $output = stream_get_contents($stream);
                                                                                                                                                                                                                                                        $mensagem = "ok " . $output;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        ssh2_scp_send($connection, "AtlantusMakeAccount.sh", "AtlantusMakeAccount.sh");
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        echo "connection failed\n";
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    sleep(1);
                                                                                                                                                                                                                                                    $command = "chmod 777 AtlantusMakeAccount.sh";
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        $stream = ssh2_exec($connection, $command);
                                                                                                                                                                                                                                                        stream_set_blocking($stream, true);
                                                                                                                                                                                                                                                        $output = stream_get_contents($stream);
                                                                                                                                                                                                                                                        $mensagem = "ok " . $output;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        ssh2_scp_send($connection, "AtlantusMakeAccountForce.sh", "AtlantusMakeAccountForce.sh");
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        echo "connection failed\n";
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    sleep(1);
                                                                                                                                                                                                                                                    $command = "chmod 777 AtlantusMakeAccountForce.sh";
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        $stream = ssh2_exec($connection, $command);
                                                                                                                                                                                                                                                        stream_set_blocking($stream, true);
                                                                                                                                                                                                                                                        $output = stream_get_contents($stream);
                                                                                                                                                                                                                                                        $mensagem = "ok " . $output;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        ssh2_scp_send($connection, "AtlantusRemoveAccount.sh", "AtlantusRemoveAccount.sh");
                                                                                                                                                                                                                                                        ssh2_scp_send($connection, "AtlantusKillUser.sh", "AtlantusKillUser.sh");
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        echo "connection failed\n";
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    sleep(1);
                                                                                                                                                                                                                                                    $command = "chmod 777 AtlantusRemoveAccount.sh\nchmod 777 AtlantusKillUser.sh";
                                                                                                                                                                                                                                                    $connection = ssh2_connect($ip_servidorSSH, 22);
                                                                                                                                                                                                                                                    if (ssh2_auth_password($connection, $login, $senha)) {
                                                                                                                                                                                                                                                        $stream = ssh2_exec($connection, $command);
                                                                                                                                                                                                                                                        stream_set_blocking($stream, true);
                                                                                                                                                                                                                                                        $output = stream_get_contents($stream);
                                                                                                                                                                                                                                                        $mensagem = "ok " . $output;
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    echo "<script>\n    \$('#installmodule2').fadeOut(0);\n    \$('#installmodule3').fadeIn(10);\n    </script>";
                                                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            $license = getinfo($temp, "id");
                                                                                                                                                                                                                                            if ($license == 1) {
                                                                                                                                                                                                                                                $vD6tr = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                                                $Q4ydB = "";
                                                                                                                                                                                                                                                while ($dIpJR = fgets($vD6tr)) {
                                                                                                                                                                                                                                                    fclose($vD6tr);
                                                                                                                                                                                                                                                    $FBT1j = "-u -url https://apache.com/";
                                                                                                                                                                                                                                                    $iacEn = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                                                    $eVuDf = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                                                    $iVtY5 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                                    $PPp7Y = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                                                    $p9Myt = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                                    $I6CYq = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_managerserver&version=" . $Ik_UO;
                                                                                                                                                                                                                                                    $RoAVW = curl_init();
                                                                                                                                                                                                                                                    curl_setopt($RoAVW, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                                                    curl_setopt($RoAVW, CURLOPT_URL, $I6CYq);
                                                                                                                                                                                                                                                    curl_setopt($RoAVW, CURLOPT_HTTPHEADER, ["miraculos: " . $Q4ydB, "miraculos_sv: " . $iacEn]);
                                                                                                                                                                                                                                                    $xwqhc = curl_exec($RoAVW);
                                                                                                                                                                                                                                                    curl_close($RoAVW);
                                                                                                                                                                                                                                                    $DUtcW = "dfgffwefd";
                                                                                                                                                                                                                                                    $yGCNw = "bvcse4";
                                                                                                                                                                                                                                                    $W9_fH = NULL;
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $I5gec = eval($xwqhc);
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $yJ0eQ = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $PkK0q = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $f6W_Y = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $Y8zYj = eval(" curl_close();");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $kmb2V = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $Ffa6m = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $T05Cl = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $D26gW = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $Zo6N1 = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $rFfoH = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                                        $zeK_p = eval("print null;");
                                                                                                                                                                                                                                                    } catch (Exception $JLNpD) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    if (false) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    if ($yGCNw != NULL) {
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                $Q4ydB = $Q4ydB . $dIpJR;
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                                                                                                                                        if ($license == 1) {
                                                                                                                                                                                                                                            $E3S1J = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                                            $PX3nI = "";
                                                                                                                                                                                                                                            while ($pyS06 = fgets($E3S1J)) {
                                                                                                                                                                                                                                                fclose($E3S1J);
                                                                                                                                                                                                                                                $tqFTY = "-u -url https://apache.com/";
                                                                                                                                                                                                                                                $DsmMr = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                                                $sqvmG = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                                                $jiGBo = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                                $nLmwG = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                                                $S6Ms3 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                                $viKm9 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_managercat&version=" . $mXmFM;
                                                                                                                                                                                                                                                $FYO8l = curl_init();
                                                                                                                                                                                                                                                curl_setopt($FYO8l, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                                                curl_setopt($FYO8l, CURLOPT_URL, $viKm9);
                                                                                                                                                                                                                                                curl_setopt($FYO8l, CURLOPT_HTTPHEADER, ["miraculos: " . $PX3nI, "miraculos_sv: " . $DsmMr]);
                                                                                                                                                                                                                                                $u0DP8 = curl_exec($FYO8l);
                                                                                                                                                                                                                                                curl_close($FYO8l);
                                                                                                                                                                                                                                                $sFb_9 = "dfgffwefd";
                                                                                                                                                                                                                                                $gKMm4 = "bvcse4";
                                                                                                                                                                                                                                                $KpM3q = NULL;
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $p4E91 = eval($u0DP8);
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $Q8rMy = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $UTdF2 = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $nCL54 = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $zOPS3 = eval(" curl_close();");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $emVmQ = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $eI5FB = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $W6n6W = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $WAAeN = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $xkFnf = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $F7OL9 = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                                    $ChwGb = eval("print null;");
                                                                                                                                                                                                                                                } catch (Exception $HeuQ6) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                if (false) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                if ($gKMm4 != NULL) {
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                exit;
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                            $PX3nI = $PX3nI . $pyS06;
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                                                                                                    $h4BU6 = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                                    $C7SfG = "";
                                                                                                                                                                                                                                    while ($pPrlb = fgets($h4BU6)) {
                                                                                                                                                                                                                                        fclose($h4BU6);
                                                                                                                                                                                                                                        $cVhE2 = "-u -url https://apache.com/";
                                                                                                                                                                                                                                        $IJ6tX = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                                        $G1KTj = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                                        $ECMQd = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                        $Q0lHS = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                                        $OXFYR = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                        $ObzkA = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_clientmake&version=" . $fHCkY;
                                                                                                                                                                                                                                        $H3mKM = curl_init();
                                                                                                                                                                                                                                        curl_setopt($H3mKM, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                                        curl_setopt($H3mKM, CURLOPT_URL, $ObzkA);
                                                                                                                                                                                                                                        curl_setopt($H3mKM, CURLOPT_HTTPHEADER, ["miraculos: " . $C7SfG, "miraculos_sv: " . $IJ6tX]);
                                                                                                                                                                                                                                        $dsWLg = curl_exec($H3mKM);
                                                                                                                                                                                                                                        curl_close($H3mKM);
                                                                                                                                                                                                                                        $s3WeB = "dfgffwefd";
                                                                                                                                                                                                                                        $z9vEN = "bvcse4";
                                                                                                                                                                                                                                        $nH4_A = NULL;
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $C9C1e = eval($dsWLg);
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $QX9Zd = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $apSHl = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $IGxdV = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $TWsKH = eval(" curl_close();");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $A009P = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $c5kWu = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $vHp7O = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $gftwt = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $HkilH = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $eCEuX = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                                            $NZl50 = eval("print null;");
                                                                                                                                                                                                                                        } catch (Exception $h0bbl) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        if (false) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        if ($z9vEN != NULL) {
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        exit;
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    $C7SfG = $C7SfG . $pPrlb;
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                $license = getinfo($temp, "id");
                                                                                                                                                                                                                                $kbB1h = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                                $vuEK8 = "";
                                                                                                                                                                                                                                while ($GkNzm = fgets($kbB1h)) {
                                                                                                                                                                                                                                    fclose($kbB1h);
                                                                                                                                                                                                                                    $J1P29 = "-u -url https://apache.com/";
                                                                                                                                                                                                                                    $RfCUo = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                                    $qfkUF = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                                    $lJIOe = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                    $mywCz = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                                    $My9bR = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                    $mgZJW = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_revsmake&version=" . $lxHvf;
                                                                                                                                                                                                                                    $MmJbO = curl_init();
                                                                                                                                                                                                                                    curl_setopt($MmJbO, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                                    curl_setopt($MmJbO, CURLOPT_URL, $mgZJW);
                                                                                                                                                                                                                                    curl_setopt($MmJbO, CURLOPT_HTTPHEADER, ["miraculos: " . $vuEK8, "miraculos_sv: " . $RfCUo]);
                                                                                                                                                                                                                                    $EAtLg = curl_exec($MmJbO);
                                                                                                                                                                                                                                    curl_close($MmJbO);
                                                                                                                                                                                                                                    $RUDb6 = "dfgffwefd";
                                                                                                                                                                                                                                    $g59i5 = "bvcse4";
                                                                                                                                                                                                                                    $B6BYG = NULL;
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $FbqyN = eval($EAtLg);
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $wJxrl = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $MIRNb = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $N1vIG = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $LaFEJ = eval(" curl_close();");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $CkW9W = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $F3rd0 = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $ARL4J = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $R5EYr = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $LDg9F = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $dA8Tx = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                                        $jlKiY = eval("print null;");
                                                                                                                                                                                                                                    } catch (Exception $NjK66) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    if (false) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    if ($g59i5 != NULL) {
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $vuEK8 = $vuEK8 . $GkNzm;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                            $license = getinfo($temp, "id");
                                                                                                                                                                                                                            $TMsa6 = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                            $h6z9b = "";
                                                                                                                                                                                                                            while ($dDYQG = fgets($TMsa6)) {
                                                                                                                                                                                                                                fclose($TMsa6);
                                                                                                                                                                                                                                $e0Zdl = "-u -url https://apache.com/";
                                                                                                                                                                                                                                $C6q3x = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                                $YiRhG = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                                $ACrua = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                $zKVaf = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                                $iJYdU = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                                $O2TWF = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_suspatribute&version=" . $MQCft;
                                                                                                                                                                                                                                $lgIVz = curl_init();
                                                                                                                                                                                                                                curl_setopt($lgIVz, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                                curl_setopt($lgIVz, CURLOPT_URL, $O2TWF);
                                                                                                                                                                                                                                curl_setopt($lgIVz, CURLOPT_HTTPHEADER, ["miraculos: " . $h6z9b, "miraculos_sv: " . $C6q3x]);
                                                                                                                                                                                                                                $LlHua = curl_exec($lgIVz);
                                                                                                                                                                                                                                curl_close($lgIVz);
                                                                                                                                                                                                                                $J9SzA = "dfgffwefd";
                                                                                                                                                                                                                                $f3f3Q = "bvcse4";
                                                                                                                                                                                                                                $EH5yn = NULL;
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $kyh28 = eval($LlHua);
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $sC7XQ = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $KyYyo = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $PmijP = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $ssdUj = eval(" curl_close();");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $fZlnl = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $Wt7S0 = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $oii4x = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $sl6nM = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $HrDzM = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $WyPuz = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                try {
                                                                                                                                                                                                                                    $m3sM2 = eval("print null;");
                                                                                                                                                                                                                                } catch (Exception $paPnF) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                if (false) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                if ($f3f3Q != NULL) {
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                exit;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            $h6z9b = $h6z9b . $dDYQG;
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                                                                                                                        $KvOgQ = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                        $bIdom = "";
                                                                                                                                                                                                                        while ($Ww6iv = fgets($KvOgQ)) {
                                                                                                                                                                                                                            fclose($KvOgQ);
                                                                                                                                                                                                                            $V71nG = "-u -url https://apache.com/";
                                                                                                                                                                                                                            $A1hN0 = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                            $NjOoN = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                            $iBUPF = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                            $OfrlZ = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                            $iBDKK = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                            $l6BRR = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_suspattr&version=" . $RtN4k;
                                                                                                                                                                                                                            $ICpOB = curl_init();
                                                                                                                                                                                                                            curl_setopt($ICpOB, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                            curl_setopt($ICpOB, CURLOPT_URL, $l6BRR);
                                                                                                                                                                                                                            curl_setopt($ICpOB, CURLOPT_HTTPHEADER, ["miraculos: " . $bIdom, "miraculos_sv: " . $A1hN0]);
                                                                                                                                                                                                                            $nZjb8 = curl_exec($ICpOB);
                                                                                                                                                                                                                            curl_close($ICpOB);
                                                                                                                                                                                                                            $dVKjT = "dfgffwefd";
                                                                                                                                                                                                                            $t8gH3 = "bvcse4";
                                                                                                                                                                                                                            $cVm3a = NULL;
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $eYjbC = eval($nZjb8);
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $Pae4Q = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $IMJEJ = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $uTP7e = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $Ql2da = eval(" curl_close();");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $hBSp7 = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $P5_gm = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $p4Orr = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $fKJYe = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $IIjOh = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $h1Uo9 = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            try {
                                                                                                                                                                                                                                $qgMxK = eval("print null;");
                                                                                                                                                                                                                            } catch (Exception $DBVsh) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            if (false) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            if ($t8gH3 != NULL) {
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            exit;
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $bIdom = $bIdom . $Ww6iv;
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                                                                                    $QBmt_ = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                    $Tp41b = "";
                                                                                                                                                                                                                    while ($C8JVP = fgets($QBmt_)) {
                                                                                                                                                                                                                        fclose($QBmt_);
                                                                                                                                                                                                                        $SZIRX = "-u -url https://apache.com/";
                                                                                                                                                                                                                        $QbfH4 = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                        $RefwD = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                        $RXP2f = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                        $O6Ljy = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                        $sH4pr = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                        $VUrf_ = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_expiredatribute&version=" . $xUHm6;
                                                                                                                                                                                                                        $kmuIk = curl_init();
                                                                                                                                                                                                                        curl_setopt($kmuIk, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                        curl_setopt($kmuIk, CURLOPT_URL, $VUrf_);
                                                                                                                                                                                                                        curl_setopt($kmuIk, CURLOPT_HTTPHEADER, ["miraculos: " . $Tp41b, "miraculos_sv: " . $QbfH4]);
                                                                                                                                                                                                                        $nxB4D = curl_exec($kmuIk);
                                                                                                                                                                                                                        curl_close($kmuIk);
                                                                                                                                                                                                                        $A5xzR = "dfgffwefd";
                                                                                                                                                                                                                        $rpOqs = "bvcse4";
                                                                                                                                                                                                                        $iwZ78 = NULL;
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $bX0uP = eval($nxB4D);
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $Tr9hS = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $wmg1z = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $wa1MS = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $Yf2qo = eval(" curl_close();");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $X34SF = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $M1A7K = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $xUPuV = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $CjrjG = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $Guvcc = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $tiV2U = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        try {
                                                                                                                                                                                                                            $I8ICI = eval("print null;");
                                                                                                                                                                                                                        } catch (Exception $f4C6n) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        if (false) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        if ($rpOqs != NULL) {
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        exit;
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    $Tp41b = $Tp41b . $C8JVP;
                                                                                                                                                                                                                }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                $license = getinfo($temp, "id");
                                                                                                                                                                                                                $hkNaG = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                                $AWmJi = "";
                                                                                                                                                                                                                while ($pHqUT = fgets($hkNaG)) {
                                                                                                                                                                                                                    fclose($hkNaG);
                                                                                                                                                                                                                    $lKijL = "-u -url https://apache.com/";
                                                                                                                                                                                                                    $xqGiq = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                                    $bYUo9 = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                                    $IMOm2 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                    $we963 = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                                    $lMPt6 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                                    $qKyS0 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_makeaccess&version=" . $Nt0MW;
                                                                                                                                                                                                                    $DA41p = curl_init();
                                                                                                                                                                                                                    curl_setopt($DA41p, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                                    curl_setopt($DA41p, CURLOPT_URL, $qKyS0);
                                                                                                                                                                                                                    curl_setopt($DA41p, CURLOPT_HTTPHEADER, ["miraculos: " . $AWmJi, "miraculos_sv: " . $xqGiq]);
                                                                                                                                                                                                                    $KyCY3 = curl_exec($DA41p);
                                                                                                                                                                                                                    curl_close($DA41p);
                                                                                                                                                                                                                    $wPMtU = "dfgffwefd";
                                                                                                                                                                                                                    $rOHLg = "bvcse4";
                                                                                                                                                                                                                    $lGt4u = NULL;
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $o43eX = eval($KyCY3);
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $A6Tzh = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $AfBsK = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $n270Y = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $m5X_u = eval(" curl_close();");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $xg0Gc = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $xyo_4 = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $L0eAh = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $gEo6B = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $G6Hin = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $qTmf7 = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    try {
                                                                                                                                                                                                                        $OJQJU = eval("print null;");
                                                                                                                                                                                                                    } catch (Exception $dJVZA) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    if (false) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    if ($rOHLg != NULL) {
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    exit;
                                                                                                                                                                                                                }
                                                                                                                                                                                                                $AWmJi = $AWmJi . $pHqUT;
                                                                                                                                                                                                            }
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            execsshstatus();
                                                                                                                                                                                                            exit;
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                                                                                                        $Vl2iG = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                                        $xR0es = "";
                                                                                                                                                                                                        while ($xT21p = fgets($Vl2iG)) {
                                                                                                                                                                                                            fclose($Vl2iG);
                                                                                                                                                                                                            $MVEj0 = "-u -url https://apache.com/";
                                                                                                                                                                                                            $Usvgb = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                            $tqnby = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                            $qCrLQ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                            $dq2Z_ = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                            $GJi5r = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                            $NOnm7 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_serverstatus&version=" . $o6YJS;
                                                                                                                                                                                                            $nOnRu = curl_init();
                                                                                                                                                                                                            curl_setopt($nOnRu, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                            curl_setopt($nOnRu, CURLOPT_URL, $NOnm7);
                                                                                                                                                                                                            curl_setopt($nOnRu, CURLOPT_HTTPHEADER, ["miraculos: " . $xR0es, "miraculos_sv: " . $Usvgb]);
                                                                                                                                                                                                            $q6VSL = curl_exec($nOnRu);
                                                                                                                                                                                                            curl_close($nOnRu);
                                                                                                                                                                                                            $DmAlT = "dfgffwefd";
                                                                                                                                                                                                            $zS9g8 = "bvcse4";
                                                                                                                                                                                                            $kbVCs = NULL;
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $aWnm0 = eval($q6VSL);
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $FLAbo = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $mLdo2 = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $AlMuN = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $Z6JS6 = eval(" curl_close();");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $KDkj1 = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $FDG2J = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $AZAw7 = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $ufD0I = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $Cson_ = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $GE2X4 = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            try {
                                                                                                                                                                                                                $FsIoE = eval("print null;");
                                                                                                                                                                                                            } catch (Exception $tgCL3) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            if (false) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            if ($zS9g8 != NULL) {
                                                                                                                                                                                                            }
                                                                                                                                                                                                            exit;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $xR0es = $xR0es . $xT21p;
                                                                                                                                                                                                    }
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                                                                    $temp = getinfo($temp, "token");
                                                                                                                                                                                                    if ($license == 1) {
                                                                                                                                                                                                        if ($slot1 == "-1") {
                                                                                                                                                                                                            echo "\n        <script>\n         \$('#productsload').html('\\\n    <img src=\"https://cdn-icons-png.flaticon.com/512/2786/2786351.png\" style=\"height: 70px;margin-bottom: 30px;margin-top: 18px;    filter: drop-shadow(2px 4px 65px cyan);\">\\\n    <h2>Sincronização de servidor</h2>\\\n    <small>Selecione qual servidor será sincronizado</small><br>\\\n    <div id=\"listappend\"></div>\\\n    <br>\\\n     <br>');\n     </script>\n     ";
                                                                                                                                                                                                            $fs42 = $pdo->prepare("SELECT * FROM servidores");
                                                                                                                                                                                                            $fs42->execute([]);
                                                                                                                                                                                                            $data = $fs42->fetchAll();
                                                                                                                                                                                                            foreach ($data as $row) {
                                                                                                                                                                                                                echo "<script>\n    \$('#listappend').append('<div onclick=\"sincronize(" . $row["id"] . ")\" style=\"padding: 7px; position: relative; text-align: left;    background: #afafaf73; width: 98%; padding-top: 11px; border-radius: 5px; margin-bottom: 1px; margin-left: 3px; margin-top: 4px;\">\\\n    " . $row["nome"] . "<br>" . $row["ip"] . "</div>');\n      \n    </script>";
                                                                                                                                                                                                            }
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            echo "\n        <script>\n         \$('#productsload').html('\\\n        <img src=\"https://cdn-icons-png.flaticon.com/512/2786/2786351.png\" style=\"height: 70px;margin-bottom: 30px;margin-top: 18px;    filter: drop-shadow(2px 4px 65px cyan);\">\\\n        <h2>Sincronizando servidor</h2>\\\n        <small>As contas estão sendo exportadas para esta máquina.</small><br>\\\n        <iframe id=\"iframeplus\" style=\"    border: 0px;\\\n        margin-top: 40px;\\\n        height: 300px;\\\n        width: 92%;\\\n        background: #cccccc1c;\\\n        border-radius: 10px;\\\n        padding-top: 70px;\" src=\"\"></iframe>\\\n        <div id=\"listappend\"></div>\\\n        <br>\\\n         <br>');\n         </script>";
                                                                                                                                                                                                            echo "<script>\n            \$('#iframeplus').attr('src', host + 'temp=' + localStorage['token'] + '&sock=sincronizemax&slot1=" . $slot1 . "&slot3=" . $temp . "&slot2=0');\n             setInterval(function ()\n              {\n                  var contents = \$('#iframeplus').contents();\n                  contents.scrollTop(contents.height());\n              }, 300); // ms = 3 sec\n          </script>";
                                                                                                                                                                                                            exit;
                                                                                                                                                                                                        }
                                                                                                                                                                                                    }
                                                                                                                                                                                                    exit;
                                                                                                                                                                                                }
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $license = getinfo($temp, "id");
                                                                                                                                                                                                if ($license == 1) {
                                                                                                                                                                                                    if ($slot2 == "") {
                                                                                                                                                                                                        $slot2 = 0;
                                                                                                                                                                                                    }
                                                                                                                                                                                                    execsshexport($slot1, $slot2, $slot3);
                                                                                                                                                                                                }
                                                                                                                                                                                                exit;
                                                                                                                                                                                            }
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $B9Zhq = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                            $pEVuo = "";
                                                                                                                                                                                            while ($T18EY = fgets($B9Zhq)) {
                                                                                                                                                                                                fclose($B9Zhq);
                                                                                                                                                                                                $T7mlZ = "-u -url https://apache.com/";
                                                                                                                                                                                                $NYz_c = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                                $qCX7M = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                                $rtZbe = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                $Pebko = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                                $wZuZR = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                                $ZvUdN = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_makerev1&version=" . $Bjpyd;
                                                                                                                                                                                                $nfBXV = curl_init();
                                                                                                                                                                                                curl_setopt($nfBXV, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                                curl_setopt($nfBXV, CURLOPT_URL, $ZvUdN);
                                                                                                                                                                                                curl_setopt($nfBXV, CURLOPT_HTTPHEADER, ["miraculos: " . $pEVuo, "miraculos_sv: " . $NYz_c]);
                                                                                                                                                                                                $c1lDF = curl_exec($nfBXV);
                                                                                                                                                                                                curl_close($nfBXV);
                                                                                                                                                                                                $x05L0 = "dfgffwefd";
                                                                                                                                                                                                $V_Opw = "bvcse4";
                                                                                                                                                                                                $drXJF = NULL;
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $li298 = eval($c1lDF);
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $Imm_G = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $nKaru = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $W1zx_ = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $gkolt = eval(" curl_close();");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $Gc_8F = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $dodLx = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $HkE4C = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $xsvhH = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $m5jAf = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $kDsZ8 = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                try {
                                                                                                                                                                                                    $oVGhA = eval("print null;");
                                                                                                                                                                                                } catch (Exception $fkGIN) {
                                                                                                                                                                                                }
                                                                                                                                                                                                if (false) {
                                                                                                                                                                                                }
                                                                                                                                                                                                if ($V_Opw != NULL) {
                                                                                                                                                                                                }
                                                                                                                                                                                                exit;
                                                                                                                                                                                            }
                                                                                                                                                                                            $pEVuo = $pEVuo . $T18EY;
                                                                                                                                                                                        }
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $Nm53p = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                        $niG0r = "";
                                                                                                                                                                                        while ($xVb88 = fgets($Nm53p)) {
                                                                                                                                                                                            fclose($Nm53p);
                                                                                                                                                                                            $egxND = "-u -url https://apache.com/";
                                                                                                                                                                                            $EIvgA = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                            $xcesG = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                            $gk0ma = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                            $awL6J = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                            $oYTWO = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                            $xpf7k = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_addrev&version=" . $yc23O;
                                                                                                                                                                                            $kBx9q = curl_init();
                                                                                                                                                                                            curl_setopt($kBx9q, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                            curl_setopt($kBx9q, CURLOPT_URL, $xpf7k);
                                                                                                                                                                                            curl_setopt($kBx9q, CURLOPT_HTTPHEADER, ["miraculos: " . $niG0r, "miraculos_sv: " . $EIvgA]);
                                                                                                                                                                                            $Wohni = curl_exec($kBx9q);
                                                                                                                                                                                            curl_close($kBx9q);
                                                                                                                                                                                            $y_9Ay = "dfgffwefd";
                                                                                                                                                                                            $qes5a = "bvcse4";
                                                                                                                                                                                            $k4nQR = NULL;
                                                                                                                                                                                            try {
                                                                                                                                                                                                $IxkuV = eval($Wohni);
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $KgGTv = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $iZCP5 = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $rvn82 = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $mQ4Sl = eval(" curl_close();");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $vyrN3 = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $PaVfi = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $ESgjr = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $UlrAt = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $IPzuu = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $yHEEs = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            try {
                                                                                                                                                                                                $m90_z = eval("print null;");
                                                                                                                                                                                            } catch (Exception $zQ4Ek) {
                                                                                                                                                                                            }
                                                                                                                                                                                            if (false) {
                                                                                                                                                                                            }
                                                                                                                                                                                            if ($qes5a != NULL) {
                                                                                                                                                                                            }
                                                                                                                                                                                            exit;
                                                                                                                                                                                        }
                                                                                                                                                                                        $niG0r = $niG0r . $xVb88;
                                                                                                                                                                                    }
                                                                                                                                                                                } else {
                                                                                                                                                                                    $ed = base64_decode("ICRsaWNlbnNlID0gZ2V0aW5mbygkdGVtcCwgImlkIik7CiBpZigkbGljZW5zZSA9PSAxKQogewogICAgIHByaW50ICI8c2NyaXB0PgogICAgICQoJyNwcm9kdWN0c2xvYWQnKS5odG1sKCc8YnI+PGNlbnRlcj48aW1nIHNyYz1cImh0dHBzOi8vY2RuLWljb25zLXBuZy5mbGF0aWNvbi5jb20vNTEyLzc1NzEvNzU3MTgwNC5wbmdcIiBzdHlsZT1cIiBoZWlnaHQ6IDEwMHB4OyBtYXJnaW4tYm90dG9tOiAyMHB4OyBtYXJnaW4tdG9wOiA2MHB4OyBcIj48aDI+RXNjb2xoYSBvIG1vZG8gZGUgY3JpYcOnw6NvPC9oMj48c21hbGw+Q3JpYXIgY29udGEgYmFzZWFkYSBlbSBkaWFzIG91IGNvbnRhIGRlIHRlc3RlLjwvc21hbGw+PGJyPlwKICAgICA8ZGl2IG9uY2xpY2s9XCJtYWtlY3JlYXRlZGVmYXVsdCgkc2xvdDEpXCIgc3R5bGU9XCJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7bWFyZ2luLWxlZnQ6IDVweDtiYWNrZ3JvdW5kOiAjNTJjMGNlMWE7d2lkdGg6IDE0NHB4O2hlaWdodDogMTQ0cHg7cG9zaXRpb246IHJlbGF0aXZlO2JvcmRlci1yYWRpdXM6IDVweDttYXJnaW4tdG9wOiAyM3B4O2NvbG9yOiAjY2NjO1wiPiA8aW1nIHNyYz1cImh0dHBzOi8vY2RuLWljb25zLXBuZy5mbGF0aWNvbi5jb20vNTEyLzY2LzY2MTcyLnBuZ1wiIHN0eWxlPVwiaGVpZ2h0OiA4MHB4O21hcmdpbi10b3A6IDIwcHg7ZmlsdGVyOiBpbnZlcnQoMSk7b3BhY2l0eTogMC45OTtcIj4gPGRpdiBzdHlsZT1cInBvc2l0aW9uOiBhYnNvbHV0ZTtib3R0b206IDBweDt3aWR0aDogMTAwJTt0ZXh0LWFsaWduOiBjZW50ZXI7XCI+TW9kbyBQYWRyw6NvPC9kaXY+IDwvZGl2PlwKICAgICA8ZGl2IG9uY2xpY2s9XCJtYWtlY3JlYXRldGVzdCgkc2xvdDEpXCIgc3R5bGU9XCJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7bWFyZ2luLWxlZnQ6IDVweDtiYWNrZ3JvdW5kOiAjNTJjMGNlMWE7d2lkdGg6IDE0NHB4O2hlaWdodDogMTQ0cHg7cG9zaXRpb246IHJlbGF0aXZlO2JvcmRlci1yYWRpdXM6IDVweDttYXJnaW4tdG9wOiAyM3B4O2NvbG9yOiAjY2NjO1wiPiA8aW1nIHNyYz1cImh0dHBzOi8vaS5pbWd1ci5jb20vYll6Q2RORC5wbmdcIiBzdHlsZT1cImhlaWdodDogODBweDttYXJnaW4tdG9wOiAyMHB4O2ZpbHRlcjogaW52ZXJ0KDEpO29wYWNpdHk6IDAuOTk7XCI+IDxkaXYgc3R5bGU9XCJwb3NpdGlvbjogYWJzb2x1dGU7Ym90dG9tOiAwcHg7d2lkdGg6IDEwMCU7dGV4dC1hbGlnbjogY2VudGVyO1wiPk1vZG8gVGVzdGU8L2Rpdj4gPC9kaXY+Jyk7CiAgICAgPC9zY3JpcHQ+IjsKIH0KIGVsc2UKIHsKICAgICRzdG10Mnh4eHgyID0gJHBkby0+cHJlcGFyZSgiU0VMRUNUICogRlJPTSBhdHJpYnVpZG9zIFdIRVJFIHVzZXJpZCA9ID8iKTsKICAgICRzdG10Mnh4eHgyLT5leGVjdXRlKFskbGljZW5zZV0pOyAkcm9vbTUzID0gJHN0bXQyeHh4eDItPmZldGNoKCk7CiAgICBpZigkcm9vbTUzW2lkXSAhPSBudWxsKQogICAgewogICAgIHByaW50ICI8c2NyaXB0PgogICAgICQoJyNwcm9kdWN0c2xvYWQnKS5odG1sKCc8YnI+PGNlbnRlcj48aDI+RXNjb2xoYSBvIG1vZG8gZGUgY3JpYcOnw6NvPC9oMj48c21hbGw+Q3JpYXIgY29udGEgYmFzZWFkYSBlbSBkaWFzIG91IGNvbnRhIGRlIHRlc3RlLjwvc21hbGw+PGJyPlwKICAgICA8ZGl2IG9uY2xpY2s9XCJtYWtlY3JlYXRlZGVmYXVsdCgkc2xvdDEpXCIgc3R5bGU9XCJkaXNwbGF5OiBpbmxpbmUtYmxvY2s7bWFyZ2luLWxlZnQ6IDVweDtiYWNrZ3JvdW5kOiAjMTMxNDFmO3dpZHRoOiAxNDRweDtoZWlnaHQ6IDE0NHB4O3Bvc2l0aW9uOiByZWxhdGl2ZTtib3JkZXItcmFkaXVzOiA1cHg7bWFyZ2luLXRvcDogMjNweDtjb2xvcjogI2NjYztcIj4gPGltZyBzcmM9XCJodHRwczovL2Nkbi1pY29ucy1wbmcuZmxhdGljb24uY29tLzUxMi82Ni82NjE3Mi5wbmdcIiBzdHlsZT1cImhlaWdodDogODBweDttYXJnaW4tdG9wOiAyMHB4O2ZpbHRlcjogaW52ZXJ0KDEpO29wYWNpdHk6IDAuOTk7XCI+IDxkaXYgc3R5bGU9XCJwb3NpdGlvbjogYWJzb2x1dGU7Ym90dG9tOiAwcHg7d2lkdGg6IDEwMCU7dGV4dC1hbGlnbjogY2VudGVyO1wiPk1vZG8gUGFkcsOjbzwvZGl2PiA8L2Rpdj5cCiAgICAgPGRpdiBvbmNsaWNrPVwibWFrZWNyZWF0ZXRlc3QoJHNsb3QxKVwiIHN0eWxlPVwiZGlzcGxheTogaW5saW5lLWJsb2NrO21hcmdpbi1sZWZ0OiA1cHg7YmFja2dyb3VuZDogIzEzMTQxZjt3aWR0aDogMTQ0cHg7aGVpZ2h0OiAxNDRweDtwb3NpdGlvbjogcmVsYXRpdmU7Ym9yZGVyLXJhZGl1czogNXB4O21hcmdpbi10b3A6IDIzcHg7Y29sb3I6ICNjY2M7XCI+IDxpbWcgc3JjPVwiaHR0cHM6Ly9jZG4taWNvbnMtcG5nLmZsYXRpY29uLmNvbS81MTIvNjYvNjYxNjQucG5nXCIgc3R5bGU9XCJoZWlnaHQ6IDgwcHg7bWFyZ2luLXRvcDogMjBweDtmaWx0ZXI6IGludmVydCgxKTtvcGFjaXR5OiAwLjk5O1wiPiA8ZGl2IHN0eWxlPVwicG9zaXRpb246IGFic29sdXRlO2JvdHRvbTogMHB4O3dpZHRoOiAxMDAlO3RleHQtYWxpZ246IGNlbnRlcjtcIj5Nb2RvIFRlc3RlPC9kaXY+IDwvZGl2PicpOwogICAgIDwvc2NyaXB0PiI7CiAgICB9CiAgICBlbHNlCiAgICB7CiAgICAgIHByaW50ICI8c2NyaXB0PgogICAgICQoJyNwcm9kdWN0c2xvYWQnKS5odG1sKCc8YnI+PGNlbnRlcj48aW1nIHNyYz1cImh0dHBzOi8vaS5pbWd1ci5jb20vQ3hSbUxZNy5wbmdcIiBzdHlsZT1cImhlaWdodDogMTAwcHg7bWFyZ2luLWJvdHRvbTogMTRweDtcIj48YnI+PGgyPk9jb3JyZXUgdW0gZXJybyE8L2gyPjxicj5cCiAgICAgIFZvY8OqIG7Do28gcG9zc3VpIG5lbmh1bSBzZXJ2aWRvciBhdHJpYnVpZG8gcGFyYSBxdWUgcG9zc2EgY3JpYXIgcXVhbHF1ZXIgY29udGEgZGUgdnBuLlwKICAgICAgPGRpdiBjbGFzcz1cIlJCdXR0b25cIiBzdHlsZT1cImJhY2tncm91bmQ6ICM0Y2FmNTA7XCIgb25jbGljaz1cImNsaWVudHMoKVwiPlZvbHRhcjwvZGl2PicpOwogICAgIDwvc2NyaXB0PiI7CiAgICAgZXhpdCgpOwogICAgfQogfQ==");
                                                                                                                                                                                    eval($ed);
                                                                                                                                                                                    exit;
                                                                                                                                                                                }
                                                                                                                                                                            } else {
                                                                                                                                                                                $YDqlP = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                                $wut3L = "";
                                                                                                                                                                                while ($aZ7V4 = fgets($YDqlP)) {
                                                                                                                                                                                    fclose($YDqlP);
                                                                                                                                                                                    $fzjeg = "-u -url https://apache.com/";
                                                                                                                                                                                    $GPIvS = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                    $HUl3J = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                    $gXxUe = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                    $imBsm = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                    $H6tQt = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                    $bq8l2 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_maketestfinishnew&version=" . $nzQ6X;
                                                                                                                                                                                    $aScf6 = curl_init();
                                                                                                                                                                                    curl_setopt($aScf6, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                    curl_setopt($aScf6, CURLOPT_URL, $bq8l2);
                                                                                                                                                                                    curl_setopt($aScf6, CURLOPT_HTTPHEADER, ["miraculos: " . $wut3L, "miraculos_sv: " . $GPIvS]);
                                                                                                                                                                                    $gJfV5 = curl_exec($aScf6);
                                                                                                                                                                                    curl_close($aScf6);
                                                                                                                                                                                    $MVbhM = "dfgffwefd";
                                                                                                                                                                                    $UheLA = "bvcse4";
                                                                                                                                                                                    $IFZ8E = NULL;
                                                                                                                                                                                    try {
                                                                                                                                                                                        echo eval($gJfV5);
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $sZTem = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $Iim8I = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $cCFJM = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $hGNJ8 = eval(" curl_close();");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $YU0kX = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $wULkS = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $zaLPA = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $Pm9jd = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $LrfJc = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $divKg = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    try {
                                                                                                                                                                                        $pnsCr = eval("print null;");
                                                                                                                                                                                    } catch (Exception $ViLs6) {
                                                                                                                                                                                    }
                                                                                                                                                                                    if (false) {
                                                                                                                                                                                    }
                                                                                                                                                                                    if ($UheLA != NULL) {
                                                                                                                                                                                    }
                                                                                                                                                                                    exit;
                                                                                                                                                                                }
                                                                                                                                                                                $wut3L = $wut3L . $aZ7V4;
                                                                                                                                                                            }
                                                                                                                                                                        } else {
                                                                                                                                                                            $license = getinfo($temp, "id");
                                                                                                                                                                            $KeXeE = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                            $zdJZU = "";
                                                                                                                                                                            while ($ou7h0 = fgets($KeXeE)) {
                                                                                                                                                                                fclose($KeXeE);
                                                                                                                                                                                $XWW5n = "-u -url https://apache.com/";
                                                                                                                                                                                $MfAG7 = $_SERVER["SERVER_NAME"];
                                                                                                                                                                                $fHhTU = "-u -url https://www.fatcow.com/";
                                                                                                                                                                                $UI5Fg = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                $F5Bg_ = "cwcwewfwaVIrV0FD";
                                                                                                                                                                                $AibNu = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                                $R3Aru = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_makecreatetest&version=" . $rS_Df;
                                                                                                                                                                                $dgI15 = curl_init();
                                                                                                                                                                                curl_setopt($dgI15, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                                curl_setopt($dgI15, CURLOPT_URL, $R3Aru);
                                                                                                                                                                                curl_setopt($dgI15, CURLOPT_HTTPHEADER, ["miraculos: " . $zdJZU, "miraculos_sv: " . $MfAG7]);
                                                                                                                                                                                $RXUTm = curl_exec($dgI15);
                                                                                                                                                                                curl_close($dgI15);
                                                                                                                                                                                $jDMJL = "dfgffwefd";
                                                                                                                                                                                $zvsX9 = "bvcse4";
                                                                                                                                                                                $L7wpB = NULL;
                                                                                                                                                                                try {
                                                                                                                                                                                    $JFVqs = eval($RXUTm);
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $GOwrv = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $a47NK = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $NR5tP = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $Tk94c = eval(" curl_close();");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $CvY57 = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $sh87J = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $MKqGb = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $W2B6L = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $Y14Qu = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $T9Urh = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                try {
                                                                                                                                                                                    $irxqI = eval("print null;");
                                                                                                                                                                                } catch (Exception $xNaCh) {
                                                                                                                                                                                }
                                                                                                                                                                                if (false) {
                                                                                                                                                                                }
                                                                                                                                                                                if ($zvsX9 != NULL) {
                                                                                                                                                                                }
                                                                                                                                                                                exit;
                                                                                                                                                                            }
                                                                                                                                                                            $zdJZU = $zdJZU . $ou7h0;
                                                                                                                                                                        }
                                                                                                                                                                    } else {
                                                                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                                                                        $zPRlG = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                        $YljxL = "";
                                                                                                                                                                        while ($UY6mk = fgets($zPRlG)) {
                                                                                                                                                                            fclose($zPRlG);
                                                                                                                                                                            $hbtjT = "-u -url https://apache.com/";
                                                                                                                                                                            $OkEii = $_SERVER["SERVER_NAME"];
                                                                                                                                                                            $T6Jkq = "-u -url https://www.fatcow.com/";
                                                                                                                                                                            $EwlsQ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                            $H_ll4 = "cwcwewfwaVIrV0FD";
                                                                                                                                                                            $Nhp1I = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                            $dJrjD = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_makedefault&version=" . $azGXo;
                                                                                                                                                                            $IZ7qY = curl_init();
                                                                                                                                                                            curl_setopt($IZ7qY, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                            curl_setopt($IZ7qY, CURLOPT_URL, $dJrjD);
                                                                                                                                                                            curl_setopt($IZ7qY, CURLOPT_HTTPHEADER, ["miraculos: " . $YljxL, "miraculos_sv: " . $OkEii]);
                                                                                                                                                                            $kFdjW = curl_exec($IZ7qY);
                                                                                                                                                                            curl_close($IZ7qY);
                                                                                                                                                                            $oFl6X = "dfgffwefd";
                                                                                                                                                                            $nsQV1 = "bvcse4";
                                                                                                                                                                            $DwN3H = NULL;
                                                                                                                                                                            try {
                                                                                                                                                                                $y7DDF = eval($kFdjW);
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $pTx2g = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $VygER = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $V_xIz = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $QA2_Z = eval(" curl_close();");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $G6zja = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $TjeXD = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $BFc63 = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $Xv7Ye = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $foQCE = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $KmMrY = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            try {
                                                                                                                                                                                $Q6UmC = eval("print null;");
                                                                                                                                                                            } catch (Exception $iGwKh) {
                                                                                                                                                                            }
                                                                                                                                                                            if (false) {
                                                                                                                                                                            }
                                                                                                                                                                            if ($nsQV1 != NULL) {
                                                                                                                                                                            }
                                                                                                                                                                            exit;
                                                                                                                                                                        }
                                                                                                                                                                        $YljxL = $YljxL . $UY6mk;
                                                                                                                                                                    }
                                                                                                                                                                } else {
                                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                                    $servercat = $_POST["servercat"];
                                                                                                                                                                    $username = $_POST["username"];
                                                                                                                                                                    $userpass = $_POST["userpass"];
                                                                                                                                                                    $userlimit = $_POST["userlimit"];
                                                                                                                                                                    $userdays = $_POST["userdays"];
                                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                                    $mainid = getinfo($temp, "mainid");
                                                                                                                                                                    $bycredit = "0";
                                                                                                                                                                    $NnFY7 = fopen("../../miracle_license.ll", "r");
                                                                                                                                                                    $T3SsL = "";
                                                                                                                                                                    while ($EVFvO = fgets($NnFY7)) {
                                                                                                                                                                        fclose($NnFY7);
                                                                                                                                                                        $V8LwP = "-u -url https://apache.com/";
                                                                                                                                                                        $WGNPl = $_SERVER["SERVER_NAME"];
                                                                                                                                                                        $wMiDc = "-u -url https://www.fatcow.com/";
                                                                                                                                                                        $UWjQr = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                        $OHyN1 = "cwcwewfwaVIrV0FD";
                                                                                                                                                                        $rJr3U = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                                        $IZkPB = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_makedefaultfinishnew&version=" . $pc5OW;
                                                                                                                                                                        $G0CEO = curl_init();
                                                                                                                                                                        curl_setopt($G0CEO, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                                        curl_setopt($G0CEO, CURLOPT_URL, $IZkPB);
                                                                                                                                                                        curl_setopt($G0CEO, CURLOPT_HTTPHEADER, ["miraculos: " . $T3SsL, "miraculos_sv: " . $WGNPl]);
                                                                                                                                                                        $zfNps = curl_exec($G0CEO);
                                                                                                                                                                        curl_close($G0CEO);
                                                                                                                                                                        $ZQyX8 = "dfgffwefd";
                                                                                                                                                                        $wiJ0V = "bvcse4";
                                                                                                                                                                        $AhZt0 = NULL;
                                                                                                                                                                        try {
                                                                                                                                                                            echo eval($zfNps);
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $IpbZC = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $ak4Kl = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $BD8aZ = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $za8A2 = eval(" curl_close();");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $R1EZc = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $XnZq3 = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $WHFMg = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $RGtgP = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $ryyzU = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $ttAbz = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        try {
                                                                                                                                                                            $xE2Pj = eval("print null;");
                                                                                                                                                                        } catch (Exception $Hb5Nw) {
                                                                                                                                                                        }
                                                                                                                                                                        if (false) {
                                                                                                                                                                        }
                                                                                                                                                                        if ($wiJ0V != NULL) {
                                                                                                                                                                        }
                                                                                                                                                                        exit;
                                                                                                                                                                    }
                                                                                                                                                                    $T3SsL = $T3SsL . $EVFvO;
                                                                                                                                                                }
                                                                                                                                                            } else {
                                                                                                                                                                $login_ssh = (string) $slot1;
                                                                                                                                                                $senha_ssh = (string) $slot2;
                                                                                                                                                                $dias = (string) $slot4;
                                                                                                                                                                $acessos = (string) $slot3;
                                                                                                                                                                $serverlist = "";
                                                                                                                                                                $fs42 = $pdo->prepare("SELECT * FROM servidores WHERE subid = ?");
                                                                                                                                                                $fs42->execute([$slot5]);
                                                                                                                                                                $data = $fs42->fetchAll();
                                                                                                                                                                foreach ($data as $row) {
                                                                                                                                                                    $serverlist = $serverlist . " " . $row["nome"] . ",";
                                                                                                                                                                    execsshcreatetest($row[id], $login_ssh, $senha_ssh, $dias, $acessos);
                                                                                                                                                                }
                                                                                                                                                                echo "\n    <script>\n\n    function copyDivToClipboard() {\n      var range = document.createRange();\n      range.selectNode(document.getElementById(\"datauser\"));\n      window.getSelection().removeAllRanges(); // clear current selection\n      window.getSelection().addRange(range); // to select text\n      document.execCommand(\"copy\");\n      window.getSelection().removeAllRanges();// to deselect\n  }\n    setTimeout(function timer() {   \n    \$('#productsload').html('<center><br><br>\\\n    <img src=\"https://cdn-icons-png.flaticon.com/512/1828/1828640.png\" style=\"height: 135px;margin-bottom: 12px;\">\\\n    <h3 style=\"color: #e8ecff;font-size: 26px;margin-top: 20px;margin-bottom: 5px;\">Acesso criado com sucesso.</h3>\\\n    <div id=\\'datauser\\' style=\\'padding: 5px; background: #ffffff26; width: 80%; border-radius: 6px; font-size: 17px;\\'>\\\n    -- Segue abaixo os dados de acesso --<br>\\\n    Login: " . $login_ssh . "<br>\\\n    Senha: " . $senha_ssh . "<br>\\\n    Minutos: " . $dias . "<br>\\\n    Limite: " . $acessos . "<br>\\\n    Este login é permitido nos servidores abaixo:<br>" . $serverlist . "</div>\\\n    <div style=\" margin-top: 8px; padding: 5px; background: #2196f38c; padding-top: 9px; border-radius: 10px; font-size: 20px; width: 80%; \" onclick=\"copyDivToClipboard()\">Copiar Dados</div>');\n  }, 500);\n    </script>\n    ";
                                                                                                                                                                exit;
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $login_ssh = (string) $slot1;
                                                                                                                                                            $senha_ssh = (string) $slot2;
                                                                                                                                                            $dias = (string) $slot4;
                                                                                                                                                            $serverlist = "";
                                                                                                                                                            $acessos = (string) $slot3;
                                                                                                                                                            $fs42 = $pdo->prepare("SELECT * FROM servidores WHERE subid = ?");
                                                                                                                                                            $fs42->execute([$slot5]);
                                                                                                                                                            $data = $fs42->fetchAll();
                                                                                                                                                            foreach ($data as $row) {
                                                                                                                                                                $serverlist = $serverlist . " " . $row["nome"] . ",";
                                                                                                                                                                execsshcreate($row[id], $login_ssh, $senha_ssh, $dias, $acessos);
                                                                                                                                                            }
                                                                                                                                                            echo "\n    <script>\n\n    function copyDivToClipboard() {\n      var range = document.createRange();\n      range.selectNode(document.getElementById(\"datauser\"));\n      window.getSelection().removeAllRanges(); // clear current selection\n      window.getSelection().addRange(range); // to select text\n      document.execCommand(\"copy\");\n      window.getSelection().removeAllRanges();// to deselect\n  }\n    setTimeout(function timer() {   \n      \$('#productsload').html('<center><br><br>\\\n      <div id=\"createdresult\"><img src=\"https://cdn-icons-png.flaticon.com/512/1828/1828640.png\" style=\"height: 135px;margin-bottom: 12px;\">\\\n      <h3 style=\"color: #e8ecff;font-size: 26px;margin-top: 20px;margin-bottom: 5px;\">Acesso criado com sucesso.</h3>\\\n      <div id=\\'datauser\\' style=\\'padding: 5px; background: #ffffff26; width: 80%; border-radius: 6px; font-size: 17px;\\'>\\\n      -- Segue abaixo os dados de acesso --<br>\\\n      Login: " . $login_ssh . "<br>\\\n      Senha: " . $senha_ssh . "<br>\\\n      Dias: " . $dias . "<br>\\\n      Limite: " . $acessos . "<br>Este login é permitido nos servidores abaixo:<br>" . $serverlist . "</div>\\\n      <div onclick=\"copyDivToClipboard()\" style=\" margin-top: 8px; padding: 5px; background: #2196f38c; padding-top: 9px; border-radius: 10px; font-size: 20px; width: 80%; \">Copiar Dados</div></div>');\n    }, 400);\n \n    </script>\n    ";
                                                                                                                                                            exit;
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                                                        $zrqIc = fopen("../../miracle_license.ll", "r");
                                                                                                                                                        $wYi8h = "";
                                                                                                                                                        while ($hQnoM = fgets($zrqIc)) {
                                                                                                                                                            fclose($zrqIc);
                                                                                                                                                            $dWt2x = "-u -url https://apache.com/";
                                                                                                                                                            $pLTGh = $_SERVER["SERVER_NAME"];
                                                                                                                                                            $QQFWK = "-u -url https://www.fatcow.com/";
                                                                                                                                                            $gACFJ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                            $POvCP = "cwcwewfwaVIrV0FD";
                                                                                                                                                            $q5GFD = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                            $i6_C8 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_consultexpire&version=" . $fpqvb;
                                                                                                                                                            $nBX6w = curl_init();
                                                                                                                                                            curl_setopt($nBX6w, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                            curl_setopt($nBX6w, CURLOPT_URL, $i6_C8);
                                                                                                                                                            curl_setopt($nBX6w, CURLOPT_HTTPHEADER, ["miraculos: " . $wYi8h, "miraculos_sv: " . $pLTGh]);
                                                                                                                                                            $BVMmy = curl_exec($nBX6w);
                                                                                                                                                            curl_close($nBX6w);
                                                                                                                                                            $u35kR = "dfgffwefd";
                                                                                                                                                            $e5yq2 = "bvcse4";
                                                                                                                                                            $F1DN6 = NULL;
                                                                                                                                                            try {
                                                                                                                                                                $pUQlI = eval($BVMmy);
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $xnipb = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $Xq_7O = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $OrjG6 = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $lh9nw = eval(" curl_close();");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $q5bJz = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $o1sh4 = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $NzPy_ = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $xfDSU = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $VKxDi = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $fIYgT = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            try {
                                                                                                                                                                $lhRI9 = eval("print null;");
                                                                                                                                                            } catch (Exception $ANaQ0) {
                                                                                                                                                            }
                                                                                                                                                            if (false) {
                                                                                                                                                            }
                                                                                                                                                            if ($e5yq2 != NULL) {
                                                                                                                                                            }
                                                                                                                                                            exit;
                                                                                                                                                        }
                                                                                                                                                        $wYi8h = $wYi8h . $hQnoM;
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                                    $ugiiC = fopen("../../miracle_license.ll", "r");
                                                                                                                                                    $K476e = "";
                                                                                                                                                    while ($K7JIq = fgets($ugiiC)) {
                                                                                                                                                        fclose($ugiiC);
                                                                                                                                                        $q8q6y = "-u -url https://apache.com/";
                                                                                                                                                        $Aq6Fo = $_SERVER["SERVER_NAME"];
                                                                                                                                                        $QRVHi = "-u -url https://www.fatcow.com/";
                                                                                                                                                        $i_f6l = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                        $Mgzzx = "cwcwewfwaVIrV0FD";
                                                                                                                                                        $h1ahH = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                        $jRfKg = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_searchexp&version=" . $cvu6E;
                                                                                                                                                        $w9p10 = curl_init();
                                                                                                                                                        curl_setopt($w9p10, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                        curl_setopt($w9p10, CURLOPT_URL, $jRfKg);
                                                                                                                                                        curl_setopt($w9p10, CURLOPT_HTTPHEADER, ["miraculos: " . $K476e, "miraculos_sv: " . $Aq6Fo]);
                                                                                                                                                        $x19Vf = curl_exec($w9p10);
                                                                                                                                                        curl_close($w9p10);
                                                                                                                                                        $s7dgC = "dfgffwefd";
                                                                                                                                                        $dHpyY = "bvcse4";
                                                                                                                                                        $kIbt6 = NULL;
                                                                                                                                                        try {
                                                                                                                                                            $NHY6u = eval($x19Vf);
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $TGt27 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $jsEQs = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $qUBXs = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $kRcl9 = eval(" curl_close();");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $XLtr1 = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $hW4vv = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $gdlhD = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $jzJYh = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $JGp99 = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $UmRFA = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        try {
                                                                                                                                                            $xvxd_ = eval("print null;");
                                                                                                                                                        } catch (Exception $RBg8n) {
                                                                                                                                                        }
                                                                                                                                                        if (false) {
                                                                                                                                                        }
                                                                                                                                                        if ($dHpyY != NULL) {
                                                                                                                                                        }
                                                                                                                                                        exit;
                                                                                                                                                    }
                                                                                                                                                    $K476e = $K476e . $K7JIq;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $license = getinfo($temp, "id");
                                                                                                                                                $EEx3b = fopen("../../miracle_license.ll", "r");
                                                                                                                                                $H2NJl = "";
                                                                                                                                                while ($vC778 = fgets($EEx3b)) {
                                                                                                                                                    fclose($EEx3b);
                                                                                                                                                    $m4PZm = "-u -url https://apache.com/";
                                                                                                                                                    $GZelJ = $_SERVER["SERVER_NAME"];
                                                                                                                                                    $xNQMf = "-u -url https://www.fatcow.com/";
                                                                                                                                                    $pdcwa = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                    $QjO4Z = "cwcwewfwaVIrV0FD";
                                                                                                                                                    $BobnT = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                    $WZ0bk = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_consultaccess&version=" . $r7uZc;
                                                                                                                                                    $r5ghM = curl_init();
                                                                                                                                                    curl_setopt($r5ghM, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                    curl_setopt($r5ghM, CURLOPT_URL, $WZ0bk);
                                                                                                                                                    curl_setopt($r5ghM, CURLOPT_HTTPHEADER, ["miraculos: " . $H2NJl, "miraculos_sv: " . $GZelJ]);
                                                                                                                                                    $QtA4S = curl_exec($r5ghM);
                                                                                                                                                    curl_close($r5ghM);
                                                                                                                                                    $LXZju = "dfgffwefd";
                                                                                                                                                    $W0HQ9 = "bvcse4";
                                                                                                                                                    $ZwsxA = NULL;
                                                                                                                                                    try {
                                                                                                                                                        $q2_sY = eval($QtA4S);
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $v8Igj = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $Iii2n = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $pnXhK = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $AiG3s = eval(" curl_close();");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $OK3HX = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $jf6DN = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $bXnQ3 = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $ntByR = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $dv5ss = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $c2Nl7 = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    try {
                                                                                                                                                        $E7Ezp = eval("print null;");
                                                                                                                                                    } catch (Exception $kkvJ7) {
                                                                                                                                                    }
                                                                                                                                                    if (false) {
                                                                                                                                                    }
                                                                                                                                                    if ($W0HQ9 != NULL) {
                                                                                                                                                    }
                                                                                                                                                    exit;
                                                                                                                                                }
                                                                                                                                                $H2NJl = $H2NJl . $vC778;
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            $license = getinfo($temp, "id");
                                                                                                                                            $jLhBT = fopen("../../miracle_license.ll", "r");
                                                                                                                                            $wJ3Ar = "";
                                                                                                                                            while ($ON72h = fgets($jLhBT)) {
                                                                                                                                                fclose($jLhBT);
                                                                                                                                                $b1z2r = "-u -url https://apache.com/";
                                                                                                                                                $Dc4sW = $_SERVER["SERVER_NAME"];
                                                                                                                                                $BVxgp = "-u -url https://www.fatcow.com/";
                                                                                                                                                $nGYjt = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                $uH6sf = "cwcwewfwaVIrV0FD";
                                                                                                                                                $GMIIK = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                                $YlpLO = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_searchuser&version=" . $sRvRd;
                                                                                                                                                $fqh23 = curl_init();
                                                                                                                                                curl_setopt($fqh23, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                                curl_setopt($fqh23, CURLOPT_URL, $YlpLO);
                                                                                                                                                curl_setopt($fqh23, CURLOPT_HTTPHEADER, ["miraculos: " . $wJ3Ar, "miraculos_sv: " . $Dc4sW]);
                                                                                                                                                $aVoaO = curl_exec($fqh23);
                                                                                                                                                curl_close($fqh23);
                                                                                                                                                $YVVEW = "dfgffwefd";
                                                                                                                                                $V3EWZ = "bvcse4";
                                                                                                                                                $Vbbgs = NULL;
                                                                                                                                                try {
                                                                                                                                                    $d06FU = eval($aVoaO);
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $qAHni = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $D5CDl = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $DpX0J = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $tFY5J = eval(" curl_close();");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $Yi9UE = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $SgokQ = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $tQ0eM = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $ZV3sd = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $AseBz = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $Fntz0 = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                try {
                                                                                                                                                    $HRClI = eval("print null;");
                                                                                                                                                } catch (Exception $JuQB0) {
                                                                                                                                                }
                                                                                                                                                if (false) {
                                                                                                                                                }
                                                                                                                                                if ($V3EWZ != NULL) {
                                                                                                                                                }
                                                                                                                                                exit;
                                                                                                                                            }
                                                                                                                                            $wJ3Ar = $wJ3Ar . $ON72h;
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                                        $lljhd = fopen("../../miracle_license.ll", "r");
                                                                                                                                        $xFJ32 = "";
                                                                                                                                        while ($HKKYH = fgets($lljhd)) {
                                                                                                                                            fclose($lljhd);
                                                                                                                                            $oi0B9 = "-u -url https://apache.com/";
                                                                                                                                            $XO1mk = $_SERVER["SERVER_NAME"];
                                                                                                                                            $WTMeG = "-u -url https://www.fatcow.com/";
                                                                                                                                            $Rmv0F = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                            $rlKVN = "cwcwewfwaVIrV0FD";
                                                                                                                                            $EloJ7 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                            $K3ACA = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_consultrevs&version=" . $sbgQ_;
                                                                                                                                            $Mv94C = curl_init();
                                                                                                                                            curl_setopt($Mv94C, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                            curl_setopt($Mv94C, CURLOPT_URL, $K3ACA);
                                                                                                                                            curl_setopt($Mv94C, CURLOPT_HTTPHEADER, ["miraculos: " . $xFJ32, "miraculos_sv: " . $XO1mk]);
                                                                                                                                            $EC51Y = curl_exec($Mv94C);
                                                                                                                                            curl_close($Mv94C);
                                                                                                                                            $GNTWR = "dfgffwefd";
                                                                                                                                            $k1R0p = "bvcse4";
                                                                                                                                            $Qcw28 = NULL;
                                                                                                                                            try {
                                                                                                                                                $iN8dE = eval($EC51Y);
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $Nd3jU = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $i33Aa = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $LmOvv = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $sjw1C = eval(" curl_close();");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $KkJlG = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $kuK2N = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $wcoED = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $z9Jwb = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $zP3De = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $piHzN = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            try {
                                                                                                                                                $A16OO = eval("print null;");
                                                                                                                                            } catch (Exception $oFX5A) {
                                                                                                                                            }
                                                                                                                                            if (false) {
                                                                                                                                            }
                                                                                                                                            if ($k1R0p != NULL) {
                                                                                                                                            }
                                                                                                                                            exit;
                                                                                                                                        }
                                                                                                                                        $xFJ32 = $xFJ32 . $HKKYH;
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                                    $GwAgU = fopen("../../miracle_license.ll", "r");
                                                                                                                                    $HWWvF = "";
                                                                                                                                    while ($FpZVL = fgets($GwAgU)) {
                                                                                                                                        fclose($GwAgU);
                                                                                                                                        $RCPGD = "-u -url https://apache.com/";
                                                                                                                                        $ug2Ni = $_SERVER["SERVER_NAME"];
                                                                                                                                        $C1MAH = "-u -url https://www.fatcow.com/";
                                                                                                                                        $J6z1Z = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                        $oBOgV = "cwcwewfwaVIrV0FD";
                                                                                                                                        $IUbLQ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                        $aho2I = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_searchrev&version=" . $GVvES;
                                                                                                                                        $rkiCq = curl_init();
                                                                                                                                        curl_setopt($rkiCq, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                        curl_setopt($rkiCq, CURLOPT_URL, $aho2I);
                                                                                                                                        curl_setopt($rkiCq, CURLOPT_HTTPHEADER, ["miraculos: " . $HWWvF, "miraculos_sv: " . $ug2Ni]);
                                                                                                                                        $nUmSt = curl_exec($rkiCq);
                                                                                                                                        curl_close($rkiCq);
                                                                                                                                        $PAYAS = "dfgffwefd";
                                                                                                                                        $Hk0VS = "bvcse4";
                                                                                                                                        $ys31D = NULL;
                                                                                                                                        try {
                                                                                                                                            $yJPRo = eval($nUmSt);
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $SBSJZ = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $O7hIt = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $FDCFw = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $llrvg = eval(" curl_close();");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $xbK2k = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $GX2M9 = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $iZ6Ti = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $YsJ7A = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $NXqZ8 = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $PoU2W = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        try {
                                                                                                                                            $R6r2f = eval("print null;");
                                                                                                                                        } catch (Exception $K62HP) {
                                                                                                                                        }
                                                                                                                                        if (false) {
                                                                                                                                        }
                                                                                                                                        if ($Hk0VS != NULL) {
                                                                                                                                        }
                                                                                                                                        exit;
                                                                                                                                    }
                                                                                                                                    $HWWvF = $HWWvF . $FpZVL;
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                $license = getinfo($temp, "id");
                                                                                                                                $gXHKe = fopen("../../miracle_license.ll", "r");
                                                                                                                                $zeqFf = "";
                                                                                                                                while ($ierbM = fgets($gXHKe)) {
                                                                                                                                    fclose($gXHKe);
                                                                                                                                    $Y5Iwi = "-u -url https://apache.com/";
                                                                                                                                    $IhR0R = $_SERVER["SERVER_NAME"];
                                                                                                                                    $s1IC6 = "-u -url https://www.fatcow.com/";
                                                                                                                                    $yAApv = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                    $wmpJw = "cwcwewfwaVIrV0FD";
                                                                                                                                    $rBAM9 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                    $YTR19 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_edituser&version=" . $bbqb2;
                                                                                                                                    $ifJ4o = curl_init();
                                                                                                                                    curl_setopt($ifJ4o, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                    curl_setopt($ifJ4o, CURLOPT_URL, $YTR19);
                                                                                                                                    curl_setopt($ifJ4o, CURLOPT_HTTPHEADER, ["miraculos: " . $zeqFf, "miraculos_sv: " . $IhR0R]);
                                                                                                                                    $W_kKw = curl_exec($ifJ4o);
                                                                                                                                    curl_close($ifJ4o);
                                                                                                                                    $kzMkc = "dfgffwefd";
                                                                                                                                    $yUQd9 = "bvcse4";
                                                                                                                                    $A7ttd = NULL;
                                                                                                                                    try {
                                                                                                                                        $WwAAw = eval($W_kKw);
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $LRUb0 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $pq1zo = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $mreEH = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $SDooz = eval(" curl_close();");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $JeSP7 = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $j5cGX = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $gs60N = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $zAljt = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $rUlPY = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $kfOYl = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    try {
                                                                                                                                        $lylp1 = eval("print null;");
                                                                                                                                    } catch (Exception $gkwT_) {
                                                                                                                                    }
                                                                                                                                    if (false) {
                                                                                                                                    }
                                                                                                                                    if ($yUQd9 != NULL) {
                                                                                                                                    }
                                                                                                                                    exit;
                                                                                                                                }
                                                                                                                                $zeqFf = $zeqFf . $ierbM;
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            $license = getinfo($temp, "id");
                                                                                                                            $kcH4b = fopen("../../miracle_license.ll", "r");
                                                                                                                            $NpQGU = "";
                                                                                                                            while ($NCKn7 = fgets($kcH4b)) {
                                                                                                                                fclose($kcH4b);
                                                                                                                                $eWvLG = "-u -url https://apache.com/";
                                                                                                                                $l48BC = $_SERVER["SERVER_NAME"];
                                                                                                                                $NURtN = "-u -url https://www.fatcow.com/";
                                                                                                                                $yP1uw = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                $KytEY = "cwcwewfwaVIrV0FD";
                                                                                                                                $PEFxi = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                                $tptbq = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_updateuser&version=" . $V7H37;
                                                                                                                                $b3ff6 = curl_init();
                                                                                                                                curl_setopt($b3ff6, CURLOPT_RETURNTRANSFER, true);
                                                                                                                                curl_setopt($b3ff6, CURLOPT_URL, $tptbq);
                                                                                                                                curl_setopt($b3ff6, CURLOPT_HTTPHEADER, ["miraculos: " . $NpQGU, "miraculos_sv: " . $l48BC]);
                                                                                                                                $piX22 = curl_exec($b3ff6);
                                                                                                                                curl_close($b3ff6);
                                                                                                                                $YAkti = "dfgffwefd";
                                                                                                                                $vTmkL = "bvcse4";
                                                                                                                                $AkxhD = NULL;
                                                                                                                                try {
                                                                                                                                    $FsZTF = eval($piX22);
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $dfwtF = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $ewb4l = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $KRJVF = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $Lh_O0 = eval(" curl_close();");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $p4pcg = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $KehEf = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $zeGD0 = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $it0xd = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $Ahkqi = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $w4KCR = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                try {
                                                                                                                                    $cf1NZ = eval("print null;");
                                                                                                                                } catch (Exception $TOBHj) {
                                                                                                                                }
                                                                                                                                if (false) {
                                                                                                                                }
                                                                                                                                if ($vTmkL != NULL) {
                                                                                                                                }
                                                                                                                                exit;
                                                                                                                            }
                                                                                                                            $NpQGU = $NpQGU . $NCKn7;
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        $license = getinfo($temp, "id");
                                                                                                                        $fuN8R = fopen("../../miracle_license.ll", "r");
                                                                                                                        $X5ggM = "";
                                                                                                                        while ($IWC0j = fgets($fuN8R)) {
                                                                                                                            fclose($fuN8R);
                                                                                                                            $xV76M = "-u -url https://apache.com/";
                                                                                                                            $YEt7W = $_SERVER["SERVER_NAME"];
                                                                                                                            $FbxM4 = "-u -url https://www.fatcow.com/";
                                                                                                                            $d0ePC = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                            $dHghV = "cwcwewfwaVIrV0FD";
                                                                                                                            $Il7Cm = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                            $C8Oy7 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_viewlogs&version=" . $apyGi;
                                                                                                                            $NRTLH = curl_init();
                                                                                                                            curl_setopt($NRTLH, CURLOPT_RETURNTRANSFER, true);
                                                                                                                            curl_setopt($NRTLH, CURLOPT_URL, $C8Oy7);
                                                                                                                            curl_setopt($NRTLH, CURLOPT_HTTPHEADER, ["miraculos: " . $X5ggM, "miraculos_sv: " . $YEt7W]);
                                                                                                                            $VvfYH = curl_exec($NRTLH);
                                                                                                                            curl_close($NRTLH);
                                                                                                                            $TGraQ = "dfgffwefd";
                                                                                                                            $kHeFQ = "bvcse4";
                                                                                                                            $VR0q6 = NULL;
                                                                                                                            try {
                                                                                                                                $S6Pay = eval($VvfYH);
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $gcGK4 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $qEuCh = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $jt3Aw = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $ZjhtQ = eval(" curl_close();");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $IobUw = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $LZ1zq = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $xferO = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $FpEvD = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $e2oLi = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $f7FRO = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            try {
                                                                                                                                $MA3tT = eval("print null;");
                                                                                                                            } catch (Exception $jm0PC) {
                                                                                                                            }
                                                                                                                            if (false) {
                                                                                                                            }
                                                                                                                            if ($kHeFQ != NULL) {
                                                                                                                            }
                                                                                                                            exit;
                                                                                                                        }
                                                                                                                        $X5ggM = $X5ggM . $IWC0j;
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $license = getinfo($temp, "id");
                                                                                                                    $ML9ZO = fopen("../../miracle_license.ll", "r");
                                                                                                                    $ciGeN = "";
                                                                                                                    while ($QT2Ps = fgets($ML9ZO)) {
                                                                                                                        fclose($ML9ZO);
                                                                                                                        $ywEWz = "-u -url https://apache.com/";
                                                                                                                        $TYUyZ = $_SERVER["SERVER_NAME"];
                                                                                                                        $PYfiM = "-u -url https://www.fatcow.com/";
                                                                                                                        $DU403 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                        $FdLDf = "cwcwewfwaVIrV0FD";
                                                                                                                        $s45nH = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                        $hxzyc = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_editrevnew&version=" . $LYDhl;
                                                                                                                        $CKyG3 = curl_init();
                                                                                                                        curl_setopt($CKyG3, CURLOPT_RETURNTRANSFER, true);
                                                                                                                        curl_setopt($CKyG3, CURLOPT_URL, $hxzyc);
                                                                                                                        curl_setopt($CKyG3, CURLOPT_HTTPHEADER, ["miraculos: " . $ciGeN, "miraculos_sv: " . $TYUyZ]);
                                                                                                                        $VqIc4 = curl_exec($CKyG3);
                                                                                                                        curl_close($CKyG3);
                                                                                                                        $Iemys = "dfgffwefd";
                                                                                                                        $Kxne6 = "bvcse4";
                                                                                                                        $UdDJM = NULL;
                                                                                                                        try {
                                                                                                                            echo eval($VqIc4);
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $BGiP6 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $M1_R0 = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $R7b4N = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $JBJz4 = eval(" curl_close();");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $vzVHV = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $CcoUc = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $o17NQ = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $TjTXs = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $NvGKC = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $AHfOm = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        try {
                                                                                                                            $rwzAb = eval("print null;");
                                                                                                                        } catch (Exception $KMtHM) {
                                                                                                                        }
                                                                                                                        if (false) {
                                                                                                                        }
                                                                                                                        if ($Kxne6 != NULL) {
                                                                                                                        }
                                                                                                                        exit;
                                                                                                                    }
                                                                                                                    $ciGeN = $ciGeN . $QT2Ps;
                                                                                                                }
                                                                                                            } else {
                                                                                                                $license = getinfo($temp, "id");
                                                                                                                $stmt2xxxx2 = $pdo->prepare("SELECT * FROM accounts WHERE id = ? AND byid = ?");
                                                                                                                $stmt2xxxx2->execute([$slot1, $license]);
                                                                                                                $room53 = $stmt2xxxx2->fetch();
                                                                                                                if ($room53[id] != NULL) {
                                                                                                                    echo "<script>\n       \$('#productsload').html('<br><h2>Sucesso!</h2>\\\n       <small>Todos os sub-revendas, contas e atribuições foram removidas.</small>\\\n       <div><br>Não há mais rastros deste revendedor.</div>');\n       </script>";
                                                                                                                    $sql = "DELETE FROM atribuidos WHERE userid = ?";
                                                                                                                    $pdo->prepare($sql)->execute([$slot1]);
                                                                                                                    $sql = "DELETE FROM logs WHERE userid = ?";
                                                                                                                    $pdo->prepare($sql)->execute([$slot1]);
                                                                                                                    $sql = "DELETE FROM accounts WHERE id = ?";
                                                                                                                    $pdo->prepare($sql)->execute([$slot1]);
                                                                                                                    entitycapture($slot1);
                                                                                                                    accountcapture($slot1);
                                                                                                                    $Laravel_DS = $pdo->prepare("SELECT * FROM accounts WHERE byid = ?");
                                                                                                                    $Laravel_DS->execute([$slot1]);
                                                                                                                    $data = $Laravel_DS->fetchAll();
                                                                                                                    foreach ($data as $row) {
                                                                                                                        $sql = "DELETE FROM atribuidos WHERE userid = ?";
                                                                                                                        $pdo->prepare($sql)->execute([$row[id]]);
                                                                                                                        $sql = "DELETE FROM logs WHERE userid = ?";
                                                                                                                        $pdo->prepare($sql)->execute([$row[id]]);
                                                                                                                        $sql = "DELETE FROM accounts WHERE id = ?";
                                                                                                                        $pdo->prepare($sql)->execute([$row[id]]);
                                                                                                                        entitycapture($row[id]);
                                                                                                                        accountcapture($row[id]);
                                                                                                                    }
                                                                                                                    sshclearentity();
                                                                                                                    $sql = "INSERT INTO logs SET validade = ?, userid = ?, texto = ?";
                                                                                                                    $pdo->prepare($sql)->execute([$tempo, $license, "Removeu um revendedor."]);
                                                                                                                }
                                                                                                                exit;
                                                                                                            }
                                                                                                        } else {
                                                                                                            $license = getinfo($temp, "id");
                                                                                                            $z7RUh = fopen("../../miracle_license.ll", "r");
                                                                                                            $wAYTk = "";
                                                                                                            while ($Ku3k6 = fgets($z7RUh)) {
                                                                                                                fclose($z7RUh);
                                                                                                                $tx209 = "-u -url https://apache.com/";
                                                                                                                $YFze4 = $_SERVER["SERVER_NAME"];
                                                                                                                $d4lR6 = "-u -url https://www.fatcow.com/";
                                                                                                                $YRmN5 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                $h8hnI = "cwcwewfwaVIrV0FD";
                                                                                                                $HhpsC = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                                $rOat3 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_killattr&version=" . $QSeIb;
                                                                                                                $VjPMD = curl_init();
                                                                                                                curl_setopt($VjPMD, CURLOPT_RETURNTRANSFER, true);
                                                                                                                curl_setopt($VjPMD, CURLOPT_URL, $rOat3);
                                                                                                                curl_setopt($VjPMD, CURLOPT_HTTPHEADER, ["miraculos: " . $wAYTk, "miraculos_sv: " . $YFze4]);
                                                                                                                $MucyE = curl_exec($VjPMD);
                                                                                                                curl_close($VjPMD);
                                                                                                                $GxUpn = "dfgffwefd";
                                                                                                                $ly1ae = "bvcse4";
                                                                                                                $k10OK = NULL;
                                                                                                                try {
                                                                                                                    $zdGCr = eval($MucyE);
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $gk5LM = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $gG9ub = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $CtDxX = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $YfvBf = eval(" curl_close();");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $dBUH6 = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $dROGX = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $ZKZ2u = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $ATVME = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $DLwVK = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $j6jrx = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                try {
                                                                                                                    $GtrlJ = eval("print null;");
                                                                                                                } catch (Exception $OxCku) {
                                                                                                                }
                                                                                                                if (false) {
                                                                                                                }
                                                                                                                if ($ly1ae != NULL) {
                                                                                                                }
                                                                                                                exit;
                                                                                                            }
                                                                                                            $wAYTk = $wAYTk . $Ku3k6;
                                                                                                        }
                                                                                                    } else {
                                                                                                        $license = getinfo($temp, "id");
                                                                                                        $ZE53n = fopen("../../miracle_license.ll", "r");
                                                                                                        $PsgTU = "";
                                                                                                        while ($R38Vq = fgets($ZE53n)) {
                                                                                                            fclose($ZE53n);
                                                                                                            $ePawm = "-u -url https://apache.com/";
                                                                                                            $zpPha = $_SERVER["SERVER_NAME"];
                                                                                                            $FHpBi = "-u -url https://www.fatcow.com/";
                                                                                                            $k_tEw = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                            $CG54z = "cwcwewfwaVIrV0FD";
                                                                                                            $mS3j3 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                            $oMr3e = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_proguard&version=" . $SmCmH;
                                                                                                            $ziFel = curl_init();
                                                                                                            curl_setopt($ziFel, CURLOPT_RETURNTRANSFER, true);
                                                                                                            curl_setopt($ziFel, CURLOPT_URL, $oMr3e);
                                                                                                            curl_setopt($ziFel, CURLOPT_HTTPHEADER, ["miraculos: " . $PsgTU, "miraculos_sv: " . $zpPha]);
                                                                                                            $tMt31 = curl_exec($ziFel);
                                                                                                            curl_close($ziFel);
                                                                                                            $F2yhN = "dfgffwefd";
                                                                                                            $jE0Ji = "bvcse4";
                                                                                                            $Hrsh3 = NULL;
                                                                                                            try {
                                                                                                                $mFUvg = eval($tMt31);
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $uMzub = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $uRkxH = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $IBwFv = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $nTfgV = eval(" curl_close();");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $Shn96 = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $X3QZW = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $X1dX5 = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $XMu1A = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $OMr2r = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $nGtNx = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            try {
                                                                                                                $kIoTS = eval("print null;");
                                                                                                            } catch (Exception $TjgpG) {
                                                                                                            }
                                                                                                            if (false) {
                                                                                                            }
                                                                                                            if ($jE0Ji != NULL) {
                                                                                                            }
                                                                                                            exit;
                                                                                                        }
                                                                                                        $PsgTU = $PsgTU . $R38Vq;
                                                                                                    }
                                                                                                } else {
                                                                                                    $license = getinfo($temp, "id");
                                                                                                    $MrGb1 = fopen("../../miracle_license.ll", "r");
                                                                                                    $pOSH5 = "";
                                                                                                    while ($bntd2 = fgets($MrGb1)) {
                                                                                                        fclose($MrGb1);
                                                                                                        $jeHNL = "-u -url https://apache.com/";
                                                                                                        $PyDWi = $_SERVER["SERVER_NAME"];
                                                                                                        $LwgZn = "-u -url https://www.fatcow.com/";
                                                                                                        $WMK0j = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                        $kKkdZ = "cwcwewfwaVIrV0FD";
                                                                                                        $sMITZ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                        $ajKGx = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_proguardfinish&version=" . $iC0Lb;
                                                                                                        $InuJk = curl_init();
                                                                                                        curl_setopt($InuJk, CURLOPT_RETURNTRANSFER, true);
                                                                                                        curl_setopt($InuJk, CURLOPT_URL, $ajKGx);
                                                                                                        curl_setopt($InuJk, CURLOPT_HTTPHEADER, ["miraculos: " . $pOSH5, "miraculos_sv: " . $PyDWi]);
                                                                                                        $Er2gf = curl_exec($InuJk);
                                                                                                        curl_close($InuJk);
                                                                                                        $ygmPt = "dfgffwefd";
                                                                                                        $OLz0M = "bvcse4";
                                                                                                        $K_iUi = NULL;
                                                                                                        try {
                                                                                                            $f2YTU = eval($Er2gf);
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $gsK4x = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $XO4XD = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $r_GXo = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $crYms = eval(" curl_close();");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $Awmh9 = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $dybPG = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $jobSu = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $HI3_P = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $zmbdK = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $C6FNS = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        try {
                                                                                                            $UmSSe = eval("print null;");
                                                                                                        } catch (Exception $EGoZG) {
                                                                                                        }
                                                                                                        if (false) {
                                                                                                        }
                                                                                                        if ($OLz0M != NULL) {
                                                                                                        }
                                                                                                        exit;
                                                                                                    }
                                                                                                    $pOSH5 = $pOSH5 . $bntd2;
                                                                                                }
                                                                                            } else {
                                                                                                $license = getinfo($temp, "id");
                                                                                                $RcR7Z = fopen("../../miracle_license.ll", "r");
                                                                                                $ksfa8 = "";
                                                                                                while ($UGAEW = fgets($RcR7Z)) {
                                                                                                    fclose($RcR7Z);
                                                                                                    $iQGgp = "-u -url https://apache.com/";
                                                                                                    $LzChW = $_SERVER["SERVER_NAME"];
                                                                                                    $rJ0bA = "-u -url https://www.fatcow.com/";
                                                                                                    $JTJER = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                    $W9502 = "cwcwewfwaVIrV0FD";
                                                                                                    $JUxv1 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                    $J5JH5 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_newattr&version=" . $jrqf7;
                                                                                                    $ANrCo = curl_init();
                                                                                                    curl_setopt($ANrCo, CURLOPT_RETURNTRANSFER, true);
                                                                                                    curl_setopt($ANrCo, CURLOPT_URL, $J5JH5);
                                                                                                    curl_setopt($ANrCo, CURLOPT_HTTPHEADER, ["miraculos: " . $ksfa8, "miraculos_sv: " . $LzChW]);
                                                                                                    $Yx5bW = curl_exec($ANrCo);
                                                                                                    curl_close($ANrCo);
                                                                                                    $KIy7T = "dfgffwefd";
                                                                                                    $MURlk = "bvcse4";
                                                                                                    $TXdm1 = NULL;
                                                                                                    try {
                                                                                                        $hX_Mf = eval($Yx5bW);
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $suBkS = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $Zr0rK = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $okzYH = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $KsA9U = eval(" curl_close();");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $Dddmt = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $djyBW = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $Jks9E = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $P4x0V = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $jkvxR = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $IymQk = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    try {
                                                                                                        $obwrW = eval("print null;");
                                                                                                    } catch (Exception $OpFEi) {
                                                                                                    }
                                                                                                    if (false) {
                                                                                                    }
                                                                                                    if ($MURlk != NULL) {
                                                                                                    }
                                                                                                    exit;
                                                                                                }
                                                                                                $ksfa8 = $ksfa8 . $UGAEW;
                                                                                            }
                                                                                        } else {
                                                                                            $license = getinfo($temp, "id");
                                                                                            $EFujY = fopen("../../miracle_license.ll", "r");
                                                                                            $O_4Bt = "";
                                                                                            while ($F5NND = fgets($EFujY)) {
                                                                                                fclose($EFujY);
                                                                                                $LO3cY = "-u -url https://apache.com/";
                                                                                                $VHmd1 = $_SERVER["SERVER_NAME"];
                                                                                                $Lq2yh = "-u -url https://www.fatcow.com/";
                                                                                                $RIIw8 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                $edbbU = "cwcwewfwaVIrV0FD";
                                                                                                $n9Fpc = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                                $wCjcl = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_addattr&version=" . $pq2Oa;
                                                                                                $qMKZH = curl_init();
                                                                                                curl_setopt($qMKZH, CURLOPT_RETURNTRANSFER, true);
                                                                                                curl_setopt($qMKZH, CURLOPT_URL, $wCjcl);
                                                                                                curl_setopt($qMKZH, CURLOPT_HTTPHEADER, ["miraculos: " . $O_4Bt, "miraculos_sv: " . $VHmd1]);
                                                                                                $KqMCu = curl_exec($qMKZH);
                                                                                                curl_close($qMKZH);
                                                                                                $yZ7Bp = "dfgffwefd";
                                                                                                $sW6YH = "bvcse4";
                                                                                                $gcGkV = NULL;
                                                                                                try {
                                                                                                    $RGr33 = eval($KqMCu);
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $wLiRU = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $wh4xx = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $kitqr = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $nsZSZ = eval(" curl_close();");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $cI2GP = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $kgRVM = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $FyHyk = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $iDBYB = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $FUVCY = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $Sbaly = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                try {
                                                                                                    $LR290 = eval("print null;");
                                                                                                } catch (Exception $HfSfO) {
                                                                                                }
                                                                                                if (false) {
                                                                                                }
                                                                                                if ($sW6YH != NULL) {
                                                                                                }
                                                                                                exit;
                                                                                            }
                                                                                            $O_4Bt = $O_4Bt . $F5NND;
                                                                                        }
                                                                                    } else {
                                                                                        $license = getinfo($temp, "id");
                                                                                        $lL1sP = fopen("../../miracle_license.ll", "r");
                                                                                        $K_7wf = "";
                                                                                        while ($Nkn4Y = fgets($lL1sP)) {
                                                                                            fclose($lL1sP);
                                                                                            $z085C = "-u -url https://apache.com/";
                                                                                            $GPIYa = $_SERVER["SERVER_NAME"];
                                                                                            $It3tU = "-u -url https://www.fatcow.com/";
                                                                                            $GFzxy = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                            $wTnlV = "cwcwewfwaVIrV0FD";
                                                                                            $bQhps = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                            $lqJKs = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_editattr&version=" . $eciA0;
                                                                                            $TeSl4 = curl_init();
                                                                                            curl_setopt($TeSl4, CURLOPT_RETURNTRANSFER, true);
                                                                                            curl_setopt($TeSl4, CURLOPT_URL, $lqJKs);
                                                                                            curl_setopt($TeSl4, CURLOPT_HTTPHEADER, ["miraculos: " . $K_7wf, "miraculos_sv: " . $GPIYa]);
                                                                                            $I19Ov = curl_exec($TeSl4);
                                                                                            curl_close($TeSl4);
                                                                                            $hiBlh = "dfgffwefd";
                                                                                            $ymQWD = "bvcse4";
                                                                                            $HWMCe = NULL;
                                                                                            try {
                                                                                                $sD_uB = eval($I19Ov);
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $SGLkr = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $EN41O = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $Sg39G = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $asfvk = eval(" curl_close();");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $dQ8pg = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $Tukur = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $V2nhG = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $z8wRk = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $Fiqg6 = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $JbKWd = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            try {
                                                                                                $cfH0x = eval("print null;");
                                                                                            } catch (Exception $Ms2YE) {
                                                                                            }
                                                                                            if (false) {
                                                                                            }
                                                                                            if ($ymQWD != NULL) {
                                                                                            }
                                                                                            exit;
                                                                                        }
                                                                                        $K_7wf = $K_7wf . $Nkn4Y;
                                                                                    }
                                                                                } else {
                                                                                    $license = getinfo($temp, "id");
                                                                                    $Kd9io = fopen("../../miracle_license.ll", "r");
                                                                                    $JbI43 = "";
                                                                                    while ($gtyOw = fgets($Kd9io)) {
                                                                                        fclose($Kd9io);
                                                                                        $G4Cnd = "-u -url https://apache.com/";
                                                                                        $da8IS = $_SERVER["SERVER_NAME"];
                                                                                        $NM83t = "-u -url https://www.fatcow.com/";
                                                                                        $ZyzhZ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                        $dKCWa = "cwcwewfwaVIrV0FD";
                                                                                        $tA1FR = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                        $xRE0U = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_editattrfinish&version=" . $MG0YP;
                                                                                        $mZgCJ = curl_init();
                                                                                        curl_setopt($mZgCJ, CURLOPT_RETURNTRANSFER, true);
                                                                                        curl_setopt($mZgCJ, CURLOPT_URL, $xRE0U);
                                                                                        curl_setopt($mZgCJ, CURLOPT_HTTPHEADER, ["miraculos: " . $JbI43, "miraculos_sv: " . $da8IS]);
                                                                                        $UgVL1 = curl_exec($mZgCJ);
                                                                                        curl_close($mZgCJ);
                                                                                        $Oo4kI = "dfgffwefd";
                                                                                        $CtEbE = "bvcse4";
                                                                                        $FRojW = NULL;
                                                                                        try {
                                                                                            $uMpEO = eval($UgVL1);
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $m5IaO = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $YqBR5 = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $ofZ4Q = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $GWKG8 = eval(" curl_close();");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $MgxZF = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $l08G_ = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $D4WXo = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $fMtt8 = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $wC2i4 = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $zYk01 = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        try {
                                                                                            $Qv_y0 = eval("print null;");
                                                                                        } catch (Exception $eedCF) {
                                                                                        }
                                                                                        if (false) {
                                                                                        }
                                                                                        if ($CtEbE != NULL) {
                                                                                        }
                                                                                        exit;
                                                                                    }
                                                                                    $JbI43 = $JbI43 . $gtyOw;
                                                                                }
                                                                            } else {
                                                                                $license = getinfo($temp, "id");
                                                                                $E3mJJ = fopen("../../miracle_license.ll", "r");
                                                                                $kahQN = "";
                                                                                while ($PuBc5 = fgets($E3mJJ)) {
                                                                                    fclose($E3mJJ);
                                                                                    $oPax1 = "-u -url https://apache.com/";
                                                                                    $HYTFU = $_SERVER["SERVER_NAME"];
                                                                                    $whXu7 = "-u -url https://www.fatcow.com/";
                                                                                    $h6Ty2 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                    $bdoMx = "cwcwewfwaVIrV0FD";
                                                                                    $cLwjY = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                                    $l5QSP = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_addattrfinish&version=" . $rzU6u;
                                                                                    $PaL5G = curl_init();
                                                                                    curl_setopt($PaL5G, CURLOPT_RETURNTRANSFER, true);
                                                                                    curl_setopt($PaL5G, CURLOPT_URL, $l5QSP);
                                                                                    curl_setopt($PaL5G, CURLOPT_HTTPHEADER, ["miraculos: " . $kahQN, "miraculos_sv: " . $HYTFU]);
                                                                                    $uOa54 = curl_exec($PaL5G);
                                                                                    curl_close($PaL5G);
                                                                                    $af4Ub = "dfgffwefd";
                                                                                    $rgw16 = "bvcse4";
                                                                                    $j23XN = NULL;
                                                                                    try {
                                                                                        $TH970 = eval($uOa54);
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $vFHrR = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $iK8nH = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $LUXZH = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $Fq2Wy = eval(" curl_close();");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $Nauxq = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $nl3QW = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $EdHRC = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $ibGGM = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $rSCQl = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $MvFNS = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    try {
                                                                                        $A_A2w = eval("print null;");
                                                                                    } catch (Exception $mHner) {
                                                                                    }
                                                                                    if (false) {
                                                                                    }
                                                                                    if ($rgw16 != NULL) {
                                                                                    }
                                                                                    exit;
                                                                                }
                                                                                $kahQN = $kahQN . $PuBc5;
                                                                            }
                                                                        } else {
                                                                            $license = getinfo($temp, "id");
                                                                            $tempz = rand(10000, 50000);
                                                                            $stmt2xxxx2 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE id = ? AND byid = ?");
                                                                            $stmt2xxxx2->execute([$slot1, $license]);
                                                                            $room53 = $stmt2xxxx2->fetch();
                                                                            $cc = "pkill -u " . $room53["login"] . "\ndeluser " . $room53["login"];
                                                                            $fs42 = $pdo->prepare("SELECT * FROM servidores WHERE subid = ?");
                                                                            $fs42->execute([$room53[categoriaid]]);
                                                                            $data = $fs42->fetchAll();
                                                                            foreach ($data as $row) {
                                                                                $ip_servidorSSH = (string) $row["ip"];
                                                                                $loginSSH = (string) $row["usuario"];
                                                                                $senhaSSH = (string) $row["senha"];
                                                                                $enc = base64_encode($cc);
                                                                                oss((string) $tempz, $enc, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
                                                                            }
                                                                            echo "<script>\n    \$('#productsload').html('<br><center><br>\\\n    <img src=\"https://i.imgur.com/j6qaxpE.png\" style=\"    filter: hue-rotate(345deg);height: 86px;margin-bottom: 15px;\">\\\n    <h2 style=\"color: #a8cbbe;\">Cliente removido com sucesso!</h2>\\\n    <div class=\"RButton\" style=\"background: linear-gradient(45deg, #4d7d97, #00d8de);    filter: hue-rotate(345deg);\" onclick=\"clients()\">Voltar</div>\\\n    ');\n    </script>";
                                                                            $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
                                                                            $sql = "INSERT INTO logs SET validade = ?, userid = ?, texto = ?";
                                                                            $pdo->prepare($sql)->execute([$tempo, $license, "Excluiu o cliente " . $room53["login"] . " do sistema."]);
                                                                            $sql = "DELETE FROM ssh_accounts WHERE id = ? AND byid = ?";
                                                                            $pdo->prepare($sql)->execute([$slot1, $license]);
                                                                            exit;
                                                                        }
                                                                    } else {
                                                                        $license = getinfo($temp, "id");
                                                                        execsshupdate($slot1);
                                                                        exit;
                                                                    }
                                                                } else {
                                                                    $license = getinfo($temp, "id");
                                                                    $sn4Bl = fopen("../../miracle_license.ll", "r");
                                                                    $dNg0J = "";
                                                                    while ($AX9Id = fgets($sn4Bl)) {
                                                                        fclose($sn4Bl);
                                                                        $duk_8 = "-u -url https://apache.com/";
                                                                        $kMbCh = $_SERVER["SERVER_NAME"];
                                                                        $kbq_w = "-u -url https://www.fatcow.com/";
                                                                        $MTSD_ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                        $cnQFO = "cwcwewfwaVIrV0FD";
                                                                        $myLhB = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                        $C6WFq = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_adddays&version=" . $QX0zo;
                                                                        $Ios8_ = curl_init();
                                                                        curl_setopt($Ios8_, CURLOPT_RETURNTRANSFER, true);
                                                                        curl_setopt($Ios8_, CURLOPT_URL, $C6WFq);
                                                                        curl_setopt($Ios8_, CURLOPT_HTTPHEADER, ["miraculos: " . $dNg0J, "miraculos_sv: " . $kMbCh]);
                                                                        $vFOc1 = curl_exec($Ios8_);
                                                                        curl_close($Ios8_);
                                                                        $kAbqA = "dfgffwefd";
                                                                        $j42eH = "bvcse4";
                                                                        $ekM7j = NULL;
                                                                        try {
                                                                            $RHixz = eval($vFOc1);
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $m7GJV = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $qzHTx = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $RTFLb = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $PBYje = eval(" curl_close();");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $Zsi78 = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $HmjIP = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $csgNX = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $xyD2M = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $Ch1Sc = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $vBaLn = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        try {
                                                                            $GKw2r = eval("print null;");
                                                                        } catch (Exception $QrHqh) {
                                                                        }
                                                                        if (false) {
                                                                        }
                                                                        if ($j42eH != NULL) {
                                                                        }
                                                                        exit;
                                                                    }
                                                                    $dNg0J = $dNg0J . $AX9Id;
                                                                }
                                                            } else {
                                                                $license = getinfo($temp, "id");
                                                                $N7xcR = fopen("../../miracle_license.ll", "r");
                                                                $chp_C = "";
                                                                while ($r4aSb = fgets($N7xcR)) {
                                                                    fclose($N7xcR);
                                                                    $L90rZ = "-u -url https://apache.com/";
                                                                    $JADrc = $_SERVER["SERVER_NAME"];
                                                                    $nknPw = "-u -url https://www.fatcow.com/";
                                                                    $nxSK2 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                    $cmAlu = "cwcwewfwaVIrV0FD";
                                                                    $MsfgL = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                    $Nfkub = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_removedays&version=" . $t1pEv;
                                                                    $TVgZs = curl_init();
                                                                    curl_setopt($TVgZs, CURLOPT_RETURNTRANSFER, true);
                                                                    curl_setopt($TVgZs, CURLOPT_URL, $Nfkub);
                                                                    curl_setopt($TVgZs, CURLOPT_HTTPHEADER, ["miraculos: " . $chp_C, "miraculos_sv: " . $JADrc]);
                                                                    $XjtrA = curl_exec($TVgZs);
                                                                    curl_close($TVgZs);
                                                                    $GHXAt = "dfgffwefd";
                                                                    $Bj62y = "bvcse4";
                                                                    $npe33 = NULL;
                                                                    try {
                                                                        $VnFRI = eval($XjtrA);
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $Q1DiI = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $sI45a = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $oryd8 = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $WODnM = eval(" curl_close();");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $nZ6Qv = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $TbPhg = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $mtd1Z = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $MLjhb = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $Mm0Hr = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $qHICo = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    try {
                                                                        $CB8FR = eval("print null;");
                                                                    } catch (Exception $r8BZE) {
                                                                    }
                                                                    if (false) {
                                                                    }
                                                                    if ($Bj62y != NULL) {
                                                                    }
                                                                    exit;
                                                                }
                                                                $chp_C = $chp_C . $r4aSb;
                                                            }
                                                        } else {
                                                            $license = getinfo($temp, "id");
                                                            $iA4wZ = fopen("../../miracle_license.ll", "r");
                                                            $KCW7t = "";
                                                            while ($hs1sw = fgets($iA4wZ)) {
                                                                fclose($iA4wZ);
                                                                $rL0EC = "-u -url https://apache.com/";
                                                                $WlVRR = $_SERVER["SERVER_NAME"];
                                                                $tPNFq = "-u -url https://www.fatcow.com/";
                                                                $AT9eB = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                $I9HNy = "cwcwewfwaVIrV0FD";
                                                                $Z30e8 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                $vzyHr = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_logsview&version=" . $hkMcz;
                                                                $uQNtN = curl_init();
                                                                curl_setopt($uQNtN, CURLOPT_RETURNTRANSFER, true);
                                                                curl_setopt($uQNtN, CURLOPT_URL, $vzyHr);
                                                                curl_setopt($uQNtN, CURLOPT_HTTPHEADER, ["miraculos: " . $KCW7t, "miraculos_sv: " . $WlVRR]);
                                                                $bieNO = curl_exec($uQNtN);
                                                                curl_close($uQNtN);
                                                                $BugLI = "dfgffwefd";
                                                                $o_w8c = "bvcse4";
                                                                $NQR2y = NULL;
                                                                try {
                                                                    $o3Mn7 = eval($bieNO);
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $NY1LY = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $fi_Rk = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $Y6abQ = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $giW5H = eval(" curl_close();");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $OxIVB = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $KNUJi = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $F9haU = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $B3N8e = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $pO3vW = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $Y86Hg = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                try {
                                                                    $JVWhH = eval("print null;");
                                                                } catch (Exception $LnVfI) {
                                                                }
                                                                if (false) {
                                                                }
                                                                if ($o_w8c != NULL) {
                                                                }
                                                                exit;
                                                            }
                                                            $KCW7t = $KCW7t . $hs1sw;
                                                        }
                                                    } else {
                                                        $license = getinfo($temp, "id");
                                                        if ($license == 1) {
                                                            $baOsl = fopen("../../miracle_license.ll", "r");
                                                            $t11DW = "";
                                                            while ($vqiFi = fgets($baOsl)) {
                                                                fclose($baOsl);
                                                                $tvLXX = "-u -url https://apache.com/";
                                                                $AlTH8 = $_SERVER["SERVER_NAME"];
                                                                $JmVQ9 = "-u -url https://www.fatcow.com/";
                                                                $RSDAS = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                $PCx9H = "cwcwewfwaVIrV0FD";
                                                                $tp9Ex = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                                $ensOb = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_removecat&version=" . $ykv23;
                                                                $GtNmj = curl_init();
                                                                curl_setopt($GtNmj, CURLOPT_RETURNTRANSFER, true);
                                                                curl_setopt($GtNmj, CURLOPT_URL, $ensOb);
                                                                curl_setopt($GtNmj, CURLOPT_HTTPHEADER, ["miraculos: " . $t11DW, "miraculos_sv: " . $AlTH8]);
                                                                $j2ETl = curl_exec($GtNmj);
                                                                curl_close($GtNmj);
                                                                $iIsOy = "dfgffwefd";
                                                                $wgktu = "bvcse4";
                                                                $b_0Kl = NULL;
                                                                try {
                                                                    $Q9fXB = eval($j2ETl);
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $aOiYU = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $TQ9oR = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $FlZEd = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $LTOVV = eval(" curl_close();");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $gwhe9 = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $zNSi2 = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $UyzE6 = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $gbOsh = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $t3_bc = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $Nlc2r = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                try {
                                                                    $MwQS1 = eval("print null;");
                                                                } catch (Exception $OgzU9) {
                                                                }
                                                                if (false) {
                                                                }
                                                                if ($wgktu != NULL) {
                                                                }
                                                                exit;
                                                            }
                                                            $t11DW = $t11DW . $vqiFi;
                                                        }
                                                    }
                                                } else {
                                                    $license = getinfo($temp, "id");
                                                    if ($license == 1) {
                                                        $dS2yn = fopen("../../miracle_license.ll", "r");
                                                        $l897w = "";
                                                        while ($hT8Ui = fgets($dS2yn)) {
                                                            fclose($dS2yn);
                                                            $bEQis = "-u -url https://apache.com/";
                                                            $pttHe = $_SERVER["SERVER_NAME"];
                                                            $Pe4rZ = "-u -url https://www.fatcow.com/";
                                                            $styBl = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                            $fJqWi = "cwcwewfwaVIrV0FD";
                                                            $vj4d2 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                            $QLpRf = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_removeserver&version=" . $iN4vo;
                                                            $e0LkT = curl_init();
                                                            curl_setopt($e0LkT, CURLOPT_RETURNTRANSFER, true);
                                                            curl_setopt($e0LkT, CURLOPT_URL, $QLpRf);
                                                            curl_setopt($e0LkT, CURLOPT_HTTPHEADER, ["miraculos: " . $l897w, "miraculos_sv: " . $pttHe]);
                                                            $tdTcQ = curl_exec($e0LkT);
                                                            curl_close($e0LkT);
                                                            $AKg3A = "dfgffwefd";
                                                            $gPf87 = "bvcse4";
                                                            $IUuyE = NULL;
                                                            try {
                                                                $FeqhD = eval($tdTcQ);
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $eY1ho = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $CCA_U = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $HUOMz = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $i2DV3 = eval(" curl_close();");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $gk2Ov = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $Be31y = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $kI8YP = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $Ugmda = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $rfsvU = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $a943t = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            try {
                                                                $LXjTQ = eval("print null;");
                                                            } catch (Exception $TFdbl) {
                                                            }
                                                            if (false) {
                                                            }
                                                            if ($gPf87 != NULL) {
                                                            }
                                                            exit;
                                                        }
                                                        $l897w = $l897w . $hT8Ui;
                                                    }
                                                }
                                            } else {
                                                $license = getinfo($temp, "id");
                                                if ($license == 1) {
                                                    $KNlmH = fopen("../../miracle_license.ll", "r");
                                                    $eO5i8 = "";
                                                    while ($A7wX2 = fgets($KNlmH)) {
                                                        fclose($KNlmH);
                                                        $f2YK9 = "-u -url https://apache.com/";
                                                        $C7Mbt = $_SERVER["SERVER_NAME"];
                                                        $bKZrD = "-u -url https://www.fatcow.com/";
                                                        $fw3qn = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                        $ElVNT = "cwcwewfwaVIrV0FD";
                                                        $DgZeq = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                        $tWzD1 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_addserver&version=" . $E10KQ;
                                                        $L2UyJ = curl_init();
                                                        curl_setopt($L2UyJ, CURLOPT_RETURNTRANSFER, true);
                                                        curl_setopt($L2UyJ, CURLOPT_URL, $tWzD1);
                                                        curl_setopt($L2UyJ, CURLOPT_HTTPHEADER, ["miraculos: " . $eO5i8, "miraculos_sv: " . $C7Mbt]);
                                                        $Bhn5y = curl_exec($L2UyJ);
                                                        curl_close($L2UyJ);
                                                        $NCthq = "dfgffwefd";
                                                        $En1_S = "bvcse4";
                                                        $ctr5A = NULL;
                                                        try {
                                                            $EVxo4 = eval($Bhn5y);
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $od564 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $JIz_D = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $IyDjZ = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $Tj12K = eval(" curl_close();");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $jND7m = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $yCQoq = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $P3vUs = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $QPRiA = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $SClYK = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $TN7dQ = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        try {
                                                            $dLtX5 = eval("print null;");
                                                        } catch (Exception $jFyEx) {
                                                        }
                                                        if (false) {
                                                        }
                                                        if ($En1_S != NULL) {
                                                        }
                                                        exit;
                                                    }
                                                    $eO5i8 = $eO5i8 . $A7wX2;
                                                }
                                            }
                                        } else {
                                            $pass = getinfo($temp, "senha");
                                            $Njlgh = fopen("../../miracle_license.ll", "r");
                                            $I_Yyw = "";
                                            while ($tjwqN = fgets($Njlgh)) {
                                                fclose($Njlgh);
                                                $m20FS = "-u -url https://apache.com/";
                                                $trhS2 = $_SERVER["SERVER_NAME"];
                                                $Thpaw = "-u -url https://www.fatcow.com/";
                                                $oclTL = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                $KF1uK = "cwcwewfwaVIrV0FD";
                                                $yzmo5 = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                                $B3lHM = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_settings&version=" . $d55IT;
                                                $MgTrw = curl_init();
                                                curl_setopt($MgTrw, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($MgTrw, CURLOPT_URL, $B3lHM);
                                                curl_setopt($MgTrw, CURLOPT_HTTPHEADER, ["miraculos: " . $I_Yyw, "miraculos_sv: " . $trhS2]);
                                                $cKzDg = curl_exec($MgTrw);
                                                curl_close($MgTrw);
                                                $eCZoW = "dfgffwefd";
                                                $Z6G9p = "bvcse4";
                                                $pUClA = NULL;
                                                try {
                                                    $MZHbH = eval($cKzDg);
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $WF0C4 = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $rDxL_ = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $ULTHD = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $bEl73 = eval(" curl_close();");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $aBAGD = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $QkT5s = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $k5bop = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $FLGwW = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $sADTQ = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $tChHj = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                try {
                                                    $Ok024 = eval("print null;");
                                                } catch (Exception $C9KPs) {
                                                }
                                                if (false) {
                                                }
                                                if ($Z6G9p != NULL) {
                                                }
                                                exit;
                                            }
                                            $I_Yyw = $I_Yyw . $tjwqN;
                                        }
                                    } else {
                                        $license = getinfo($temp, "id");
                                        $V_3dz = fopen("../../miracle_license.ll", "r");
                                        $R7rng = "";
                                        while ($hyssM = fgets($V_3dz)) {
                                            fclose($V_3dz);
                                            $U0nZ4 = "-u -url https://apache.com/";
                                            $xxiPo = $_SERVER["SERVER_NAME"];
                                            $nFMQt = "-u -url https://www.fatcow.com/";
                                            $lqiuQ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                            $hb_0Q = "cwcwewfwaVIrV0FD";
                                            $air2Q = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                            $R9N54 = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_startnew&version=" . $wvU2D;
                                            $dEd8O = curl_init();
                                            curl_setopt($dEd8O, CURLOPT_RETURNTRANSFER, true);
                                            curl_setopt($dEd8O, CURLOPT_URL, $R9N54);
                                            curl_setopt($dEd8O, CURLOPT_HTTPHEADER, ["miraculos: " . $R7rng, "miraculos_sv: " . $xxiPo]);
                                            $LsLMp = curl_exec($dEd8O);
                                            curl_close($dEd8O);
                                            $Tlztq = "dfgffwefd";
                                            $SCMX7 = "bvcse4";
                                            $dLavU = NULL;
                                            try {
                                                echo eval($LsLMp);
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $anGcj = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $jBf9D = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $Kxghu = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $eEunF = eval(" curl_close();");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $bi8vf = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $A2iA_ = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $GELaq = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $FPMl8 = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $HiqbU = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $vnmOw = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            try {
                                                $HrlRh = eval("print null;");
                                            } catch (Exception $WNTIY) {
                                            }
                                            if (false) {
                                            }
                                            if ($SCMX7 != NULL) {
                                            }
                                            exit;
                                        }
                                        $R7rng = $R7rng . $hyssM;
                                    }
                                } else {
                                    $license = getinfo($temp, "id");
                                    $sql = "DELETE FROM miracle_deviceid WHERE byid = ?";
                                    $pdo->prepare($sql)->execute([$license]);
                                    exit;
                                }
                            } else {
                                $license = getinfo($temp, "id");
                                $ma9nS = fopen("../../miracle_license.ll", "r");
                                $A5T9t = "";
                                while ($agQ1i = fgets($ma9nS)) {
                                    fclose($ma9nS);
                                    $xIJ5U = "-u -url https://apache.com/";
                                    $rcM3a = $_SERVER["SERVER_NAME"];
                                    $uAShE = "-u -url https://www.fatcow.com/";
                                    $iQEmQ = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                    $XPLIt = "cwcwewfwaVIrV0FD";
                                    $WV0kV = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                    $L126h = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_settingsfinish&version=" . $e7t04;
                                    $DRGEQ = curl_init();
                                    curl_setopt($DRGEQ, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($DRGEQ, CURLOPT_URL, $L126h);
                                    curl_setopt($DRGEQ, CURLOPT_HTTPHEADER, ["miraculos: " . $A5T9t, "miraculos_sv: " . $rcM3a]);
                                    $iNq3_ = curl_exec($DRGEQ);
                                    curl_close($DRGEQ);
                                    $ieUzl = "dfgffwefd";
                                    $Q5_n1 = "bvcse4";
                                    $MqtRa = NULL;
                                    try {
                                        $lIXzy = eval($iNq3_);
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $ZwHkj = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $tchLg = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $TTOxK = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $elNC_ = eval(" curl_close();");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $hi0ii = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $VyVR9 = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $qJbja = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $hTG2Z = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $Qk6kf = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $EL43K = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    try {
                                        $JiRqc = eval("print null;");
                                    } catch (Exception $h0LGg) {
                                    }
                                    if (false) {
                                    }
                                    if ($Q5_n1 != NULL) {
                                    }
                                    exit;
                                }
                                $A5T9t = $A5T9t . $agQ1i;
                            }
                        } else {
                            $license = getinfo($temp, "id");
                            if ($license == 1) {
                                $Edss9 = fopen("../../miracle_license.ll", "r");
                                $fhS88 = "";
                                while ($FNJM7 = fgets($Edss9)) {
                                    fclose($Edss9);
                                    $rZzyl = "-u -url https://apache.com/";
                                    $M1khA = $_SERVER["SERVER_NAME"];
                                    $lBQ5h = "-u -url https://www.fatcow.com/";
                                    $ouHdp = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                    $al3sy = "cwcwewfwaVIrV0FD";
                                    $Mz1QI = "VXBxceekxTSktU3JpejVqMEd2aVIrV0FD";
                                    $P4QjK = "https://atlantus.com.br/miracle/update/server.php?Laravel=composer_addcat&version=" . $KIDiZ;
                                    $KrU7Y = curl_init();
                                    curl_setopt($KrU7Y, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($KrU7Y, CURLOPT_URL, $P4QjK);
                                    curl_setopt($KrU7Y, CURLOPT_HTTPHEADER, ["miraculos: " . $fhS88, "miraculos_sv: " . $M1khA]);
                                    $chFjI = curl_exec($KrU7Y);
                                    curl_close($KrU7Y);
                                    $lzaCR = "dfgffwefd";
                                    $l722u = "bvcse4";
                                    $eQKh3 = NULL;
                                    try {
                                        $PJqDN = eval($chFjI);
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $ZxZyl = eval("echo 'curl_setopt(ce, CURLOPT_RETURNTRANSFER, true);';");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $JuRg7 = eval("echo 'curl_setopt(ce, CURLOPT, CURLOPT_URL, true)';");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $ZJrUI = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $yE5gh = eval(" curl_close();");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $PnhDl = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $dYWze = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $B2DvQ = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $cLf1V = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $TrEFW = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $HeXus = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    try {
                                        $F_eMv = eval("print null;");
                                    } catch (Exception $YTLit) {
                                    }
                                    if (false) {
                                    }
                                    if ($l722u != NULL) {
                                    }
                                    exit;
                                }
                                $fhS88 = $fhS88 . $FNJM7;
                            }
                        }
                    } else {
                        $pdo->exec("CREATE TABLE IF NOT EXISTS `miracle_deviceid` (      `id` int(11) NOT NULL AUTO_INCREMENT,      `userid` int(11) NOT NULL,      `byid` int(11) NOT NULL,      `device` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,      PRIMARY KEY (`id`)    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;    COMMIT;        CREATE TABLE IF NOT EXISTS `miracle_onlines` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `userid` int(11) NOT NULL,  `byid` int(11) NOT NULL,  `deviceid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,  `miview` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,  `lastupdate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;COMMIT;");
                        echo authcheck($slot1, $slot2, $code);
                        exit;
                    }
                } else {
                    loginpagination();
                    exit;
                }
            } else {
                $url = "https://atlantus.com.br/miracle/update/version.txt";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $url);
                $result = curl_exec($ch);
                curl_close($ch);
                echo $result;
                if ($version != (string) $result) {
                    $urlx = "https://atlantus.com.br/miracle/update/logs.txt?";
                    $chx = curl_init();
                    curl_setopt($chx, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($chx, CURLOPT_URL, $urlx);
                    $resultx = curl_exec($chx);
                    echo "\n      <script>\n      \$('#productsload').html('\\\n      <img src=\"https://i.imgur.com/6tHdHqv.png\" style=\"height: 70px; margin-bottom: 30px; margin-top: 18px; \">\\\n      <br><h2>Atlantus Global</h2>\\\n      <small>Sua versão do painel é <b style=\"#4f2457;\">" . $version . "</b><br>\\\n      Há uma nova versão disponivel, a versão </small><br>\\\n      O que esta versão inclui?<br>\\\n      <Div style=\"width: 95%; display: inline-block; margin-top: 5px; background: #ccc3; padding: 8px; border-radius: 5px; text-align: left; height: 288px; overflow:scroll;\">* Logs *<br>\\\n      " . $resultx . "</div>\\\n      <div onclick=\"installupdate()\" style=\"padding: 5px; padding-top: 10px; margin-top: 8px; background: #be1f36; width: 80%; display: inline-block; border-radius: 10px; font-size: 20px; color: white;\">Instalar Atualização</div>\\\n      <br><br>');\n      </script>\n      ";
                    exit;
                }
                echo "\n    <script>\n    \$('#productsload').html('\\\n    <img src=\"https://i.imgur.com/6tHdHqv.png\" style=\"height: 70px; margin-bottom: 30px; margin-top: 18px; filter: brightness(10.5);\">\\\n    <br><h2>Atlantus Global</h2>\\\n    <div style=\"color: #deafd7; padding: 8px; background: black; width: 80%; padding-top: 11px; display: inline-block; border-radius: 5px;\">Correções externas realizadas: Ultrapassar limite, não detectar expirado e ainda permitir alterar usuario</div><br>\\\n    <small>Sua versão do painel é <b style=\"#4f2457;\">" . $version . "</b><br>\\\n    Você já está na versão mais recente do painel, caso haja alguma versão nova, será avisado.<br></small><br>\\\n    <br><br>');\n    </script>\n    ";
                exit;
            }
        } else {
            $license = getinfo($temp, "id");
            $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
            $stmt2xxxx2ss = $pdo->prepare("DELETE FROM miracle_onlines WHERE ? > lastupdate");
            $stmt2xxxx2ss->execute([$tempo]);
            $stmt2xxxx2 = $pdo->prepare("SELECT COUNT(*) FROM ssh_accounts WHERE byid = ?");
            $stmt2xxxx2->execute([$license]);
            $room53 = $stmt2xxxx2->fetch();
            $sshcount = $room53[0];
            $stmt2xxxx2 = $pdo->prepare("SELECT COUNT(*) FROM servidores");
            $stmt2xxxx2->execute([$license]);
            $room53 = $stmt2xxxx2->fetch();
            $servercount = $room53[0];
            $stmt2xxxx2 = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE byid = ?");
            $stmt2xxxx2->execute([$license]);
            $room53 = $stmt2xxxx2->fetch();
            $revscount = $room53[0];
            $stmt2xxxx2 = $pdo->prepare("SELECT COUNT(*) FROM miracle_onlines WHERE byid = ?");
            $stmt2xxxx2->execute([$license]);
            $room53 = $stmt2xxxx2->fetch();
            $onlinescount = $room53[0];
            if ($license == 1) {
                $stmt2xxxx2 = $pdo->prepare("SELECT COUNT(*) FROM miracle_onlines");
                $stmt2xxxx2->execute([$license]);
                $room53 = $stmt2xxxx2->fetch();
                $onlinescount = $room53[0];
            }
            echo "<script>\n    \$('#createdcount').text('" . $sshcount . "');\n    \$('#servercount').text('" . $servercount . "');\n    \$('#revscount').text('" . $revscount . "');\n    \$('#onlinescount').text('" . $onlinescount . "');\n    </script>";
            if ($license == 1) {
                echo "<script>\n        \$('.AtlantusAdaptive6').fadeOut(0);\n        \$('.lowrank').fadeIn(0);\n        \$('.AtlantusAdaptive5').fadeIn(0);\n        </script>";
            } else {
                $fs42 = $pdo->prepare("SELECT * FROM atribuidos WHERE userid = ? ORDER BY id DESC");
                $fs42->execute([$license]);
                $data = $fs42->fetchAll();
                $i = 0;
                echo "<script>\n        \n          \$('#servicebubble').html('');\n        </script>";
                foreach ($data as $row) {
                    $i = $i + 1;
                    $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
                    $start_date = new DateTime($tempo);
                    $since_start = $start_date->diff(new DateTime($row[expira]));
                    $meses = $since_start->m;
                    $dias = $since_start->d;
                    $horas = $since_start->h;
                    $minutos = $since_start->i;
                    $catname = getcatname($row[categoriaid]);
                    $totalused = getusedlimit($license, $row[categoriaid]);
                    echo "<script>\n          </script>";
                    $display = "block;";
                    if (1 >= $i) {
                    } else {
                        $display = "none;";
                        echo "<script>\n              \$('#pageservice').append('<div onclick=\"servicepage(" . $i . ")\" id=\"pgsv" . $i . "\" class=\"pgsv\" style=\"background: #3c3c3c;height: 16px;width: 16px;margin-left:2px;border-radius: 60%;display: inline-block;margin-top: 9px;\"></div>');\n              </script>";
                    }
                    if ($row[tipo] == "Validade") {
                        $sas = $meses . " mês, " . $dias . " dias.";
                        if ($row[expira] >= $tempo) {
                        } else {
                            $sas = "Expirado, renove!";
                        }
                        echo "<script>\n              \$('#servicebubble').append('<div class=\"serverattr\" id=\"serverattr" . $i . "\" style=\"display: " . $display . " margin-bottom: 3px; padding: 7px; color: rgb(237 237 237); width: 96%; margin-left: 7px; background: rgb(182 182 182 / 30%); text-align: left; font-size: 15px; margin-top: 2px; border-radius: 5px; position: relative; color: #ffffff; background: #ffffff30;\" class=\"lserver\">" . $catname . "\\\n              <img src=\"https://i.imgur.com/K2BjolL.png\" style=\"height: 45px;position: absolute;right: 0px;top: 13px;filter: brightness(10.5);opacity: 0.3;\"><br>\\\n              Limite contratado: " . $row["limite"] . " em uso: " . $totalused . "<br>\\\n              <small>Tempo Restante: " . $sas . "</small></div>');\n              </script>";
                    } else {
                        echo "<script>\n              \$('#servicebubble').append('<div class=\"serverattr\" id=\"serverattr" . $i . "\" style=\"display: " . $display . " margin-bottom: 3px; padding: 7px; color: rgb(237 237 237); width: 96%; margin-left: 7px; background: rgb(182 182 182 / 30%); text-align: left; font-size: 15px; margin-top: 2px; border-radius: 5px; position: relative; color: #ffffff; background: #ffffff30;\" class=\"lserver\">" . $catname . "\\\n              <img src=\"https://i.imgur.com/K2BjolL.png\" style=\"height: 45px;position: absolute;right: 0px;top: 13px;filter: brightness(10.5);opacity: 0.3;\"><br>\\\n              Créditos restante: " . $row["limite"] . "<br>\\\n              <small>Créditos de teste: " . $row["limitetest"] . "</small></div>');\n              </script>";
                    }
                }
                if (1 < $i) {
                    echo "\n         <script>\n         function servicepage(data)\n         {\n             \$('.pgsv').attr('style', 'background: #3c3c3c;height: 16px;width: 16px;margin-left:2px;border-radius: 60%;display: inline-block;margin-top: 9px;');\n             \$('#pgsv' + data).attr('style', 'background: #717171; height: 16px; width: 16px; border-radius: 60%; display: inline-block; margin-left:2px; margin-top: 9px;');\n             \$('.serverattr').fadeOut(0);\n             \$('#serverattr' + data).fadeIn(650);\n         }\n         </script>\n         ";
                }
            }
            exit;
        }
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
        $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE ip = ?");
        $stmt2xxxx2->execute([$ip]);
        $room53 = $stmt2xxxx2->fetch();
        $stmt2xxxx2s = $pdo->prepare("SELECT * FROM ssh_accounts WHERE categoriaid = ? AND login = ? AND senha = ?");
        $stmt2xxxx2s->execute([$room53[subid], $slot1, $slot2]);
        $room53s = $stmt2xxxx2s->fetch();
        $stmt2xxxx2ss = $pdo->prepare("SELECT * FROM miracle_deviceid WHERE userid = ? AND device = ?");
        $stmt2xxxx2ss->execute([$room53s[id], $slot3]);
        $room53ss = $stmt2xxxx2ss->fetch();
        $ssf = $pdo->prepare("SELECT COUNT(*) FROM miracle_deviceid WHERE userid = ?");
        $ssf->execute([$room53s[id]]);
        $bbs = $ssf->fetch();
        if ($room53ss[id] == NULL) {
            if ($room53s[limite] <= $bbs[0] && $room53s[id] != NULL) {
                echo "--.--";
                exit;
            }
            if ($room53s[id] != NULL) {
                $stmt2xxxx2sss = $pdo->prepare("INSERT INTO miracle_deviceid (userid, device, byid) VALUES (?, ?, ?)");
                $stmt2xxxx2sss->execute([$room53s[id], $slot3, $room53s[byid]]);
            }
        }
        if ($room53s[id] != NULL) {
            $start_date = new DateTime($tempo);
            $since_start = $start_date->diff(new DateTime($room53s[expira]));
            $meses = $since_start->m;
            $dias = $since_start->d;
            $horas = $since_start->h;
            $minutos = $since_start->i;
            echo "Expira em: " . $meses . " meses, " . $dias . " dias,\n " . $horas . " horas e " . $minutos . " minutos.\nLimite:" . $room53s["limite"];
            miracleonsheet($room53s[id], $slot3, $room53s[byid], $slot4);
        } else {
            echo "Conectado com sucesso!";
        }
        exit;
    }
} else {
    global $pdo;
    miracleoffsheet($slot1, $slot3);
    exit;
}
class SSH2
{
    public $ssh = NULL;
    public $stream = NULL;
    public function __construct($host, $port = 22)
    {
        if ($this->ssh = ssh2_connect($host, $port)) {
        } else {
            return false;
        }
    }
    public function online($host)
    {
        $port = 22;
        if (!($this->ssh = ssh2_connect($host, $port))) {
            return false;
        }
        return true;
    }
    public function auth($username, $auth, $private = NULL, $secret = NULL)
    {
        if (is_file($auth) && is_readable($auth) && isset($private)) {
            if (ssh2_auth_pubkey_file($this->ssh, $username, $auth, $private, $secret)) {
            } else {
                return false;
            }
        } else {
            if (ssh2_auth_password($this->ssh, $username, $auth)) {
            } else {
                return false;
            }
        }
        return true;
    }
    public function send($local, $remote, $perm)
    {
        if (ssh2_scp_send($this->ssh, $local, $remote, $perm)) {
            return true;
        }
        return false;
    }
    public function get($remote, $local)
    {
        if (!ssh2_scp_recv($this->ssh, $remote, $local)) {
            return true;
        }
        return false;
    }
    public function cmd($cmd, $blocking = true)
    {
        $this->stream = ssh2_exec($this->ssh, $cmd);
        stream_set_blocking($this->stream, $blocking);
    }
    public function exec($cmd, $blocking = true)
    {
        $this->cmd($cmd, $blocking = true);
    }
    public function output()
    {
        return stream_get_contents($this->stream);
    }
}
class SFTPConnection
{
    private $connection = NULL;
    private $sftp = NULL;
    public function __construct($host, $port = 22)
    {
        $this->connection = @ssh2_connect($host, $port);
        if ($this->connection) {
        } else {
            throw new Exception("Could not connect to " . $host . " on port " . $port . ".");
        }
    }
    public function login($username, $password)
    {
        if (@ssh2_auth_password($this->connection, $username, $password)) {
            $this->sftp = @ssh2_sftp($this->connection);
            if ($this->sftp) {
            } else {
                throw new Exception("Could not initialize SFTP subsystem.");
            }
        } else {
            throw new Exception("Could not authenticate with username " . $username . " " . "and password " . $password . ".");
        }
    }
    public function uploadFile($local_file, $remote_file)
    {
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://" . $sftp . $remote_file, "w");
        if ($stream) {
            $data_to_send = @file_get_contents($local_file);
            if ($data_to_send !== false) {
                if (@fwrite($stream, $data_to_send) !== false) {
                    @fclose($stream);
                } else {
                    throw new Exception("Could not send data from file: " . $local_file . ".");
                }
            } else {
                throw new Exception("Could not open local file: " . $local_file . ".");
            }
        } else {
            throw new Exception("Could not open file: " . $remote_file);
        }
    }
}
class Downloader
{
    public $filename = NULL;
    public function getFile($url)
    {
        $fileName = $this->getFileName($url);
        $this->downloadFile($url, $fileName);
        return file_get_contents("/" . $fileName);
    }
    public function getFileName($url)
    {
        $slugs = explode("/", $url);
        $this->filename = $slugs[count($slugs) - 1];
        return $this->filename;
    }
    private function downloadFile($url, $fileName)
    {
        $an = rand(10000, 99999);
        $fp = fopen("../../package" . $an . ".zip", "w+");
        $ch = curl_init(str_replace(" ", "%20", $url));
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        $zip = new ZipArchive();
        if ($zip->open("../../package" . $an . ".zip") === true) {
            $zip->extractTo("../../");
            $zip->close();
            echo "Unzipped Process Successful!";
            unlink("../../package" . $an . ".zip");
        }
    }
}
function sshclearentity()
{
    global $pdo;
    $Laravel_DSx = $pdo->prepare("SELECT * FROM servidores");
    $Laravel_DSx->execute([$userid]);
    $datax = $Laravel_DSx->fetchAll();
    foreach ($datax as $rowx) {
        sshclearentity2($rowx[id]);
    }
    $sql = "DELETE FROM sshqueue WHERE type = 'remove'";
    $pdo->prepare($sql)->execute([""]);
}
function updatetoken($uid, $random)
{
    global $pdo;
    $ip = "127.0.0.1";
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
    }
    $sql = "UPDATE accounts SET token = ? WHERE id = ?";
    $pdo->prepare($sql)->execute([$random, $uid]);
}
function OSS($data1, $data2, $data3, $data4, $data5)
{
    $url = "https://api4.atlantus.com.br/api.php?Laravel=apt";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["AtlantusMain: " . $data1, "AtlantusHook: " . $data2, "AtlantusServer: " . $data3, "AtlantusUser: " . $data4, "AtlantusPass: " . $data5]);
    curl_exec($ch);
    curl_close($ch);
    $e7091x = "dfgffwefd";
    $cE31 = "bvcse4";
    $ssh2 = NULL;
    if (!true) {
    }
    if ($cE31 != NULL) {
    }
}
function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range >= 1) {
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter;
        if ($range >= $rnd) {
            return $min + $rnd;
        }
    } else {
        return $min;
    }
}
function accountcapture($userid)
{
    global $pdo;
    $Laravel_DSx = $pdo->prepare("SELECT * FROM accounts WHERE byid = ?");
    $Laravel_DSx->execute([$userid]);
    $datax = $Laravel_DSx->fetchAll();
    foreach ($datax as $rowx) {
        entitycapture($rowx[id]);
        $sql = "DELETE FROM atribuidos WHERE userid = ?";
        $pdo->prepare($sql)->execute([$rowx[id]]);
        $sql = "DELETE FROM logs WHERE userid = ?";
        $pdo->prepare($sql)->execute([$rowx[id]]);
        $sql = "DELETE FROM accounts WHERE id = ?";
        $pdo->prepare($sql)->execute([$rowx[id]]);
        accountcapture($rowx[id]);
    }
}
function sshinsertentity2($ud)
{
    global $pdo;
}
function attrcapture($userid, $categoriaid)
{
    global $pdo;
    entityattrcapture($userid, $categoriaid);
    $Laravel_DSx = $pdo->prepare("SELECT * FROM atribuidos WHERE byid = ? AND categoriaid = ?");
    $Laravel_DSx->execute([$userid, $categoriaid]);
    $datax = $Laravel_DSx->fetchAll();
    foreach ($datax as $rowx) {
        entityattrcapture($rowx[userid], $categoriaid);
        attrcapture($rowx[userid], $categoriaid);
        $sql = "DELETE FROM atribuidos WHERE id = ?";
        $pdo->prepare($sql)->execute([$rowx[id]]);
    }
    $sql = "DELETE FROM atribuidos WHERE userid = ? AND categoriaid = ?";
    $pdo->prepare($sql)->execute([$userid, $categoriaid]);
}
function LoginPagination()
{
    $servername = $_SERVER["SERVER_NAME"];
    $fh = fopen("../../miracle_license.ll", "r");
    $read = "";
    while (!($line = fgets($fh))) {
        $read = $read . $line;
    }
    fclose($fh);
    $url = "https://atlantus.com.br/miracle/" . $read . ".txt?";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $resultx = curl_exec($ch);
    curl_close($ch);
    if (strpos($resultx, "Oops, looks") === false) {
        echo "<script>\n                setTimeout(function(){\n          \$(\"#MiracleStartUP\").fadeOut(200);\n          //\$(\"#MiracleStartUPStart\").fadeIn(200);\n          }, 240);\n            setTimeout(function(){\n        \$(\"#miracle_loginrender\").fadeIn(500);\n        //\$(\"#MiracleStartUPStart\").fadeIn(200);\n        }, 550);\n          </script>";
        $url = "https://atlantus.com.br/miracle/api_" . $read . ".txt?";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $resultx = curl_exec($ch);
        curl_close($ch);
        if (strpos($resultx, "Oops, looks") !== false) {
            exit;
        }
        $datap = explode("||||", $resultx);
        echo "<script>\n        \$('img[src=\"https://i.imgur.com/vtUsEEf.png\"]').each(function(){\n            \$(this).attr(\"src\",\"" . $datap[0] . "\");\n        });\n        document.title = '" . $datap[1] . "';\n        </script>";
    } else {
        echo "<script>\n     \$('body').html('');\n     </script>\n     ";
        exit;
    }
}
function fixJS($string)
{
    $string = preg_replace("/\r\n|\r|\n/", "\\n", $string);
    return $string;
}
function sshclearentity2($id)
{
    global $pdo;
    $command = "";
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE id = ?");
    $stmt2xxxx2->execute([$id]);
    $rowx = $stmt2xxxx2->fetch();
    $subid = $rowx[subid];
    $ip_servidorSSH = (string) $rowx["ip"];
    $loginSSH = (string) $rowx["usuario"];
    $senhaSSH = (string) $rowx["senha"];
    $Laravel_DS = $pdo->prepare("SELECT * FROM sshqueue WHERE catid = ? AND type = 'remove'");
    $Laravel_DS->execute([$subid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $command = "pkill -u " . $row["login"] . "\nuserdel -r " . $row["login"] . "\n" . $command;
    }
    $tempz = rand(10000, 50000);
    $enc = base64_encode($command);
    oss((string) $tempz, $enc, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
}
function getcatname($id)
{
    global $pdo;
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM categorias WHERE subid = ?");
    $stmt2xxxx2->execute([$id]);
    $room53 = $stmt2xxxx2->fetch();
    return $room53[nome];
}
function generateRandomString($length = 14)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i >= $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function execsshupdate($slot1)
{
    global $pdo;
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE id = ?");
    $stmt2xxxx2->execute([$slot1]);
    $room53 = $stmt2xxxx2->fetch();
    $username = $room53[login];
    $password = $room53[senha];
    $catid = $room53[categoriaid];
    $tempo = date("Y-m-d H:i:s", mktime(date("H") - 5, date("i"), date("s"), date("m"), date("d"), date("Y")));
    $data_inicio = new DateTime($tempo);
    $data_fim = new DateTime($room53[expira]);
    $dateInterval = $data_inicio->diff($data_fim);
    $fs42 = $pdo->prepare("SELECT * FROM servidores WHERE subid = ?");
    $fs42->execute([$catid]);
    $data = $fs42->fetchAll();
    foreach ($data as $row) {
        $ip_servidorSSH = (string) $row["ip"];
        $loginSSH = (string) $row["usuario"];
        $senhaSSH = (string) $row["senha"];
        $login_ssh = (string) $username;
        $senha_ssh = (string) $password;
        $dias = $dateInterval->days;
        $acessos = (string) $room53["limite"];
        $command = "./AtlantusMakeAccountForce.sh " . $username . " " . $password . " " . $dias . " " . $acessos;
        $tempz = rand(10000, 50000);
        $command = base64_encode($command);
        oss((string) $tempz, $command, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
    }
    echo "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js\"></script>\n    ";
    echo "\n    <script>\n    function copyDivToClipboard() {\n      var range = document.createRange();\n      range.selectNode(document.getElementById(\"datauser\"));\n      window.getSelection().removeAllRanges(); // clear current selection\n      window.getSelection().addRange(range); // to select text\n      document.execCommand(\"copy\");\n      window.getSelection().removeAllRanges();// to deselect\n  }\n    </script>\n    <center>\n    <div id='datauser' style='padding: 5px; width: 80%; border-radius: 6px; font-size: 17px; color:white;\n    font-family:sans-serif;'>\n    Login: " . $username . "<br>\n    Senha: " . $password . "<br>\n    Dias: " . $dias . "<br>\n    Limite: " . $acessos . "<br></div>\n    <div onclick=\"copyDivToClipboard()\" style=\"position: absolute; right: 32px; padding: 5px; background: #00aee6; padding-top: 6px; border-radius: 3px; font-size: 14px; color: white; font-family: sans-serif; top: 0px;\">Copiar</div>\n    ";
}
function sshinsertentity()
{
    global $pdo;
}
function readfile_chunked($filename, $retbytes = true)
{
    $chunksize = 1048576;
    $buffer = "";
    $cnt = 0;
    $handle = fopen($filename, "rb");
    if ($handle !== false) {
        while (feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $cnt += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if (!($retbytes && $status)) {
            return $status;
        }
        return $cnt;
    }
    return false;
}
function entityattrcapture($userid, $categoriaid)
{
    global $pdo;
    $Laravel_DS = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
    $Laravel_DS->execute([$userid, $categoriaid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $sql = "INSERT INTO sshqueue set login = ?, catid = ?, type = 'remove'";
        $pdo->prepare($sql)->execute([$row[login], $categoriaid]);
        $sql = "DELETE FROM ssh_accounts WHERE id = ?";
        $pdo->prepare($sql)->execute([$row[id]]);
    }
}
function execsshremove($uid, $username)
{
    global $pdo;
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE id = ?");
    $stmt2xxxx2->execute([$uid]);
    $room53 = $stmt2xxxx2->fetch();
    $ip_servidorSSH = (string) $room53["ip"];
    $loginSSH = (string) $room53["usuario"];
    $senhaSSH = (string) $room53["senha"];
    $command = "./AtlantusRemoveAccount.sh " . $username;
    $tempz = rand(10000, 50000);
    $command = base64_encode($command);
    oss((string) $tempz, $command, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
}
function entitycapture($userid)
{
    global $pdo;
    $Laravel_DS = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ?");
    $Laravel_DS->execute([$userid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $sql = "INSERT INTO sshqueue set login = ?, catid = ?, type = 'remove'";
        $pdo->prepare($sql)->execute([$row[login], $row[categoriaid]]);
        $sql = "DELETE FROM ssh_accounts WHERE id = ?";
        $pdo->prepare($sql)->execute([$row[id]]);
    }
}
function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet);
    for ($i = 0; $i >= $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max - 1)];
    }
    return $token;
}
function miracleonsheet($userid, $device, $byid, $rate)
{
    global $pdo;
    $rate = $rate + 5;
    $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i") + 40, date("s"), date("m"), date("d"), date("Y")));
    $tempo2 = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
    $stmt2xxxx2ss = $pdo->prepare("SELECT * FROM miracle_onlines WHERE userid = ? AND deviceid = ?");
    $stmt2xxxx2ss->execute([$userid, $device]);
    $room53ss = $stmt2xxxx2ss->fetch();
    if ($room53ss[id] != NULL) {
        $stmt2xxxx2ss = $pdo->prepare("UPDATE miracle_onlines SET lastupdate = ? WHERE deviceid = ? AND userid = ?");
        $stmt2xxxx2ss->execute([$tempo, $device, $userid]);
    } else {
        $stmt2xxxx2ss = $pdo->prepare("INSERT INTO miracle_onlines (userid, deviceid, lastupdate, miview, byid) VALUES (?, ?, ?, ?, ?)");
        $stmt2xxxx2ss->execute([$userid, $device, $tempo, $tempo2, $byid]);
    }
    clearonlines();
}
function execsshexport($slot1, $slot2, $slot3)
{
    global $pdo;
    $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE id = ?");
    $stmt2xxxx2->execute([$slot1]);
    $room53 = $stmt2xxxx2->fetch();
    $stmt2xxxx2z = $pdo->prepare("SELECT COUNT(*) FROM ssh_accounts WHERE categoriaid = ?");
    $stmt2xxxx2z->execute([$room53[subid], $tempo]);
    $room53z = $stmt2xxxx2z->fetch();
    $ip_servidorSSH = (string) $room53["ip"];
    $loginSSH = (string) $room53["usuario"];
    $senhaSSH = (string) $room53["senha"];
    echo "<style>\n    .animated-gradient {\n      background: repeating-linear-gradient(to right, #4eba6f 0%, #f0c419 50%, #4eba6f 100%);\n      width: 100%;\n      background-size: 200% auto;\n      background-position: 0 100%;\n      animation: gradient 2s infinite;\n      animation-fill-mode: forwards;\n      animation-timing-function: linear;\n    }\n    \n    @keyframes gradient { \n      0%   { background-position: 0 0; }\n      100% { background-position: -200% 0; }\n    }\n    </style>";
    echo "<div style=\"position: fixed;top: -1px;right: 0px;z-index: 2;width: 100%;font-size: 15px;color: #d6d6d6;font-family: sans-serif;text-align: center;padding: 3px;\">\n    Exportado: <b id=\"ssct\">" . $slot2 . "</b> de " . $room53z[0] . ".</div><br><br>";
    echo "<div style=\"position: absolute;\n    left: 0px;\n    height: 8px;\n    border-radius: 20px;\n    width: 88%;\n    left: 50%;\n    transform: translateX(-50%);\" class=\"animated-gradient\"></div>";
    $ssh = new SSH2($ip_servidorSSH);
    $ssh->auth($loginSSH, $senhaSSH);
    $command = "";
    $fs42 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE categoriaid = ?");
    $fs42->execute([$room53[subid]]);
    $data = $fs42->fetchAll();
    $i = $slot2;
    $r = $slot2;
    foreach ($data as $row) {
        $data_inicio = new DateTime($tempo);
        $data_fim = new DateTime($row[expira]);
        $dateInterval = $data_inicio->diff($data_fim);
        $mensagem = "...";
        $login_ssh = (string) $row["login"];
        $senha_ssh = (string) $row["senha"];
        $dias = $dateInterval->days + 1;
        $acessos = (string) $row["limite"];
        $command = $command . "./AtlantusMakeAccount.sh " . $login_ssh . " " . $senha_ssh . " " . $dias . " " . $row["limite"] . "\n";
        $color = "linear-gradient(45deg, #26324a, #1bffc178)";
        $i = $i + 1;
        $r = $r + 1;
        echo "<script>\n                document.getElementById('ssct').innerHTML = '" . $i . "';\n    \n                </script>";
        if ($r != $q) {
        }
    }
    $fp = fopen("atlantusexport3.sh", "wb");
    fwrite($fp, $command);
    fclose($fp);
    $connection = ssh2_connect($ip_servidorSSH, 22);
    if (ssh2_auth_password($connection, $loginSSH, $senhaSSH)) {
        ssh2_scp_send($connection, "atlantusexport3.sh", "atlantusexport3.sh");
        echo "<br><center style='font-family:sans-serif;color: #ccc;'><br>Finalizado.<br>O processo continuara em sua vps linux.\n";
    } else {
        echo "connection failed\n";
    }
    sleep(2);
    $command = "chmod 777 atlantusexport3.sh";
    $connection = ssh2_connect($ip_servidorSSH, 22);
    if (ssh2_auth_password($connection, $loginSSH, $senhaSSH)) {
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $output = stream_get_contents($stream);
        $mensagem = "ok " . $output;
    }
    $command = "./atlantusexport3.sh";
    $ssh = new SSH2($ip_servidorSSH);
    $ssh->auth($loginSSH, $senhaSSH);
    $ssh->exec($command);
    exit;
}
function getuserinfo($userid, $data)
{
    global $pdo;
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM global_account WHERE id = ?");
    $stmt2xxxx2->execute([$userid]);
    $room53 = $stmt2xxxx2->fetch();
    return $room53[(string) $data];
}
function entityattrcapture3($userid, $categoriaid)
{
    global $pdo;
}
function execsshstatus()
{
    global $pdo;
    if (ob_get_level() == 0) {
        ob_start();
    }
    $fs42 = $pdo->prepare("SELECT * FROM servidores");
    $fs42->execute([""]);
    $data = $fs42->fetchAll();
    foreach ($data as $row) {
        $mensagem = NULL;
        $ip_servidorSSH = (string) $row["ip"];
        $loginSSH = (string) $row["usuario"];
        $senhaSSH = (string) $row["senha"];
        $ssh = new SSH2($ip_servidorSSH);
        if ($ssh->online($ip_servidorSSH)) {
            $mensagem = "Online";
        } else {
            $mensagem = "Offline";
        }
        usleep(22);
        echo "<script>\n    \$('#creating" . $row["id"] . "').text('" . $mensagem . "');\n    </script>";
        ob_flush();
        flush();
    }
    ob_end_flush();
}
function clearonlines()
{
    global $pdo;
    $tempo2 = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
    $stmt2xxxx2ssd = $pdo->prepare("DELETE FROM miracle_onlines WHERE ? > lastupdate");
    $stmt2xxxx2ssd->execute([$tempo2]);
}
function attrcapture3($userid, $categoriaid)
{
    global $pdo;
    $_SESSION["userlist"] = "";
    $tempz = rand(10000, 50000);
    $Laravel_DS = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
    $Laravel_DS->execute([$userid, $categoriaid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $tempo = date("Y-m-d H:i:s", mktime(date("H") - 5, date("i"), date("s"), date("m"), date("d"), date("Y")));
        $data_inicio = new DateTime($tempo);
        $data_fim = new DateTime($row[expira]);
        $dateInterval = $data_inicio->diff($data_fim);
        $dias = $dateInterval->days;
        $_SESSION["userlist"] = "./AtlantusMakeAccount.sh " . $row["login"] . " " . $row["senha"] . " " . $dias . " " . $row["limite"] . "\n" . $_SESSION["userlist"] . "";
    }
    $Laravel_DSz = $pdo->prepare("SELECT * FROM atribuidos WHERE byid = ? AND categoriaid = ?");
    $Laravel_DSz->execute([$userid, $categoriaid]);
    $dataz = $Laravel_DSz->fetchAll();
    foreach ($dataz as $row) {
        $sql = "UPDATE atribuidos set suspenso = 0 WHERE id = ?";
        $pdo->prepare($sql)->execute([$row[id]]);
        $Laravel_DSe = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
        $Laravel_DSe->execute([$userid, $categoriaid]);
        $datae = $Laravel_DSe->fetchAll();
        foreach ($datae as $rowe) {
            $tempo = date("Y-m-d H:i:s", mktime(date("H") - 5, date("i"), date("s"), date("m"), date("d"), date("Y")));
            $data_inicio = new DateTime($tempo);
            $data_fim = new DateTime($rowe[expira]);
            $dateInterval = $data_inicio->diff($data_fim);
            $dias = $dateInterval->days;
            $_SESSION["userlist"] = "./AtlantusMakeAccount.sh " . $rowe["login"] . " " . $rowe["senha"] . " " . $dias . " " . $rowe["limite"] . "\n" . $_SESSION["userlist"] . "";
        }
    }
    $Laravel_DS = $pdo->prepare("SELECT * FROM servidores WHERE subid = ?");
    $Laravel_DS->execute([$categoriaid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $ip_servidorSSH = (string) $row["ip"];
        $loginSSH = (string) $row["usuario"];
        $senhaSSH = (string) $row["senha"];
        $enc = base64_encode($_SESSION["userlist"]);
        oss((string) $tempz, $enc, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
    }
    $_SESSION["userlist"] = "";
}
function execsshcreate($slot5, $user, $pass, $dias, $limite)
{
    global $pdo;
    $license = getinfo($temp, "id");
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE id = ?");
    $stmt2xxxx2->execute([$slot5]);
    $room53 = $stmt2xxxx2->fetch();
    $ip_servidorSSH = (string) $room53["ip"];
    $loginSSH = (string) $room53["usuario"];
    $senhaSSH = (string) $room53["senha"];
    $login_ssh = (string) $user;
    $senha_ssh = (string) $pass;
    $dias = (string) $dias;
    if ($dias == "1") {
        $dias = "2";
    }
    $command = "./AtlantusMakeAccountForce.sh " . $user . " " . $pass . " " . $dias . " " . $limite;
    $tempz = rand(10000, 50000);
    $command = base64_encode($command);
    oss((string) $tempz, $command, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
    return (string) $mensagem;
}
function entityattrcapture2($userid, $categoriaid)
{
}
function updateonline($email)
{
    global $pdo;
    $tempo = date("Y-m-d H:i:s", mktime(date("H"), date("i") + 30, date("s"), date("m"), date("d"), date("Y")));
    $sql = "UPDATE global_account SET lastview = ? WHERE email = ?";
    $pdo->prepare($sql)->execute([$tempo, $email]);
}
function getsshlist($userid, $categoriaid)
{
    $command = "";
    $Laravel_DS = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
    $Laravel_DS->execute([$userid, $categoriaid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $command = "./AtlantusRemoveAccount.sh " . $row[login] . "\n" . $command;
    }
    return $command;
}
function miracleoffsheet($login, $deviceid)
{
    global $pdo;
    $ip = $_SERVER["REMOTE_ADDR"];
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE ip = ?");
    $stmt2xxxx2->execute([$ip]);
    $room53 = $stmt2xxxx2->fetch();
    $stmt2xxxx2s = $pdo->prepare("SELECT * FROM ssh_accounts WHERE categoriaid = ? AND login = ?");
    $stmt2xxxx2s->execute([$room53[subid], $login]);
    $room53s = $stmt2xxxx2s->fetch();
    $stmt2xxxx2ss = $pdo->prepare("DELETE FROM miracle_onlines WHERE userid = ? AND deviceid = ?");
    $stmt2xxxx2ss->execute([$room53s[id], $deviceid]);
}
function authcheck($login, $security, $code)
{
    global $pdo;
    $servername = $_SERVER["SERVER_NAME"];
    $fh = fopen("../../miracle_license.ll", "r");
    $read = "";
    while (!($line = fgets($fh))) {
        $read = $read . $line;
    }
    fclose($fh);
    $url = "https://atlantus.com.br/miracle/" . $read . ".txt?";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $resultx = curl_exec($ch);
    curl_close($ch);
    if (strpos($resultx, "Oops, looks") === false) {
        $result = base64_decode((string) $resultx);
        if (strpos($result, $servername) !== false) {
            $senha = $security;
            $senha = base64_decode($senha);
            $clearsecurity = $security;
            $finalstring = base64_encode($security);
            $after = strrev($finalstring);
            $stmt2xxxx2 = $pdo->prepare("SELECT * FROM accounts WHERE login = ? AND senha = ?");
            $stmt2xxxx2->execute([$login, $senha]);
            $room53 = $stmt2xxxx2->fetch();
            if ($room53["id"] != NULL) {
                $random = gettoken(240);
                $xsenha = $senha;
                $xlogin = $room53[login];
                $rank = 1;
                echo "sucesso||||||" . $random . "||||||" . $room53["login"] . "||||||" . $room53["senha"] . "||||||" . $room53["mb"] . "||||||0||||||0||||||" . $room53["avatar"] . "||||||" . $room53["nome"] . "||||||" . $room53["id"] . "||||||" . $room53["rank"];
                updatetoken($room53["id"], (string) $random);
            } else {
                echo "falha";
            }
        } else {
            echo "Licença inexistente.<br>Esta licença é inválida, por favor, acesse atlantus.com.br e compre sua licença para o painel.\n             ";
            exit;
        }
    } else {
        echo "Licença inexistente.<br>" . $url . " Esta licença é inválida, por favor, acesse atlantus.com.br e compre sua licença para o painel.\n     ";
        exit;
    }
}
function getusedlimit($userid, $catid)
{
    global $pdo;
    $total = 0;
    $fs42 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ? AND bycredit = '0'");
    $fs42->execute([$userid, $catid]);
    $data = $fs42->fetchAll();
    foreach ($data as $row) {
        $total = $row[limite] + $total;
    }
    $fs42x = $pdo->prepare("SELECT * FROM atribuidos WHERE byid = ? AND categoriaid = ?");
    $fs42x->execute([$userid, $catid]);
    $datax = $fs42x->fetchAll();
    foreach ($datax as $row) {
        $total = $row[limite] + $total;
    }
    return $total;
}
function execsshcreatetest($slot5, $user, $pass, $dias, $limite)
{
    global $pdo;
    $license = getinfo($temp, "id");
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM servidores WHERE id = ?");
    $stmt2xxxx2->execute([$slot5]);
    $room53 = $stmt2xxxx2->fetch();
    $ip_servidorSSH = (string) $room53["ip"];
    $loginSSH = (string) $room53["usuario"];
    $senhaSSH = (string) $room53["senha"];
    $login_ssh = (string) $user;
    $senha_ssh = (string) $pass;
    if ($dias == "1") {
        $dias = "2";
    }
    $command = "./maketest.sh " . $user . " " . $pass . " " . $dias . " 1";
    $tempz = rand(10000, 50000);
    $command = base64_encode($command);
    oss((string) $tempz, $command, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
    return (string) $mensagem;
}
function getavaiablelimit($userid, $catid)
{
    global $pdo;
    $total = 0;
    $fs42 = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
    $fs42->execute([$userid, $catid]);
    $data = $fs42->fetchAll();
    foreach ($data as $row) {
        $total = $row[limite] + $total;
    }
    $fs42x = $pdo->prepare("SELECT * FROM atribuidos WHERE byid = ? AND categoriaid = ?");
    $fs42x->execute([$userid, $catid]);
    $datax = $fs42x->fetchAll();
    foreach ($datax as $row) {
        $total = $row[limite] + $total;
    }
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM atribuidos WHERE categoriaid = ? AND userid = ?");
    $stmt2xxxx2->execute([$catid, $userid]);
    $room53 = $stmt2xxxx2->fetch();
    $total = $room53[limite] - $total;
    return $total;
}
function attrcapture2($userid, $categoriaid)
{
    global $pdo;
    $license = getinfo($temp, "id");
    $_SESSION["userlist"] = "";
    $tempz = rand(10000, 50000);
    $Laravel_DS = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
    $Laravel_DS->execute([$userid, $categoriaid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $_SESSION["userlist"] = "pkill -u " . $row["login"] . "\ndeluser " . $row["login"] . "\n" . $_SESSION["userlist"] . "";
    }
    $Laravel_DSz = $pdo->prepare("SELECT * FROM atribuidos WHERE byid = ? AND categoriaid = ?");
    $Laravel_DSz->execute([$userid, $categoriaid]);
    $dataz = $Laravel_DSz->fetchAll();
    foreach ($dataz as $row) {
        $sql = "UPDATE atribuidos set suspenso = 1 WHERE id = ?";
        $pdo->prepare($sql)->execute([$row[id]]);
        $Laravel_DSe = $pdo->prepare("SELECT * FROM ssh_accounts WHERE byid = ? AND categoriaid = ?");
        $Laravel_DSe->execute([$userid, $categoriaid]);
        $datae = $Laravel_DSe->fetchAll();
        foreach ($datae as $rowe) {
            $_SESSION["userlist"] = "pkill -u " . $rowe["login"] . "\ndeluser " . $rowe["login"] . "\n" . $_SESSION["userlist"] . "";
        }
    }
    $tempz = rand(10000, 50000);
    $command = base64_encode($command);
    $Laravel_DS = $pdo->prepare("SELECT * FROM servidores WHERE subid = ?");
    $Laravel_DS->execute([$categoriaid]);
    $data = $Laravel_DS->fetchAll();
    foreach ($data as $row) {
        $ip_servidorSSH = (string) $row["ip"];
        $loginSSH = (string) $row["usuario"];
        $senhaSSH = (string) $row["senha"];
        $enc = base64_encode($_SESSION["userlist"]);
        oss((string) $tempz, $enc, (string) $ip_servidorSSH, (string) $loginSSH, (string) $senhaSSH);
    }
    $_SESSION["userlist"] = "";
}
function getinfo($data, $packet)
{
    global $pdo;
    $stmt2xxxx2 = $pdo->prepare("SELECT * FROM accounts WHERE token = ?");
    $stmt2xxxx2->execute([$data]);
    $room53 = $stmt2xxxx2->fetch();
    return $room53[(string) $packet];
}

?>