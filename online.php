<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "config/conexao.php";
header("Access-Control-Allow-Origin: *");
try {
    $userTracker = new UserTracker($_SERVER["REMOTE_ADDR"]);
    $userTracker->handle_request();
} catch (Exception $e) {
    error_log($e->getMessage());
}
$usuarios = [];
$dir = new DirectoryIterator("log/");
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $userTracker = new UserTracker($fileinfo->getBasename(".txt"));
        $usuarios = array_merge($usuarios, $userTracker->get_active_users());
    }
}
if (!empty($usuarios)) {
    try {
        $pdo = new PDO("mysql:host=" . $server . ";dbname=" . $db . ";charset=utf8", $user, $pass, [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $check_column_query = "SHOW COLUMNS FROM api_online LIKE 'status'";
        $stmt = $pdo->prepare($check_column_query);
        $stmt->execute();
        $column_exists = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$column_exists) {
            $alter_table_query = "ALTER TABLE api_online ADD COLUMN status VARCHAR(10) DEFAULT 'Off'";
            $pdo->exec($alter_table_query);
        }
        $usuarios_limite_atingido = [];
        $usuarios_validade_expirada = [];
        $usuarios_nao_encontrados = [];
        $contagem_usuarios_online = [];
        foreach ($usuarios as $nome_usuario) {
            if ($nome_usuario !== "root") {
                $stmt_user = $pdo->prepare("SELECT COUNT(*) AS count, byid, limite, expira FROM ssh_accounts WHERE login = ?");
                $stmt_user->execute([$nome_usuario]);
                $resultado_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
                if (0 < $resultado_user["count"]) {
                    $byid = $resultado_user["byid"];
                    $limite = $resultado_user["limite"];
                    $expira = $resultado_user["expira"];
                    $tempo_atual = date("Y-m-d H:i:s");
                    if ($expira && $expira < $tempo_atual) {
                        $usuarios_validade_expirada[] = $nome_usuario;
                    } else {
                        if (!isset($contagem_usuarios_online[$nome_usuario])) {
                            $contagem_usuarios_online[$nome_usuario] = 0;
                        }
                        $contagem_usuarios_online[$nome_usuario]++;
                        if ($limite < $contagem_usuarios_online[$nome_usuario]) {
                            $usuarios_limite_atingido[] = $nome_usuario;
                        }
                        $stmt = $pdo->prepare("SELECT * FROM api_online WHERE login = ?");
                        $stmt->execute([$nome_usuario]);
                        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $tempo_atual = date("Y-m-d H:i:s");
                        if (0 < count($resultado)) {
                            $limite_atualizado = $contagem_usuarios_online[$nome_usuario] . " | " . $limite;
                            $stmt = $pdo->prepare("UPDATE api_online SET limite = ?, byid = ?, status = 'On' WHERE login = ?");
                            $stmt->execute([$limite_atualizado, $byid, $nome_usuario]);
                        } else {
                            $tempo_inicio = date("Y-m-d H:i:s");
                            $limite_atualizado = $contagem_usuarios_online[$nome_usuario] . " | " . $limite;
                            $stmt = $pdo->prepare("INSERT INTO api_online (login, online, start_time, limite, byid, status) VALUES (?, ?, ?, ?, ?, 'On')");
                            $stmt->execute([$nome_usuario, $tempo_atual, $tempo_inicio, $limite_atualizado, $byid]);
                        }
                        $usuarios_encontrados[] = $nome_usuario;
                    }
                } else {
                    $usuarios_nao_encontrados[] = $nome_usuario;
                }
            }
        }
        $stmt_off = $pdo->prepare("UPDATE api_online SET status = 'Off' WHERE login NOT IN (" . rtrim(str_repeat("?,", count($usuarios)), ",") . ")");
        $stmt_off->execute($usuarios);
        $stmt_usuarios = $pdo->query("SELECT * FROM api_online WHERE status = 'Off'");
        $usuarios_offline = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);
        foreach ($usuarios_offline as $usuario) {
            $nome_usuario = $usuario["login"];
            $stmt = $pdo->prepare("DELETE FROM api_online WHERE login = ?");
            $stmt->execute([$nome_usuario]);
        }
        $stmt_servidores = $pdo->prepare("SELECT * FROM servidores");
        $stmt_servidores->execute();
        $servidores = $stmt_servidores->fetchAll(PDO::FETCH_ASSOC);
        foreach ($servidores as $servidor) {
            $ip = $servidor["ip"];
            $usuario_ssh = $servidor["usuario"];
            $porta = $servidor["porta"];
            $senha = $servidor["senha"];
            $conexao_ssh = ssh2_connect($ip, $porta);
            if (!$conexao_ssh) {
                throw new Exception("Falha ao conectar ao servidor SSH: " . $ip);
            }
            if (!ssh2_auth_password($conexao_ssh, $usuario_ssh, $senha)) {
                throw new Exception("Falha na autenticação SSH para o servidor: " . $ip);
            }
            if (!empty($usuarios_limite_atingido)) {
                $usuarios_para_remover = implode(" ", $usuarios_limite_atingido);
                $comando = "./KillUser.sh " . $usuarios_para_remover;
                $stream = ssh2_exec($conexao_ssh, $comando);
                stream_set_blocking($stream, true);
                $saida = stream_get_contents($stream);
                fclose($stream);
            }
            if (!empty($usuarios_validade_expirada)) {
                $usuarios_para_excluir = implode(" ", $usuarios_validade_expirada);
                $comando = "./ExcluirExpiradoApi.sh " . $usuarios_para_excluir;
                $stream = ssh2_exec($conexao_ssh, $comando);
                stream_set_blocking($stream, true);
                $saida = stream_get_contents($stream);
                fclose($stream);
            }
            ssh2_exec($conexao_ssh, "exit");
            unset($conexao_ssh);
        }
        $pdo = NULL;
    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    }
} else {
    echo "Lista de usuários vazia.";
}
class UserTracker
{
    private $file_path = NULL;
    private $online_users_data = NULL;
    public function __construct($ip)
    {
        $this->file_path = "log/" . $ip . ".txt";
        $this->online_users_data = file_exists($this->file_path) ? json_decode(file_get_contents($this->file_path), true) : [];
    }
    private function update_user_activity($user_list)
    {
        $this->online_users_data = ["users" => $user_list, "last_active" => time()];
        if (!file_put_contents($this->file_path, json_encode($this->online_users_data, JSON_PRETTY_PRINT))) {
            throw new Exception("Failed to write to the file.");
        }
    }
    public function get_active_users($timeout = 300)
    {
        $active_users = [];
        if (time() - $this->online_users_data["last_active"] <= $timeout) {
            $active_users = explode(",", $this->online_users_data["users"]);
        }
        return $active_users;
    }
    public function handle_request()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["users"])) {
            $user_list = $_POST["users"];
            $user_list_array = array_filter(explode(",", $user_list), function ($user) {
                return !in_array(strtolower(trim($user)), ["root", "unknown"]);
            });
            $user_list = implode(",", $user_list_array);
            $this->update_user_activity($user_list);
            return $user_list;
        }
        return "";
    }
}

?>