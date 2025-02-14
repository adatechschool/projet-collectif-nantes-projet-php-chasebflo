<?php
require 'databaseconnect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {

        $stmt = $pdo->prepare("DELETE FROM dechets_collectes WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: waste_list.php?success=1");
            exit();
        } else {
            echo "Erreur lors de la suppression.";
        }
    } catch (PDOException $e) {
        die("Erreur: " . $e->getMessage());
    }
} else {
    echo "ID invalide.";
}


try {
  
    $pdo->exec("ALTER TABLE dechets_collectes
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
                INSERT INTO benevoles_archives (id_original, nom, email, role, date_suppression)
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