<?php
session_start();
require 'databaseconnect.php';
require 'session_check.php';
require 'role_middleware.php';
checkRole('admin');


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    try {
        // Marquer le bénévole comme supprimé
        $stmt = $pdo->prepare("
            UPDATE benevoles 
            SET deleted_at = NOW() 
            WHERE id = :id AND deleted_at IS NULL
        ");
        $stmt->execute([':id' => $id]);
        
        header("Location: volunteer_list.php?success=1");
        exit();
        
    } catch (PDOException $e) {
        error_log("Erreur soft delete: " . $e->getMessage());
        die("Erreur lors de la suppression: " . $e->getMessage());
    }
} else {
    echo "ID invalide.";
}

// Fonction pour obtenir la liste des bénévoles actifs
function getActiveBenevoles($pdo) {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM benevoles 
        WHERE deleted_at IS NULL
        ORDER BY nom
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir la liste des bénévoles supprimés
function getDeletedBenevoles($pdo) {
    $stmt = $pdo->prepare("
        SELECT * 
        FROM benevoles 
        WHERE deleted_at IS NOT NULL
        ORDER BY nom
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>