<?php
session_start();
require 'config.php';

// Si l'utilisateur est déjà connecté, rediriger vers collection_list.php
if (isset($_SESSION["user_id"])) {
    header("Location: collection_list.php");
    exit;
}

// Initialisation des variables
$error = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Nettoyage et validation des entrées
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            // Vérifier si l'utilisateur existe dans la table `benevoles`
            $stmt = $pdo->prepare("SELECT * FROM benevoles WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Vérification du mot de passe
            if ($user && password_verify($password, $user['mot_de_passe'])) {

                   // Vérification si le compte est actif
    if ($user['deleted_at'] !== NULL) {
        $error = "Ce compte utilisateur n'est pas actif.";
        sleep(1);
    } else {
                 // Protection contre la fixation de session
        session_regenerate_id(true);

        // Stockage des informations en session
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["nom"] = $user["nom"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["last_activity"] = time();

        // Redirection
        header("Location: collection_list.php");
        exit;
    }
} else {
    $error = "Identifiants incorrects";
    // Délai pour prévenir le brute force
    sleep(1);
}
        } catch (PDOException $e) {
            // Log l'erreur de manière sécurisée
            error_log("Erreur de connexion : " . $e->getMessage());
            $error = "Une erreur est survenue. Veuillez réessayer plus tard.";
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96">
            <h1 class="text-3xl font-bold text-blue-900 mb-6 text-center">Connexion</h1>

            <?php if (!empty($error)) : ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6" autocomplete="off">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?= htmlspecialchars($email) ?>"
                        required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex justify-between items-center">
                    <a href="hash_password.php" class="text-sm text-blue-600 hover:underline">
                        Mot de passe oublié ?
                    </a>
                    <button
                        type="submit"
                        class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded-lg shadow-md transition duration-200">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>