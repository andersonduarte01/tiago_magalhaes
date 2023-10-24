<?php

session_start();
define("SUCCESS_MESSAGE", "<div>Sistema atualizado com Sucesso!</div>");
define("ZIP_ERROR_MESSAGE", "<div>Não foi possível abrir o arquivo ZIP.</div>");
define("DOWNLOAD_ERROR_MESSAGE", "<div>Falha ao baixar o arquivo ZIP.</div>");
$zipUrl = "https://painelssh.com.br/upload/uploads/PAINEL-WEB-PRO.zip";
$zipFileName = basename($zipUrl);
if (!downloadfile($zipUrl, $zipFileName)) {
    $_SESSION["link2"] = DOWNLOAD_ERROR_MESSAGE;
    header("Location: ./home/admin.php");
    exit;
}
if (!extractzip($zipFileName)) {
    $_SESSION["link2"] = ZIP_ERROR_MESSAGE;
    header("Location: ./home/admin.php");
    exit;
}
deletefile($zipFileName);
$_SESSION["link3"] = SUCCESS_MESSAGE;
header("Location: ./home/admin.php");
exit;
function downloadFile($url, $path)
{
    $file = @file_get_contents($url);
    if ($file === false) {
        return false;
    }
    return @file_put_contents($path, $file) !== false;
}
function extractZip($path)
{
    $zip = new ZipArchive();
    if ($zip->open($path) === true) {
        $zip->extractTo("./");
        $zip->close();
        return true;
    }
    return false;
}
function deleteFile($path)
{
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

?>