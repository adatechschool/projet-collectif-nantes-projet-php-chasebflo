<?php
require 'databaseconnect.php';
require 'session_check.php';
require 'role_middleware.php';
checkRole('admin'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Fonction pour restaurer un bénévole
    function restoreBenevole($pdo, $id) {
        try {
            $stmt = $pdo->prepare("
                UPDATE benevoles 
                SET deleted_at = NULL 
                WHERE id = :id
            ");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Erreur restauration: " . $e->getMessage());
            return false;
        }
    }

    if (restoreBenevole($pdo, $id)) {
        // Rediriger vers la page précédente avec un message de succès
        header("Location: volunteer_list.php?success=1");
        exit;
    } else {
        // Rediriger avec un message d'erreur
        header("Location: volunteer_list.php?error=1");
        exit;
    }
}
?>
