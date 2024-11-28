<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Fonction pour vérifier le token CSRF
function checkCSRFToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Token CSRF invalide.');
        }
    }
}

// Générer un token CSRF si ce n'est pas déjà fait
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Génère un token CSRF
}

// Traitement des actions Modifier et Supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRFToken();

    if (isset($_POST['action'])) {
        $id_utilisateur = $_POST['id_utilisateur'];

        if ($_POST['action'] === 'delete') {
            // Code pour supprimer un utilisateur
            $deleteQuery = "DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
            $deleteStatement = $connexion->prepare($deleteQuery);
            $deleteStatement->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $deleteStatement->execute();
            header('Location: manage-users.php'); // Redirige après suppression
            exit();
        } elseif ($_POST['action'] === 'modify') {
            // Rediriger vers une page de modification ou afficher un formulaire
            header("Location: modify-user.php?id_utilisateur=" . $id_utilisateur);
            exit();
        }
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
    <link rel="stylesheet" href="../../../css/manage.css">
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Gestion des Utilisateurs - Jeux Olympiques - Los Angeles 2028</title>

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
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Gestion des Utilisateurs</h1>
        <div class="action-buttons">
            <button onclick="window.location.href='add-user.php'">Ajouter un Utilisateur</button>
        </div>

        <!-- Tableau des utilisateurs -->
        <?php
        try {
            // Requête pour récupérer la liste des utilisateurs depuis la base de données
            $query = "SELECT * FROM utilisateur ORDER BY nom_utilisateur";
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table><tr><th>Nom</th><th>Prénom</th><th>Modifier</th><th>Supprimer</th></tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom_utilisateur'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom_utilisateur'], ENT_QUOTES, 'UTF-8') . "</td>";
                    
                    // Bouton Modifier
                    echo "<td>
                            <form action='' method='post' style='display:inline;'>
                                <input type='hidden' name='id_utilisateur' value='" . htmlspecialchars($row['id_utilisateur'], ENT_QUOTES, 'UTF-8') . "'>
                                <input type='hidden' name='action' value='modify'>
                                <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
                                <button type='submit'>Modifier</button>
                            </form>
                          </td>";

                    // Bouton Supprimer
                    echo "<td>
                            <form action='' method='post' style='display:inline;'>
                                <input type='hidden' name='id_utilisateur' value='" . htmlspecialchars($row['id_utilisateur'], ENT_QUOTES, 'UTF-8') . "'>
                                <input type='hidden' name='action' value='delete'>
                                <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
                                <button type='submit' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\");'>Supprimer</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Aucun utilisateur trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
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