<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID de l'utilisateur est passé en paramètre
if (!isset($_GET['id_utilisateur'])) {
    header('Location: manage-users.php'); // Redirige si l'ID n'est pas fourni
    exit();
}

$id_utilisateur = $_GET['id_utilisateur'];

// Récupérer les informations de l'utilisateur
$query = "SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
$statement = $connexion->prepare($query);
$statement->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
$statement->execute();

$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifiez le token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Token CSRF invalide.');
    }

    // Récupérer les données du formulaire
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $prenom_utilisateur = $_POST['prenom_utilisateur'];
    $login_utilisateur = $_POST['login']; // Changement ici pour correspondre à votre base de données

    // Vérifiez si un nouveau mot de passe a été fourni
    if (!empty($_POST['mot_de_passe'])) {
        $password = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    } else {
        // Si aucun mot de passe n'est fourni, ne pas mettre à jour ce champ
        $password = $user['password']; // Garder l'ancien mot de passe si aucun nouveau n'est fourni
    }

    // Mettre à jour les informations de l'utilisateur
    $updateQuery = "UPDATE utilisateur SET nom_utilisateur = :nom_utilisateur, prenom_utilisateur = :prenom_utilisateur, login = :login" . 
                   (!empty($_POST['mot_de_passe']) ? ", password = :password" : "") . 
                   " WHERE id_utilisateur = :id_utilisateur";
    $updateStatement = $connexion->prepare($updateQuery);
    $updateStatement->bindParam(':nom_utilisateur', $nom_utilisateur);
    $updateStatement->bindParam(':prenom_utilisateur', $prenom_utilisateur);
    $updateStatement->bindParam(':login', $login_utilisateur); // Changement ici pour correspondre à votre base de données

    // Lier le mot de passe uniquement s'il a été fourni
    if (!empty($_POST['mot_de_passe'])) {
        $updateStatement->bindParam(':password', $password);
    }

    $updateStatement->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $updateStatement->execute();

    // Rediriger vers la page de gestion des utilisateurs
    header('Location: manage-users.php');
    exit();
}

// Générer un token CSRF si ce n'est pas déjà fait
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Génère un token CSRF
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../css/normalize.css">
        <link rel="stylesheet" href="../../../css/styles-computer.css">
        <link rel="stylesheet" href="../../../css/styles-responsive.css">
        <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
        <title>Modifier Utilisateur - Jeux Olympiques - Los Angeles 2028</title>
        <style>
            form {
                max-width: 400px;
                margin: 20px auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            input[type="text"],
            input[type="password"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            button {
                background-color: #4CAF50;
                color: white;
                padding: 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }

            button:hover {
                background-color: #45a049;
            }

            .success {
                color: green;
            }

            .error {
                color: red;
            }
        </style>
    </head>
    <body>
        <header>
            <nav>
                <ul class="menu">
                    <li><a href="../admin.php">Accueil Administration</a></li>
                    <li><a href="manage-users.php">Gestion Utilisateurs</a></li>
                    <li><a href="manage-sports.php">Gestion Sports</a></li>
                    <li><a href="manage-places.php">Gestion Lieux</a></li>
                    <li><a href="manage-countries.php">Gestion Pays</a></li>
                    <li><a href="manage-events.php">Gestion Calendrier</a></li>
                    <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                    <li><a href="manage-results.php">Gestion Résultats</a></li>
                    <li><a href="../../logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>Modifier Utilisateur</h1>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="userID" value="<?php echo htmlspecialchars($user['id_utilisateur'], ENT_QUOTES, 'UTF-8'); ?>">

                <label for="nom_utilisateur">Nom :</label>
                <input type="text" id="nom_utilisateur" name="nom_utilisateur" value="<?php echo htmlspecialchars($user['nom_utilisateur'], ENT_QUOTES, 'UTF-8'); ?>" required>

                <label for="prenom_utilisateur">Prénom :</label>
                <input type="text" id="prenom_utilisateur" name="prenom_utilisateur" value="<?php echo htmlspecialchars($user['prenom_utilisateur'], ENT_QUOTES, 'UTF-8'); ?>" required>

                <label for="login">Nouveau Login :</label>
                <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8'); ?>" required>

                <label for="mot_de_passe">Nouveau Mot de Passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Laissez vide pour conserver l'ancien mot de passe">

                <input type="submit" value="Mettre à jour l'utilisateur">   
             </form>
        </main>

        <footer>
            <figure>
                <img src="../../../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
            </figure>
        </footer>
        
    </body>
</html>