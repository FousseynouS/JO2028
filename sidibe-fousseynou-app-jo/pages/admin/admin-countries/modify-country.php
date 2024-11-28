<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Récupérer le nom du pays à modifier
if (isset($_GET['nom_pays'])) {
    $nom_pays = htmlspecialchars($_GET['nom_pays'], ENT_QUOTES, 'UTF-8');
} else {
    // Rediriger si aucun pays n'est spécifié
    header('Location: manage-countries.php');
    exit();
}

// Initialiser une variable pour stocker le message de succès ou d'erreur
$message = "";

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_nom = htmlspecialchars($_POST['nouveau_nom'], ENT_QUOTES, 'UTF-8');

    try {
        // Requête pour mettre à jour le nom du pays dans la base de données
        $query = "UPDATE pays SET nom_pays = :nouveau_nom WHERE nom_pays = :ancien_nom";
        $statement = $connexion->prepare($query);
        $statement->bindParam(':nouveau_nom', $nouveau_nom);
        $statement->bindParam(':ancien_nom', $nom_pays);
        $statement->execute();

        // Message de succès
        $_SESSION['success'] = "Le pays a été modifié avec succès.";
        header('Location: manage-countries.php');
        exit();
    } catch (PDOException $e) {
        $message = "Erreur lors de la mise à jour du pays : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Pays</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">  
</head>
<body>
    <header>
        <nav>
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Épreuves</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Modifier le Pays</h1>
        <?php
        // Afficher le message d'erreur, s'il y en a
        if ($message) {
            echo "<p style='color: red;'>" . $message . "</p>";
        }
        ?>
        <form action="" method="post">
            <label for="nouveau_nom">Nouveau Nom du Pays :</label>
            <input type="text" id="nouveau_nom" name="nouveau_nom" required>
            <input type="submit" value="Modifier le Lieu">
            </form>

            <p class="paragraph-link">
        <a class="link-home" href="manage-countries.php">Retour à la gestion des pays</a>

    </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
        </figure>
    </footer>   
</body>
</html>