<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "./config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION["login"])) {
    header("Location: nivel.php");
    exit;
}
if (!file_exists("./config/conexao.php")) {
    echo "<script>window.location.href = \"install.php\";</script>";
}
if (isset($_POST["login"]) && isset($_POST["senha"])) {
    include "login.php";
}
$query = "SHOW COLUMNS FROM config LIKE 'logo'";
$result = mysqli_query($conexao, $query);
if (mysqli_num_rows($result) == 0) {
    $query = "ALTER TABLE config ADD logo VARCHAR(300) DEFAULT 'https://i.imgur.com/mqpTJPZ.png'";
    if (!mysqli_query($conexao, $query)) {
        echo "Error adding column: " . mysqli_error($conexao);
    }
} else {
    $query = "SELECT logo FROM config";
    $result = mysqli_query($conexao, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $logo = $row["logo"];
        mysqli_free_result($result);
        if (empty($logo)) {
            $query = "UPDATE config SET logo = 'https://i.imgur.com/mqpTJPZ.png'";
            if (!mysqli_query($conexao, $query)) {
                echo "Error updating logo: " . mysqli_error($conexao);
            }
        }
    }
}
$sql = "CREATE TABLE IF NOT EXISTS miracle_deviceid (\n    id INT(11) NOT NULL AUTO_INCREMENT,\n    userid INT(11) NOT NULL,\n    byid INT(11) NOT NULL,\n    device VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,\n    PRIMARY KEY (id)\n)";
if ($conexao->query($sql) !== true) {
    echo "Error creating table: " . $conexao->error;
}
$stmt = $conexao->prepare("SELECT logo, title FROM config");
$stmt->execute();
$stmt->bind_result($logo, $title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
$logo = $logo;
date_default_timezone_set("America/Sao_Paulo");
$dataAtual = date("d");
$MesAtual = date("m");
$HoraAtual = date("H:i");
$diaSemana = date("w");
$nomesDias = ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"];
$nomeDiaAtual = $nomesDias[$diaSemana];
echo "<!DOCTYPE html>\n<link rel=\"icon\" type=\"image/png\" href=\"assets/img/favicon.png\">\n\n<html>\n<head>\n    <meta charset=\"utf-8\" />\n    <title>";
echo $titulo;
echo "</title>\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n    <meta name=\"mobile-web-app-capable\" content=\"yes\" />\n    <link rel=\"stylesheet\" href=\"./assets/css/novo.css\" />\n    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.all.min.js\"></script>\n    <style>\n    </style>\n\n</head>\n<body>\n    <div class=\"login\">\n        <img src=\"";
echo $logo;
echo "\" height=\"180\" width=\"auto\" />";
if (isset($_SESSION["loginerro"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["loginerro"] . "', showConfirmButton: false, timer: 2000 });</script>";
    unset($_SESSION["loginerro"]);
}
echo "\n        <h2>";
echo $HoraAtual;
echo "</h2>\n        <p>";
echo $dataAtual . "/" . $MesAtual . " " . $nomeDiaAtual;
echo "</p>\n        <form method=\"post\" action=\"verificar.php\">\n            <input type=\"text\" id=\"login\" name=\"login\" placeholder=\"Digite seu usuário\" required=\"required\" />\n            <input type=\"password\" id=\"senha\" name=\"senha\" placeholder=\"Digite sua senha\" required=\"required\" />\n            <button type=\"submit\" class=\"btn btn-primary btn-block btn-large\">Logar</button>\n        </form>\n    </div>\n    <div class=\"mainbg\">\n        <div class=\"ocean\">\n            <div class=\"wave\"></div>\n            <div class=\"wave\"></div>\n        </div></div>\n\n        <script>\n            document.addEventListener('contextmenu', function (event) {\n                event.preventDefault();\n            });\n        </script>\n\n    </body>\n    </html>\n\n";

?>