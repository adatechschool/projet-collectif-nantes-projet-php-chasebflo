<?php
// Démarrer la session
session_start();
require 'databaseconnect.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['user_id'];
    // Vérifier que les mots de passe correspondent
    if ($new_password === $confirm_password) {
        if (strlen($new_password) >= 8) { // Vérifier que le mot de passe fait au moins 8 caractères
            try {
                // Hasher le nouveau mot de passe
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                // Mettre à jour le mot de passe dans la base de données
                $stmt = $pdo->prepare("UPDATE benevoles SET mot_de_passe = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $message = "Mot de passe mis à jour avec succès";
            } catch(PDOException $e) {
                $error = "Erreur lors de la mise à jour du mot de passe";
            }
        } else {
            $error = "Le mot de passe doit faire au moins 8 caractères";
        }
    } else {
        $error = "Les mots de passe ne correspondent pas";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Bénévole</title>
    <title>Ajouter un Bénévole</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
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
            <li><a href="user_add.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg">
                    <i class="fas fa-user-plus mr-3"></i> Ajouter un bénévole</a></li>
            <li><a href="my_account.php" class="flex items-center py-2 px-3 hover:bg-green-700 rounded-lg"><i
                        class="fas fa-cogs mr-3"></i> Mon compte</a></li>

                        <div class="mt-6">
    <a href="logout.php" class="block w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg shadow-md text-center">
        Déconnexion
    </a>
</div>
    </div>

    <!-- Contenu principal -->
    <div class="flex-1 p-8 overflow-y-auto">
        <!-- Titre -->
        <h1 class="text-4xl font-bold text-blue-800 mb-6">Paramètres</h1>

            <!-- Message de succès ou d'erreur -->
            <div class="text-green-950 text-center mb-4" id="success-message" style="display:none;">
                Vos paramètres ont été mis à jour avec succès.
            </div>
            <div class="text-red-700 text-center mb-4" id="error-message" style="display:none;">
                Le mot de passe actuel est incorrect.
            </div>

            <form id="settings-form" class="space-y-6">
                <!-- Champ Email -->
                <div>
                    <label for="email" class="block text-gray-700 font-medium">Email</label>
                    <input type="email" name="email" id="email" value="exemple@domaine.com" required
                        class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950">
                </div>

                <!-- Champ Mot de passe actuel -->
                <div>
                    <label for="current_password" class="block text-gray-700 font-medium">Mot de passe
                        actuel</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950">
                </div>

                <!-- Champ Nouveau Mot de passe -->
                <div>
                    <label for="new_password" class="block text-gray-700 font-medium">Nouveau mot de passe</label>
                    <input type="password" name="new_password" id="new_password"
                        class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950">
                </div>

                <!-- Champ Confirmer le nouveau Mot de passe -->
                <div>
                    <label for="confirm_password" class="block text-gray-700 font-medium">Confirmer le mot de
                        passe</label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950">
                </div>

                <!-- Boutons -->
                <div class="flex justify-between items-center">
                    <a href="collection_list.php" class="font-medium text-green-950 hover:underline">Retour à la liste des
                        collectes</a>
                    <button type="button" onclick="updateSettings()"
                        class="bg-green-950 hover:bg-green-500 text-white px-6 py-2 rounded-lg shadow-md">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
<<<<<<< HEAD
=======
        <div class="text-red-600 text-center mb-4" id="error-message" style="display:none;">
            Le mot de passe actuel est incorrect.
        </div>

        <form id="settings-form" class="space-y-6">
            <!-- Champ Email -->
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="<?= $email?>" required
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Champ Mot de passe actuel -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Mot de passe
                    actuel</label>
                <input type="password" name="current_password" id="current_password" required
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Champ Nouveau Mot de passe -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                <input type="password" name="new_password" id="new_password"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Champ Confirmer le nouveau Mot de passe -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de
                    passe</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Boutons -->
            <div class="flex justify-between items-center">
                <a href="collection_list.php" class="text-sm text-blue-600 hover:underline">Retour à la liste des
                    collectes</a>
                <button type="button" onclick="updateSettings()"
                        class="bg-cyan-200 hover:bg-cyan-600 text-white px-6 py-2 rounded-lg shadow-md">
                    Mettre à jour
                </button>
            </div>
        </form>
>>>>>>> 40c1070 (archivage ok)
    </div>
</body>

</html>