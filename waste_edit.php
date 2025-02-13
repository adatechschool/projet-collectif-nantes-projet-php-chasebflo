<?php
require 'databaseconnect.php';

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: waste_list.php");
    exit;
}
$id = $_GET['id'];

// Récupérer les informations du déchet
$stmt = $pdo->prepare("SELECT * FROM dechets_collectes WHERE id = ?");
$stmt->execute([$id]);
$dechet = $stmt->fetch();

if (!$dechet) {
    header("Location: waste_list.php");
    exit;
}
// Traitement du formulaire de mise à jour
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST["type"];
    $quantite_kg = $_POST["quantite_kg"];
    $stmt = $pdo->prepare("UPDATE dechets_collectes SET type = ?, quantite_kg = ? WHERE id = ?");
    $stmt->execute([$type, $quantite_kg, $id]);
    header("Location: waste_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un déchet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="flex h-screen">
    <!-- Dashboard -->
    <div class="bg-cyan-200 text-white w-64 p-6">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
        <ul>
            <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                <i class="fas fa-tachometer-alt mr-3"></i> Tableau de bord</a>
            </li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                <i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a>
            </li>
            <li><a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a>
            </li>
            <li>
                <a href="waste_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Liste des déchets
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                <i class="fas fa-cogs mr-3"></i> Mon compte</a>
            </li>
        </ul>
        <div class="mt-6">
            <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg shadow-md">
                Déconnexion
            </button>
        </div>
    </div>
    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <h1 class="text-4xl font-bold text-blue-900 mb-6">Modifier un déchet</h1>
        <!-- Formulaire -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <form method="POST" class="space-y-4">
                <div>
                <label class="block text-sm font-medium text-gray-700">Type :</label>
                <select name="type_dechet"
                            class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="papier">Papier</option>
                        <option value="metal">Métal</option>
                        <option value="plastique">Plastique</option>
                        <option value="megots">Mégots</option>
                        <option value="bois">Bois</option>
                        <option value="verre">Verre</option>
                        <option value="dechets medicaux">Déchets médicaux</option>
                        <option value="dechets sanitaires">Déchets sanitaires</option>
                    </select>

                <label class="block text-sm font-medium text-gray-700">Quantité (kg) :</label>
                <input type="float" name="quantite_kg" 
                    value="<?= isset($dechet['quantite_kg']) ? htmlspecialchars($dechet['quantite_kg']) : '' ?>" 
                    required
                    class="w-full p-2 border border-gray-300 rounded-lg">
                </div>
                <div class="flex justify-end space-x-4">
                    <a href="waste_list.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Annuler</a>
                    <button type="submit" class="bg-cyan-200 text-white px-4 py-2 rounded-lg">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>