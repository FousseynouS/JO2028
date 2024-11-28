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

// Traitement des actions Modifier, Copier et Supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRFToken();

    if (isset($_POST['action'])) {
        $id_lieu = $_POST['id_lieu'];

        if ($_POST['action'] === 'delete') {
            // Code pour supprimer un lieu
            $deleteQuery = "DELETE FROM lieu WHERE id_lieu = :id_lieu";
            $deleteStatement = $connexion->prepare($deleteQuery);
            $deleteStatement->bindParam(':id_lieu', $id_lieu, PDO::PARAM_INT);
            if ($deleteStatement->execute()) {
                echo "<p class='success'>Lieu supprimé avec succès.</p>";
            } else {
                echo "<p class='error'>Erreur lors de la suppression du lieu.</p>";
            }
            header('Location: manage-places.php'); // Redirige après suppression
            exit();
        } elseif ($_POST['action'] === 'modify') {
            // Rediriger vers une page de modification ou afficher un formulaire
            header("Location: modify-place.php?id_lieu=" . $id_lieu);
            exit();
        } elseif ($_POST['action'] === 'copy') {
            // Code pour copier un lieu (vous pouvez implémenter cette fonctionnalité)
            // Par exemple, rediriger vers une page d'ajout avec les données pré-remplies
            header("Location: add-place.php?id_lieu=" . $id_lieu);
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
    <link rel="shortcut icon" href="../../../img/favicon.ico" type="image/x-icon">
    <title>Gestion des Lieux - Jeux Olympiques - Los Angeles 2028</title>

    <style>
        button {
            background-color: black; /* Couleur de fond */
            color: white; /* Couleur du texte */
            padding: 10px 15px; /* Espacement interne */
            margin: 5px; /* Espacement externe */
            border: none; /* Pas de contour */
            border-radius: 5px; /* Coins arrondis */
            cursor: pointer; /* Curseur en forme de main */
            transition: background-color 0.3s; /* Transition pour l'effet hover */
        }

        button:hover {
            background-color: #D7C378; /* Couleur de fond au survol */
        }

        form{
            border: none
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
    <h1>Gestion des Lieux</h1>
    <div class="action-buttons">
        <button onclick="window.location.href='add-place.php'">Ajouter un Lieu</button>
    </div>

    <!-- Affichage des messages de succès ou d'erreur -->
    <?php
    if (isset($_SESSION['success'])) {
        echo "<p class='success'>" . htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') . "</p>";
        unset($_SESSION['success']); // Supprime le message après l'affichage
    }

    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</p>";
        unset($_SESSION['error']); // Supprime le message après l'affichage
    }
    ?>

    <!-- Tableau des lieux -->
    <?php
    try {
        // Requête pour récupérer la liste des lieux depuis la base de données
        $query = "SELECT * FROM lieu ORDER BY nom_lieu"; // Utilisez 'lieu' au lieu de 'places'
        $statement = $connexion->prepare($query);
        $statement->execute();

        // Vérifier s'il y a des résultats
        if ($statement->rowCount() > 0) {
            echo "<table><tr><th>Nom du Lieu</th><th>Adresse</th><th>Code Postal</th><th>Ville</th><th>Modifier</th><th>Supprimer</th></tr>";

            // Afficher les données dans un tableau
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nom_lieu'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($row['adresse_lieu'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($row['cp_lieu'], ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td>" . htmlspecialchars($row['ville_lieu'], ENT_QUOTES, 'UTF-8') . "</td>";
                
                // Bouton Modifier
                echo "<td>
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='id_lieu' value='" . htmlspecialchars($row['id_lieu'], ENT_QUOTES, 'UTF-8') . "'>
                            <input type='hidden' name='action' value='modify'>
                            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
                            <button type='submit'>Modifier</button>
                        </form>
                      </td>";

                // Bouton Supprimer
                echo "<td>
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='id_lieu' value='" . htmlspecialchars($row['id_lieu'], ENT_QUOTES, 'UTF-8') . "'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
                            <button type='submit' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce lieu ?\");'>Supprimer</button>
                        </form>
                      </td>";

                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Aucun lieu trouvé.</p>";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
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