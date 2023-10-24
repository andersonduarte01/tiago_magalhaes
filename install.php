<?php
date_default_timezone_set("America/Sao_Paulo");
session_start();
$file_path = "./config/conexao.php";

if (file_exists($file_path)) {
    require_once $file_path;
    header("Location: index.php");
    exit;
}

echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <title>$titulo</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.min.css">
    <link id="pagestyle" href="../../assets/css/soft-ui-dashboard.css?v=1.0.6" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.13/dist/sweetalert2.all.min.js"></script>
</head>
<body class="g-sidenav-show bg-gray-100">
    <style>
        .swal-popup {
            z-index: 9999999 !important;
        }
    </style>
    <style>
        .input-group-text {
            position: relative;
            z-index: 2;
        }
    </style>
    <main class="main-content d-flex position-relative max-height-vh-100 h-100 border-radius-lg justify-content-center align-items-center">
        <div class="container-fluid py-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center py-5">
                <center>
                    <div class="container-fluid py-5">
                        <div class="col-lg-12">
                            <a class="navbar-brand m-0">
                                <img src="/assets/img/logo.png" alt="Logo" width="auto" height="180">
                            </a>
HTML;

if (isset($_SESSION["erro"])) {
    echo "<script>Swal.fire({ icon: 'error', title: 'Erro!', html: '" . $_SESSION["erro"] . "', showConfirmButton: false, timer: 5000, customClass: { popup: 'swal-popup' } });</script>";
    unset($_SESSION["erro"]);
}

echo <<<HTML
                            <form class="login100-form validate-form" action="instalando.php" method="post">
                                <h2 class="card-title">Instalação</h2><br>
                                <div class="form-group mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text icon-wrapper" id="basic-addon1">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            </svg>
                                        </span>
                                        <input class="form-control" placeholder="Usuario do Banco de Dados" type="text" name="usuariodb" required>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text icon-wrapper" id="basic-addon1">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            </svg>
                                        </span>
                                        <input class="form-control" placeholder="Senha do Banco de Dados" type="text" name="senhadb" required>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text icon-wrapper" id="basic-addon1">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            </svg>
                                        </span>
                                        <input class="form-control" placeholder="Nome do Banco de Dados" type="text" name="bancodb" required>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text icon-wrapper" id="basic-addon1">
                                            <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            </svg>
                                        </span>
                                        <input class="form-control" value="localhost" type="text" name="hostdb" required>
                                    </div>
                                </div>
                                    <svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <input type="submit" name="instalar" id="instalar" class="btn btn-primary" value="Instalar">
                                <br>
                                <br>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="assets/js/soft-ui-dashboard.min.js?v=1.0.6"></script>
</body>
</html>
HTML;

?>
