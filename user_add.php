<?php
session_start();
require 'session_check.php';
require 'role_middleware.php';
checkRole('admin');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Bénévole</title>
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
         <!-- Titre et nom de l'utilisateur --> 
         <div class="flex items-center justify-between mb-6">
         <h1 class="text-4xl font-bold text-green-950 mb-6">Ajouter un Bénévole</h1>
            <div class="text-lg text-gray-800">Bienvenue, <?= $_SESSION["nom"] ?> !</div>
        </div>

        <!-- Formulaire d'ajout -->
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
            <form action="user_add.php" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Nom</label>
                    <input type="text" name="nom"
                           class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950"
                           placeholder="Nom du bénévole" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Email</label>
                    <input type="email" name="email"
                           class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950"
                           placeholder="Email du bénévole" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Mot de passe</label>
                    <input type="password" name="mot_de_passe"
                           class="w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950"
                           placeholder="Mot de passe" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium">Rôle</label>
                    <select name="role"
                            class="appearance-none w-full mt-2 p-3 border border-green-950 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-950">
                        <option value="participant">Participant</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            class="w-full bg-green-950 hover:bg-green-500 text-white py-3 rounded-lg shadow-md font-semibold">
                        Ajouter le bénévole
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
try {
    
    include 'databaseconnect.php';
    include 'hash_password.php';
    // Variables à insérer
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $passwords = $_POST["mot_de_passe"]; // Hash du mot de passe
    $roles = $_POST["role"];

    // Préparation et exécution de la requête
    $stmt = $pdo->prepare("INSERT INTO benevoles (nom, email, mot_de_passe, role) VALUES (:nom, :email, :passwords, :roles)");
    
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':passwords' => password_hash($passwords, PASSWORD_DEFAULT),
        ':roles' => $roles
    ]);

    echo "Bénévole ajouté avec succès";
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}}
?>
