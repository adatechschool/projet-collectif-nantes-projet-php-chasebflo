<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour vérifier le rôle de l'utilisateur
function checkRole($requiredRole) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        header('Location: access_denied.php');
        exit();
    }
}
?>

