<?php
require 'databaseconnect.php';

try {
    // Requête pour récupérer les collectes avec leurs déchets et bénévoles associés
    $stmt = $pdo->query("
        SELECT c.id, c.date_collecte, c.lieu, b.nom,
               d.type_dechet, d.quantite_kg
        FROM collectes c
        LEFT JOIN benevoles b ON c.id_benevole = b.id
        LEFT JOIN dechets_collectes d ON c.id = d.id_collecte
        ORDER BY c.date_collecte DESC
    ");
    
    // Requête pour la liste des bénévoles
    $stmt_benevoles = $pdo->query("SELECT id, nom FROM benevoles ORDER BY nom");
    $benevoles = $stmt_benevoles->fetchAll();

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $lieu = $_POST["lieu"];
    $benevole_id = $_POST["benevole"];
    $quantite_kg = $_POST["quantite_kg"];
    $type_dechet = $_POST["type_dechet"];
    
    try {
        // Commencer une transaction
        $pdo->beginTransaction();
        
        // Insérer la collecte
        $stmt_collecte = $pdo->prepare("INSERT INTO collectes (date_collecte, lieu, id_benevole) VALUES (?, ?, ?)");
        $stmt_collecte->execute([$date, $lieu, $benevole_id]);
        
        // Récupérer l'ID de la collecte insérée
        $collecte_id = $pdo->lastInsertId();
        
        // Insérer les informations de déchets
        $stmt_dechets = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)");
        $stmt_dechets->execute([$collecte_id, $type_dechet, $quantite_kg]);
        
        // Valider la transaction
        $pdo->commit();
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }

    header("Location: collection_list.php");
    exit;
}
?>

<!-- Reste du code HTML -->


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une collecte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">

<div class="flex h-screen">
    <div class="bg-green-950 text-white w-64 p-6 list-none">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

            <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i class="fas fa-tachometer-alt mr-3"></i> Tableau de bord</a></li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
            <li>
                <a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>

        <div class="mt-6">
            <button onclick="window.location.href='logout.php'" class="w-full bg-red-700 hover:bg-red-500 text-white py-2 rounded-lg shadow-md">
                Déconnexion
            </button>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="text-4xl font-bold text-green-950 mb-6">Ajouter une collecte</h1>

        <!-- Formulaire -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <form method="POST" class="space-y-4">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date :</label>
                    <input type="date" name="date" required
                           class="w-full p-2 border border-green-950 rounded-lg focus:ring-green-950 focus:border-green-950">
                </div>

                <!-- Lieu -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lieu :</label>
                    <input type="text" name="lieu" required
                           class="w-full p-2 border border-green-950 rounded-lg focus:ring-green-950 focus:border-green-950">
                </div>

                <!-- Bénévole responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bénévole Responsable :</label>
                    <select name="benevole" required
                            class="w-full p-2 border border-green-950 rounded-lg focus:ring-green-950 focus:border-green-950">
                        <option value="">Sélectionner un bénévole</option>
                        <?php foreach ($benevoles as $benevole): ?>
                            <option value="<?= $benevole['id'] ?>" <?= $benevole['id'] ==  'selected' ?>>
                                <?= htmlspecialchars($benevole['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                  <!-- Type de déchets -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Type de déchets</label>
                    <select name="type_dechet"
                            class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950">
                        <option value="papier">Papier</option>
                        <option value="metal">Métal</option>
                        <option value="plastique">Plastique</option>
                        <option value="megots">Mégots</option>
                        <option value="bois">Bois</option>
                        <option value="verre">Verre</option>
                        <option value="dechets medicaux">Déchets médicaux</option>
                        <option value="dechets sanitaires">Déchets sanitaires</option>
                    </select>
                </div>
                   <!-- Quantité en kg -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantité en kg :</label>
                    <input type="float" name="quantite_kg" required
                           class="w-full p-2 border border-green-950 rounded-lg focus:ring-green-950 focus:border-green-950">
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4">
                    <a href="collection_list.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow">Annuler</a>
                    <button type="submit" class="bg-green-950 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                        ➕ Ajouter
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

</body>
</html>
