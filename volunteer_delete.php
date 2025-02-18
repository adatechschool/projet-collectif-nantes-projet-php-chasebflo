<?php
session_start();
require 'databaseconnect.php';
require 'role_middleware.php';
checkRole('admin');

try {
    $pdo->exec("DROP TRIGGER IF EXISTS archive_benevole_before_delete");
    $pdo->exec("
        CREATE TRIGGER archive_benevole_before_delete
        BEFORE DELETE ON benevoles
        FOR EACH ROW
        BEGIN
            INSERT INTO benevoles_archive (id_original, nom, email, role, date_suppression)
            VALUES (OLD.id, OLD.nom, OLD.email, OLD.role, NOW());
        END
    ");
} catch(PDOException $e) {
    error_log("Erreur création trigger: " . $e->getMessage());
}

try {
    // Vérifier si la colonne existe déjà
    $stmt = $pdo->prepare("SHOW COLUMNS FROM collectes LIKE 'benevole_archive'");
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        // Si la colonne n'existe pas, on la crée
        $pdo->exec("ALTER TABLE collectes ADD COLUMN benevole_archive BOOLEAN DEFAULT FALSE");
    }
} catch(PDOException $e) {
    error_log("Erreur création colonne: " . $e->getMessage());
}

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