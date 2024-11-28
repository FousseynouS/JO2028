<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vider les messages de succès précédents
if (isset($_SESSION['success'])) {
    unset($_SESSION['success']);
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomUtilisateur = filter_input(INPUT_POST, 'nomUtilisateur', FILTER_SANITIZE_SPECIAL_CHARS);
    $prenomUtilisateur = filter_input(INPUT_POST, 'prenomUtilisateur', FILTER_SANITIZE_SPECIAL_CHARS);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Vérifiez si tous les champs sont remplis
    if (empty($nomUtilisateur) || empty($prenomUtilisateur) || empty($login) || empty($password)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-user.php");
        exit();
    }

    // Hachage du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Vérifiez si le login existe déjà
        $queryCheck = "SELECT id_utilisateur FROM UTILISATEUR WHERE login = :login";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":login", $login, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le login existe déjà.";
            header("Location: add-user.php");
            exit();
        }

        // Requête pour ajouter un nouvel utilisateur
        $query = "INSERT INTO UTILISATEUR (nom_utilisateur, prenom_utilisateur, login, password) 
                  VALUES (:nomUtilisateur, :prenomUtilisateur, :login, :password)";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomUtilisateur", $nomUtilisateur, PDO::PARAM_STR);
        $statement->bindParam(":prenomUtilisateur", $prenomUtilisateur, PDO::PARAM_STR);
        $statement->bindParam(":login", $login, PDO::PARAM_STR);
        $statement->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "L'utilisateur a été ajouté avec succès.";
            header("Location: manage-users.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de l'utilisateur.";
            header("Location: add-user.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-user.php");
        exit();
    }
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
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Ajouter un Utilisateur - Jeux Olympiques - Los Angeles 2028</title>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
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
        <h1>Ajouter un Utilisateur</h1>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <form action="add-user.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cet utilisateur ?')">
            <label for="nomUtilisateur">Nom :</label>
            <input type="text" name="nomUtilisateur" id="nomUtilisateur" required>
            <label for="prenomUtilisateur">Prénom :</label>
            <input type="text" name="prenomUtilisateur" id="prenomUtilisateur" required>
            <label for="login">Login :</label>
            <input type="text" name="login" id="login" required>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Ajouter l'Utilisateur">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-users.php">Retour à la gestion des utilisateurs</a>
        </p>
    </main>

    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
        </figure>
    </footer>
</body>

</html>
