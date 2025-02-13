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

    // Récupérer la liste des bénévoles
    $stmt_benevoles = $pdo->prepare("SELECT id, nom FROM benevoles ORDER BY nom");
    $stmt_benevoles->execute();
    $benevoles = $stmt_benevoles->fetchAll();

} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si un ID de collecte est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: collection_list.php");
    exit;
}

$id = $_GET['id'];

// Récupérer les informations de la collecte
$stmt = $pdo->prepare("SELECT * FROM collectes WHERE id = ?");
$stmt->execute([$id]);
$collecte = $stmt->fetch();

if (!$collecte) {
    header("Location: collection_list.php");
    exit;
}

// Dans la partie de mise à jour (section POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST["date"];
    $lieu = $_POST["lieu"];
    $benevole_id = $_POST["benevole"];
    $quantite_kg = $_POST["quantite_kg"];
    $type_dechet = $_POST["type_dechet"];

    // Commencer une transaction
    $pdo->beginTransaction();
    try {
        // Mise à jour de la collecte
        $stmt = $pdo->prepare("UPDATE collectes SET date_collecte = ?, lieu = ?, id_benevole = ? WHERE id = ?");
        $stmt->execute([$date, $lieu, $benevole_id, $id]);

        // Vérifier si un enregistrement existe déjà dans dechets_collectes
        $check_stmt = $pdo->prepare("SELECT id FROM dechets_collectes WHERE id_collecte = ?");
        $check_stmt->execute([$id]);
        
        if ($check_stmt->rowCount() > 0) {
            // Update si existe
            $stmt_dechets = $pdo->prepare("UPDATE dechets_collectes SET type_dechet = ?, quantite_kg = ? WHERE id_collecte = ?");
            $stmt_dechets->execute([$type_dechet, $quantite_kg, $id]);
        } else {
            // Insert si n'existe pas
            $stmt_dechets = $pdo->prepare("INSERT INTO dechets_collectes (type_dechet, quantite_kg, id_collecte) VALUES (?, ?, ?)");
            $stmt_dechets->execute([$type_dechet, $quantite_kg, $id]);
        }

        // Valider la transaction
        $pdo->commit();
        header("Location: collection_list.php");
        exit;
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une collecte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <div class="flex h-screen">
        <!-- Dashboard -->

        <div class="bg-green-950 text-white w-64 p-6">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

            <li><a href="collection_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i class="fas fa-tachometer-alt mr-3"></i> Tableau de bord</a></li>
            <li><a href="volunteer_list.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i class="fa-solid fa-list mr-3"></i> Liste des bénévoles</a></li>
            <li>
                <a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole
                </a>
            </li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-blue-800 rounded-lg"><i class="fas fa-cogs mr-3"></i> Mon compte</a></li>

            <div class="mt-6">
                <button onclick="logout()" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg shadow-md">
                    Déconnexion
                </button>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-blue-900 mb-6">Modifier une collecte</h1>

            <!-- Formulaire -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date :</label>
                        <input type="date" name="date" value="<?= htmlspecialchars($collecte['date_collecte']) ?>" required
                            class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lieu :</label>
                        <input type="text" name="lieu" value="<?= htmlspecialchars($collecte['lieu']) ?>" required
                            class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bénévole :</label>
                        <select name="benevole" required
                            class="w-full p-2 border border-gray-300 rounded-lg">
                            <option value="" disabled selected>Sélectionnez un·e bénévole</option>
                            <?php foreach ($benevoles as $benevole): ?>
                                <option value="<?= $benevole['id'] ?>" <?= $benevole['id'] == $collecte['id_benevole'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($benevole['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Type de déchets -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Type de déchets</label>
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
                    </div>

                    <!-- Quantité en kg -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantité en kg :</label>
                        <input type="float" name="quantite_kg" required
                            class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="collection_list.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Annuler</a>
                        <button type="submit" class="bg-cyan-200 text-white px-4 py-2 rounded-lg">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>