<?php
session_start();
require 'databaseconnect.php';
require 'session_check.php';
require 'role_middleware.php';
checkRole('admin');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    try {
        // Démarrer une transaction
        $pdo->beginTransaction();
        
        // D'abord supprimer les déchets associés
        $stmt_dechets = $pdo->prepare("DELETE FROM dechets_collectes WHERE id_collecte = :id");
        $stmt_dechets->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_dechets->execute();
        
        // Ensuite supprimer la collecte
        $stmt_collecte = $pdo->prepare("DELETE FROM collectes WHERE id = :id");
        $stmt_collecte->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_collecte->execute();
        
        // Valider la transaction
        $pdo->commit();
        
        header("Location: collection_list.php?success=1");
        exit();
        
    } catch (PDOException $e) {
        // En cas d'erreur, annuler toutes les opérations
        $pdo->rollBack();
        die("Erreur: " . $e->getMessage());
    }
} else {
    echo "ID invalide.";
}
?>
