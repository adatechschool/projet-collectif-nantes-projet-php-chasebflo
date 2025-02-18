<?php
session_start();
require 'databaseconnect.php';
// require 'role_middleware.php';
// checkRole('admin');

try {
    $stmt = $pdo->query("
        SELECT b.id, b.nom, b.email, b.role, b.deleted_at
        FROM benevoles b
        ORDER BY b.nom ASC
    ");

    $query = $pdo->prepare("SELECT * FROM benevoles ");
    $query->execute();
    $benevoles = $stmt->fetchAll();
    $admin = $query->fetch(PDO::FETCH_ASSOC);
    $adminNom = $admin ? htmlspecialchars($admin['nom']) : 'Aucun administrateur trouvé';

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
    <title>Liste des Bénévoles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-green-100 text-gray-900">
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
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i
                            class="fas fa-cogs mr-3"></i> Mon compte</a></li>
        <div class="mt-6">
            <button onclick="window.location.href='logout.php'" class="w-full bg-red-700 hover:bg-red-500 text-white py-2 rounded-lg shadow-md">
                Déconnexion
            </button>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="text-4xl font-bold text-green-950 mb-6">Liste des Bénévoles</h1>

        <!-- Tableau des bénévoles -->
        <div class="overflow-hidden rounded-lg shadow-lg bg-white">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-green-950 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Nom</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Rôle</th>
                    <th class="py-3 px-4 text-left">Actif/Inactif</th>
                    <th class="py-3 px-4 text-left">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-green-200">
                <tr class="hover:bg-green-200 transition duration-200">
                <?php
if ($benevoles) {
    // Boucle sur chaque bénévole
    foreach ($benevoles as $benevole) {
        ?>
        <tr class="hover:bg-green-100 transition duration-200">
            <td class="py-3 px-4"><?php echo htmlspecialchars($benevole['nom']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars($benevole['email']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars($benevole['role']); ?></td>
            
            <!-- Affichage du statut -->
            <td class="py-3 px-4">
                <?php if ($benevole['deleted_at'] === null) { ?>
                    <span class="w-full bg-green-950 hover:bg-green-500 text-white text-center px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">Actif</span>
                <?php } else { ?>
                    <span class="w-full bg-red-700 hover:bg-red-500 text-white text-center px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200">Inactif</span>
                <?php } ?>
            </td>
            
            <td class="py-3 px-4 flex space-x-2">
                <?php if ($benevole['deleted_at'] === null) { ?>
                    <a href="volunteer_edit_2.php?id=<?= $benevole['id'] ?>"
                       class="w-full bg-green-950 hover:bg-green-500 text-white text-center px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                        ✏️ Modifier
                    </a>
                    <a href="volunteer_delete.php?id=<?= $benevole['id'] ?>"
                       class="w-full bg-red-700 hover:bg-red-500 text-white text-center px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200">
                        🗑️ Supprimer
                    </a>
                <?php } else { ?>
                    <form method="POST" action="volunteer_restore.php">
                        <input type="hidden" name="id" value="<?php echo $benevole['id']; ?>">
                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-500 text-white text-center px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200">
                            Réactiver
                        </button>
                    </form>
                <?php } ?>
            </td>
        </tr>
    <?php }
} 
$stmt->closeCursor();
?>
        </tbody>
    </table>
</div>
</div>
</div>
</body>
</html>