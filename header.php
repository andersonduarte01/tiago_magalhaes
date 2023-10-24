<?php

date_default_timezone_set("America/Sao_Paulo");
session_start();
include "config/conexao.php";
$conexao = mysqli_connect($server, $user, $pass, $db);
if ($conexao->connect_error) {
    exit("Connection failed: " . $conexao->connect_error);
}
$stmt = $conexao->prepare("SELECT title FROM config");
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();
$titulo = $title;
echo "\r\n<head>\r\n    <meta charset=\"utf-8\" />\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\r\n    <link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"../../assets/img/apple-icon.png\">\r\n    <link rel=\"icon\" type=\"image/png\" href=\"../../assets/img/favicon.png\">\r\n    <title>\r\n        ";
echo $titulo;
echo "    </title>\r\n    <!--     Fonts and icons     -->\r\n    <link rel=\"stylesheet\" href=\"../../assets/vendors/mdi/css/materialdesignicons.min.css\">\r\n    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css\" />\r\n    <link id=\"pagestyle\" href=\"../../assets/css/soft-ui-dashboard.css?v=1.0.6\" rel=\"stylesheet\" />\r\n    <link id=\"pagestyle\" href=\"../../assets/css/admin.css\" rel=\"stylesheet\" />\r\n    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.min.css\">\r\n    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\">\r\n    <link href=\"https://fonts.googleapis.com/icon?family=Material+Icons\" rel=\"stylesheet\">\r\n    <script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.all.min.js\"></script>\r\n    \r\n</head>\r\n";

?>