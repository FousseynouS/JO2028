<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'ID du lieu est passé en paramètre
if (isset($_GET['id_lieu'])) {
    $id_lieu = $_GET['id_lieu'];

    // Récupérer les informations du lieu à modifier
    $query = "SELECT * FROM lieu WHERE id_lieu = :id_lieu";
    $statement = $connexion->prepare($query);
    $statement->bindParam(':id_lieu', $id_lieu, PDO::PARAM_INT);
    $statement->execute();

    // Vérifiez si le lieu existe
    if ($statement->rowCount() == 1) {
        $lieu = $statement->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Lieu non trouvé.";
        exit;
    }
} else {
    echo "ID de lieu non spécifié.";
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $nom_lieu = $_POST['nom_lieu'];
    $adresse_lieu = $_POST['adresse_lieu'];
    $cp_lieu = $_POST['cp_lieu'];
    $ville_lieu = $_POST['ville_lieu'];

    // Mettre à jour les informations du lieu dans la base de données
    $updateQuery = "UPDATE lieu SET nom_lieu = :nom_lieu, adresse_lieu = :adresse_lieu, cp_lieu = :cp_lieu, ville_lieu = :ville_lieu WHERE id_lieu = :id_lieu";
    $updateStatement = $connexion->prepare($updateQuery);
    $updateStatement->bindParam(':nom_lieu', $nom_lieu);
    $updateStatement->bindParam(':adresse_lieu', $adresse_lieu);
    $updateStatement->bindParam(':cp_lieu', $cp_lieu);
    $updateStatement->bindParam(':ville_lieu', $ville_lieu);
    $updateStatement->bindParam(':id_lieu', $id_lieu, PDO::PARAM_INT);

    if ($updateStatement->execute()) {
        // Rediriger vers la page de gestion des lieux après la mise à jour
        header("Location: manage-places.php?message=Lieu modifié avec succès");
        exit;
    } else {
        echo "Erreur lors de la mise à jour du lieu.";
    }
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
    <title>Modifier un Lieu</title>
</head>
<body>
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
<main>
    <h1>Modifier le Lieu</h1>
    <form action="" method="post">
        <label for="nom_lieu">Nom du Lieu:</label>
        <input type="text" id="nom_lieu" name="nom_lieu" value="<?php echo htmlspecialchars($lieu['nom_lieu'], ENT_QUOTES, 'UTF-8'); ?>" required><br>

        <label for="adresse_lieu">Adresse:</label>
        <input type="text" id="adresse_lieu" name="adresse_lieu" value="<?php echo htmlspecialchars($lieu['adresse_lieu'], ENT_QUOTES, 'UTF-8'); ?>" required><br>

        <label for="cp_lieu">Code Postal:</label>
        <input type="text" id="cp_lieu" name="cp_lieu" value="<?php echo htmlspecialchars($lieu['cp_lieu'], ENT_QUOTES, 'UTF-8'); ?>" required><br>
        <label for="ville_lieu">Ville:</label>
        <input type="text" id="ville_lieu" name="ville_lieu" value="<?php echo htmlspecialchars($lieu['ville_lieu'], ENT_QUOTES, 'UTF-8'); ?>" required><br>

        <input type="submit" value="Modifier le Lieu">
    </form>

    <p class="paragraph-link">
        <a class="link-home" href="manage-places.php">Retour à la gestion des lieux</a>

    </p>
</main>

</body>
</html>
