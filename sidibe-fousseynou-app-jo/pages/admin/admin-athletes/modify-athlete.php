<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'ID de l'athlète est passé en paramètre
if (isset($_GET['id_athlete'])) {
    $id_athlete = $_GET['id_athlete'];

    // Récupérer les informations de l'athlète à modifier
    $query = "SELECT ATHLETE.*, GENRE.nom_genre, PAYS.nom_pays 
              FROM ATHLETE
              JOIN GENRE ON ATHLETE.id_genre = GENRE.id_genre
              LEFT JOIN PAYS ON ATHLETE.id_pays = PAYS.id_pays
              WHERE ATHLETE.id_athlete = :id_athlete";
    $statement = $connexion->prepare($query);
    $statement->bindParam(':id_athlete', $id_athlete, PDO::PARAM_INT);
    $statement->execute();

    // Vérifiez si l'athlète existe
    if ($statement->rowCount() == 1) {
        $athlete = $statement->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Athlète non trouvé.";
        exit;
    }
} else {
    echo "ID d'athlète non spécifié.";
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $nom_athlete = $_POST['nom_athlete'];
    $prenom_athlete = $_POST['prenom_athlete'];
    $id_genre = $_POST['id_genre'];
    $id_pays = $_POST['id_pays'];

    // Mettre à jour les informations de l'athlète dans la base de données
    $updateQuery = "UPDATE ATHLETE SET nom_athlete = :nom_athlete, prenom_athlete = :prenom_athlete, id_genre = :id_genre, id_pays = :id_pays WHERE id_athlete = :id_athlete";
    $updateStatement = $connexion->prepare($updateQuery);
    $updateStatement->bindParam(':nom_athlete', $nom_athlete);
    $updateStatement->bindParam(':prenom_athlete', $prenom_athlete);
    $updateStatement->bindParam(':id_genre', $id_genre, PDO::PARAM_INT);
    $updateStatement->bindParam(':id_pays', $id_pays, PDO::PARAM_INT);
    $updateStatement->bindParam(':id_athlete', $id_athlete, PDO::PARAM_INT);

    if ($updateStatement->execute()) {
        // Rediriger vers la page de gestion des athlètes après la mise à jour
        header("Location: manage-athletes.php?message=Athlète modifié avec succès");
        exit;
    } else {
        echo "Erreur lors de la mise à jour de l'athlète.";
    }
}

// Récupération des genres et pays pour le formulaire
$genres = $connexion->query("SELECT * FROM GENRE")->fetchAll(PDO::FETCH_ASSOC);
$pays = $connexion->query("SELECT * FROM PAYS")->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Modifier un Athlète</title>
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
        <h1>Modifier l'Athlète</h1>
        <form action="" method="post">
            <label for="nom_athlete">Nom de l'Athlète :</label>
            <input type="text" id="nom_athlete" name="nom_athlete" value="<?php echo htmlspecialchars($athlete['nom_athlete'], ENT_QUOTES, 'UTF-8'); ?>" required><br>

            <label for="prenom_athlete">Prénom de l'Athlète :</label>
            <input type="text" id="prenom_athlete" name="prenom_athlete" value="<?php echo htmlspecialchars($athlete['prenom_athlete'], ENT_QUOTES, 'UTF-8'); ?>" required><br>

            <label for="id_genre">Genre :</label>
            <select id="id_genre" name="id_genre" required>
                <?php foreach ($genres as $genre) : ?>
                    <option value="<?php echo $genre['id_genre']; ?>" <?php if ($athlete['id_genre'] == $genre['id_genre']) echo 'selected'; ?>><?php echo htmlspecialchars($genre['nom_genre'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="id_pays">Pays :</label>
            <select id="id_pays" name="id_pays" required>
                <?php foreach ($pays as $pays) : ?>
                    <option value="<?php echo $pays['id_pays']; ?>" <?php if ($athlete['id_pays'] == $pays['id_pays']) echo 'selected'; ?>><?php echo htmlspecialchars($pays['nom_pays'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select><br>

            <input type="submit" value="Modifier l'Athlète">
        </form>

        <p class="paragraph-link">
            <a class="link-home" href="manage-athletes.php">Retour à la gestion des athlètes</a>
        </p>
    </main>

</body>
</html>