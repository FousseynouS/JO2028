<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Affichage des messages de succès ou d'erreur
if (isset($_SESSION['success'])) {
    echo "<p style='color: green;'>" . htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') . "</p>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
    unset($_SESSION['error']);
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
    <link rel="stylesheet" href="../../../css/manage.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Gestion des Pays - Jeux Olympiques - Los Angeles 2028</title>
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
        <h1>Gestion des Pays</h1>
        <div class="action-buttons">
            <button onclick="window.location.href='add-country.php'">Ajouter un Pays</button>
        </div>

        <!-- Tableau des pays -->
        <?php
        try {
            // Requête pour récupérer la liste des pays depuis la base de données
            $query = "SELECT nom_pays FROM pays ORDER BY nom_pays";
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table><tr><th>Nom du Pays</th><th>Modifier</th><th>Supprimer</th></tr>";

                // Afficher les données dans un tableau
// Afficher les données dans un tableau
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['nom_pays'], ENT_QUOTES, 'UTF-8') . "</td>";
    
    // Bouton Modifier
    echo "<td>
            <form action='modify-country.php' method='get' style='display:inline;'>
                <input type='hidden' name='nom_pays' value='" . htmlspecialchars($row['nom_pays'], ENT_QUOTES, 'UTF-8') . "'>
                <button type='submit'>Modifier</button>
            </form>
            </td>";
    echo"<td>            
            <form action='delete-country.php' method='post' style='display:inline;'>
                <input type='hidden' name='nom_pays' value='" . htmlspecialchars($row['nom_pays'], ENT_QUOTES, 'UTF-8') . "'>
                <button type='submit' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce pays ?\");'>Supprimer</button>
            </form>
          </td>";
    echo "</tr>";
}
                echo "</table>";
            } else {
                echo "<p>Aucun pays trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des pays : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
        ?>
    </main>

    <footer>
        <figure>
            <img src="../../../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
        </figure>
    </footer>
</body>
</html>