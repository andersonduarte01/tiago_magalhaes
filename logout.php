<?php


session_start();
if (isset($_SESSION["iduser"])) {
    clearsessionvariables();
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    clearstatcache();
}
redirectto("index.php");
function clearSessionVariables()
{
    $_SESSION["login"] = NULL;
    $_SESSION["senha"] = NULL;
    $_SESSION["iduser"] = NULL;
    $_SESSION["byid"] = NULL;
    $_SESSION["mainid"] = NULL;
    $_SESSION["nivel"] = NULL;
    $_SESSION["nivel"] = NULL;
    $_SESSION["iduser"] = NULL;
}
function redirectTo($page)
{
    header("Location: " . $page);
    exit;
}

?>