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
    $nomLieu = filter_input(INPUT_POST, 'nomLieu', FILTER_SANITIZE_SPECIAL_CHARS);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_SPECIAL_CHARS);
    $codePostal = filter_input(INPUT_POST, 'codePostal', FILTER_SANITIZE_STRING);
    $ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_SPECIAL_CHARS);

    // Vérifiez si tous les champs sont remplis
    if (empty($nomLieu) || empty($adresse) || empty($codePostal) || empty($ville)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-place.php");
        exit();
    }
    try {
        // Requête pour ajouter un nouveau lieu
        $query = "INSERT INTO LIEU (nom_lieu, adresse_lieu, cp_lieu, ville_lieu) 
                  VALUES (:nomLieu, :adresse, :codePostal, :ville)";
        $statement = $connexion->prepare($query);
        
        // Lier les paramètres
        $statement->bindParam(":nomLieu", $nomLieu, PDO::PARAM_STR);
        $statement->bindParam(":adresse", $adresse, PDO::PARAM_STR); // Corrigé ici
        $statement->bindParam(":codePostal", $codePostal, PDO::PARAM_STR); // Corrigé ici
        $statement->bindParam(":ville", $ville, PDO::PARAM_STR);
    
        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Le lieu a été ajouté avec succès.";
            header("Location: manage-places.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout du lieu.";
            header("Location: add-place.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-place.php");
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
    <title>Ajouter un Lieu - Jeux Olympiques - Los Angeles 2028</title>
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
            <h1>Ajouter un Lieu</h1>

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

            <form action="add-place.php" method="post"
                onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce lieu ?')">
                <label for="nomLieu">Nom du Lieu :</label>
                <input type="text" name="nomLieu" id="nomLieu" required>

                <label for="adresse">Adresse :</label>
                <input type="text" name="adresse" id="adresse" required>

                <label for="codePostal">Code Postal :</label>
                <input type="text" name="codePostal" id="codePostal" required>

                <label for="ville">Ville :</label>
                <input type="text" name="ville" id="ville" required>

                <input type="submit" value="Ajouter le Lieu">
            </form>

            <p class="paragraph-link">
                <a class="link-home" href="manage-places.php">Retour à la gestion des lieux</a>
            </p>
        </main>

        <footer>
            <figure>
                <img src="../../../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
            </figure>
        </footer>
    </body>
</html>