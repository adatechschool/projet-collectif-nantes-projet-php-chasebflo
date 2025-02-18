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
        $pdo->beginTransaction();
        
        // Mettre à jour les collectes
        $stmt = $pdo->prepare("
            UPDATE collectes
            SET benevole_archive = TRUE
            WHERE id_benevole = :id
        ");
        $stmt->execute([':id' => $id]);
        
        // Supprimer le bénévole (le trigger s'occupera de l'archivage)
        $stmt = $pdo->prepare("DELETE FROM benevoles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $pdo->commit();
        header("Location: volunteer_list.php?success=1");
        exit();
       
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erreur: " . $e->getMessage());
    }
} else {
    echo "ID invalide.";
}

try {
    // Créer une nouvelle table
    $pdo->exec("CREATE TABLE IF NOT EXISTS benevoles_archive (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_original INT NOT NULL,
        nom VARCHAR(255),
        email VARCHAR(255),
        role VARCHAR(100),
        date_suppression DATETIME
    )");

    $pdo->exec("ALTER TABLE collectes
        DROP FOREIGN KEY collectes_ibfk_1,
        ADD FOREIGN KEY (id_benevole),
        REFERENCES benevoles(id),
        ON DELETE SET NULL -- Met la référence à NULL quand le bénévole est supprimé
        )");
    
    // Ajouter une colonne
    $pdo->exec("ALTER TABLE collectes ADD COLUMN benevole_archive BOOLEAN DEFAULT FALSE");
    
    echo "Tables et colonnes créées avec succès";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        $pdo->beginTransaction();

        // 1. D'abord, récupérer les données du bénévole
        $stmt = $pdo->prepare("SELECT * FROM benevoles WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $benevole = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($benevole) {
            // 2. Copier les données dans la table d'archive
            $stmt = $pdo->prepare("
                INSERT INTO benevoles_archive (id_original, nom, email, role, date_suppression)
                VALUES (:id, :nom, :email, :role, NOW())
            ");
            $stmt->execute([
                ':id' => $benevole['id'],
                ':nom' => $benevole['nom'],
                ':email' => $benevole['email'],
                ':role' => $benevole['role']
            ]);

            // 3. Mettre à jour les collectes pour indiquer que le bénévole est archivé
            $stmt = $pdo->prepare("
                UPDATE collectes 
                SET benevole_archive = TRUE 
                WHERE id_benevole = :id
            ");
            $stmt->execute([':id' => $id]);

            // 4. Supprimer le bénévole de la table principale
            $stmt = $pdo->prepare("DELETE FROM benevoles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            


            $pdo->commit();
            header("Location: volunteer_list.php?success=1");
            exit();
        }
        

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erreur: " . $e->getMessage());
    }
} else {
    echo "ID invalide.";
}
?>