<?php
session_start();

$_SESSION = array();


//vider les cookies
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();

header("Location: login.php?logout");
exit();
?>