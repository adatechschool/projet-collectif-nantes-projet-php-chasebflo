<?php
require 'databaseconnect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Fonction pour réactiver un bénévole
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
        // rafraichir la page de la liste des benevoles
        header("Location: volunteer_list.php?success=1");
        exit;
    } else {
        // rafraichir la page de la liste des benevoles
        header("Location: volunteer_list.php?error=1");
        exit;
    }
}
?>
