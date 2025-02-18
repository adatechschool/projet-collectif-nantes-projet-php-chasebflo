<?php
require 'databaseconnect.php';
try {

    // Créer le trigger on clic de reactiver
    $pdo->exec("DROP TRIGGER IF EXISTS archive_benevole_before_activate");
    $pdo->exec("
        CREATE TRIGGER archive_benevole_before_activate
        BEFORE DELETE ON benevoles_archive
        FOR EACH ROW
        BEGIN
            INSERT INTO benevoles (id,nom,mot_de_passe, email, role)
            VALUES (id_original, nom, email,mot_de_passe, role);
        END
    ");
    
    // Vérifier si la colonne benevole_archive existe dans la table collectes
    $stmt = $pdo->prepare("SHOW COLUMNS FROM collectes LIKE 'benevole_archive'");
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        $pdo->exec("ALTER TABLE collectes ADD COLUMN benevoles BOOLEAN DEFAULT FALSE");
    }
} catch(PDOException $e) {
    error_log("Erreur configuration: " . $e->getMessage());
}
try {
    $stmt = $pdo->query("
        SELECT b.id, b.nom, b.email, b.role
        FROM benevoles_archive b
        ORDER BY b.nom ASC
    ");

    $query = $pdo->prepare("SELECT * FROM benevoles_archive ");
    $query->execute();
    $benevoles_archives = $stmt->fetchAll();


} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Bénévoles archivés</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
<div class="flex h-screen">
    <!-- Barre de navigation -->
    <div class="bg-green-950 text-white w-64 p-6 list-none">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
            <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i
                            class="fas fa-tachometer-alt mr-3"></i> Tableau de bord</a></li>
            <li><a href="collection_add.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i
                            class="fas fa-plus-circle mr-3"></i> Ajouter une collecte</a></li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i
                            class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
            <li>
                <a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
                </a>
            </li>
            <li>
                <a href="volunteer_archive_list.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Bénévoles archivés
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:green-blue-700 rounded-lg"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
                            <div class="mt-6">
    <a href="logout.php" class="w-full bg-red-700 hover:bg-red-500 text-white py-2 rounded-lg shadow-md">
        Déconnexion
    </a>
</div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="text-4xl font-bold text-green-950 mb-6">Liste des Bénévoles archivés</h1>

        <!-- Tableau des bénévoles -->
        <div class="overflow-hidden rounded-lg shadow-lg bg-white">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-green-950 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Nom</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Rôle</th>
                    <th class="py-3 px-4 text-left">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-300">
                <tr class="hover:bg-gray-100 transition duration-200">
                <?php
// Vérification s'il y a des données
if ($benevoles_archives) {
    // Boucle sur chaque bénévole
    foreach ($benevoles_archives as $benevole) {
        ?>
        <tr class="hover:bg-gray-100 transition duration-200">
            <td class="py-3 px-4"><?php echo htmlspecialchars($benevole['nom']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars($benevole['email']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars($benevole['role']); ?></td>
        
        <td class="py-3 px-4 flex space-x-2">
                        <a href="volunteer_archive_list.php?id=<?= $benevole['id'] ?>"
                           class="w-full bg-green-950 hover:bg-green-500 text-white text-center px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            ✏️ Activer
                        </a>
                    </td>
                    </tr>
        <?php
    }
}
// Fermeture du curseur
$stmt->closeCursor();
?>                
                    
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

