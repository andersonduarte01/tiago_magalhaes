<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
require_once "../../config/conexao.php";
if (!extension_loaded("ssh2")) {
    $_SESSION["configerr"] = "A extensão SSH2 não está instalada. Verifique sua configuração do PHP.";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}
if (!isset($_SESSION["iduser"]) || $_SESSION["nivel"] < 2) {
    header("location: ../../logout.php");
    exit;
}
if (!isset($_SESSION["login"]) || !isset($_SESSION["senha"])) {
    header("Location: ../../logout.php");
}
if (isset($_SESSION["LAST_ACTIVITY"]) && 300 < time() - $_SESSION["LAST_ACTIVITY"]) {
    header("location: ../../logout.php");
}
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$sql_servidor = "SELECT ip, porta, usuario, senha FROM servidores";
$stmt_servidor = $conexao->prepare($sql_servidor);
$stmt_servidor->execute();
$result_servidor = $stmt_servidor->get_result();
$servidores = [];
while ($row = $result_servidor->fetch_assoc()) {
    $servidores[] = $row;
}
if ($servidores) {
    foreach ($servidores as $servidor) {
        $dominio = $_SERVER["HTTP_HOST"];
        $file = fopen("../../home/modulos/online.sh", "w");
        $online_sh_code = "#!/bin/bash\r\n\r\n# Função para iniciar o loop\r\nstart_loop() {\r\n    while true; do\r\n        # Execute o comando 'ps aux | grep priv | grep Ss' no VPS e armazene a saída em \$output\r\n        output=\$(ps aux | grep priv | grep Ss)\r\n\r\n        # Divida a saída em linhas usando a função IFS\r\n        IFS=\$'\\n' read -rd '' -a lines <<<\"\$output\"\r\n\r\n        # Inicializar uma variável para armazenar os usuários online\r\n        user_list=\"\"\r\n\r\n        # Percorra as linhas da saída\r\n        for line in \"\${lines[@]}\"; do\r\n            # Ignore a linha se ela não contiver o processo \"priv\"\r\n            if [[ \$line != *\"priv\"* ]]; then\r\n                continue\r\n            fi\r\n\r\n            # Divida cada linha em colunas usando a função read\r\n            read -ra columns <<<\"\$line\"\r\n\r\n            # Obtenha o nome de usuário da coluna 11\r\n            username=\${columns[11]}\r\n\r\n            # Adicione o nome de usuário à lista separada por vírgulas\r\n            if [[ -z \"\$user_list\" ]]; then\r\n                user_list=\"\$username\"\r\n            else\r\n                user_list=\"\$user_list,\$username\"\r\n            fi\r\n        done\r\n\r\n        # Enviar a lista de usuários para o servidor remoto via POST e capturar a saída em uma variável\r\n        response=\$(curl -s -X POST -d \"users=\$user_list\" \"https://%s/online.php\")\r\n\r\n        # Separar as respostas em duas variáveis\r\n        limit_exceeded_response=\$(echo \"\$response\" | grep \"Limite atingido\")\r\n        validity_expired_response=\$(echo \"\$response\" | grep \"A validade expirou\")\r\n\r\n        # Verificar se o limite foi atingido\r\n        if [[ -n \"\$limit_exceeded_response\" ]]; then\r\n            # Extrair os nomes de usuário da resposta\r\n            limit_exceeded_users=\$(echo \"\$limit_exceeded_response\" | awk -F \": \" '{print \$2}')\r\n            echo \"Limite atingido para os seguintes usuários: \$limit_exceeded_users\"\r\n            # Executar o comando para remover os usuários\r\n            ./KillUser.sh \"\$limit_exceeded_users\"\r\n        fi\r\n\r\n        # Verificar se a validade expirou\r\n        if [[ -n \"\$validity_expired_response\" ]]; then\r\n            # Extrair os nomes de usuário da resposta\r\n            validity_expired_users=\$(echo \"\$validity_expired_response\" | awk -F \": \" '{print \$2}')\r\n            echo \"A validade expirou para os seguintes usuários: \$validity_expired_users\"\r\n            # Executar o script para lidar com a validade expirada\r\n            ./ExcluirExpiradoApi.sh \"\$validity_expired_users\"\r\n        fi\r\n\r\n        # Aguarde 3 segundos antes da próxima iteração\r\n        sleep 3\r\n    done\r\n}\r\n\r\n# Iniciar o loop diretamente sem verificar argumentos\r\nstart_loop";
        $online_sh_code = sprintf($online_sh_code, $dominio);
        fwrite($file, $online_sh_code);
        fclose($file);
        $arquivo = fopen("../../home/modulos/online.service", "w");
        $online_service_codigo = "[Unit]\r\nDescription=Serviço do Script Online\r\nAfter=network.target\r\n\r\n[Service]\r\nType=simple\r\nExecStart=/bin/bash /etc/systemd/system/online.sh\r\nRestart=always\r\nRestartSec=5\r\n\r\n[Install]\r\nWantedBy=multi-user.target";
        fwrite($arquivo, $online_service_codigo);
        fclose($arquivo);
        $ssh = ssh2_connect($servidor["ip"], $servidor["porta"]);
        if (ssh2_auth_password($ssh, $servidor["usuario"], $servidor["senha"])) {
            if (!ssh2_scp_send($ssh, "../../home/modulos/online.sh", "/etc/systemd/system/online.sh")) {
                $_SESSION["configerr"] = "Falha ao enviar o arquivo para o servidor.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_scp_send($ssh, "../../home/modulos/online.service", "/etc/systemd/system/online.service")) {
                $_SESSION["configerr"] = "Falha ao enviar o arquivo para o servidor.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_scp_send($ssh, "../../home/modulos/ExcluirExpiradoApi.sh", "/etc/systemd/system/ExcluirExpiradoApi.sh")) {
                $_SESSION["configerr"] = "Falha ao enviar o arquivo para o servidor.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_scp_send($ssh, "../../home/modulos/KillUser.sh", "/etc/systemd/system/KillUser.sh")) {
                $_SESSION["configerr"] = "Falha ao enviar o arquivo para o servidor.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            ssh2_exec($ssh, "sudo apt-get install dos2unix -y >/dev/null 2>&1 &");
            ssh2_exec($ssh, "dos2unix /etc/systemd/system/online.sh >/dev/null 2>&1 &");
            ssh2_exec($ssh, "dos2unix /etc/systemd/system/online.service >/dev/null 2>&1 &");
            ssh2_exec($ssh, "dos2unix /etc/systemd/system/KillUser.sh >/dev/null 2>&1 &");
            ssh2_exec($ssh, "dos2unix /etc/systemd/system/ExcluirExpiradoApi.sh >/dev/null 2>&1 &");
            if (!ssh2_sftp_chmod(ssh2_sftp($ssh), "/etc/systemd/system/online.sh", 493)) {
                $_SESSION["configerr"] = "Falha ao definir permissões no arquivo remoto.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_sftp_chmod(ssh2_sftp($ssh), "/etc/systemd/system/online.service", 493)) {
                $_SESSION["configerr"] = "Falha ao definir permissões no arquivo remoto.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_sftp_chmod(ssh2_sftp($ssh), "/etc/systemd/system/KillUser.sh", 493)) {
                $_SESSION["configerr"] = "Falha ao definir permissões no arquivo remoto.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            if (!ssh2_sftp_chmod(ssh2_sftp($ssh), "/etc/systemd/system/ExcluirExpiradoApi.sh", 493)) {
                $_SESSION["configerr"] = "Falha ao definir permissões no arquivo remoto.";
                header("Location: " . $_SERVER["HTTP_REFERER"]);
                exit;
            }
            ssh2_disconnect($ssh);
        } else {
            $_SESSION["configerr"] = "Falha ao conectar com o servidor " . $servidor["ip"];
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            exit;
        }
    }
    unlink("../../home/modulos/online.sh");
    unlink("../../home/modulos/online.service");
    $_SESSION["config"] = "<div>Módulos instalados com sucesso!</div>";
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}

?>