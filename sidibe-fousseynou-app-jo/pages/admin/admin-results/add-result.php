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

// Traitement du formulaire d'ajout de résultat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCSRFToken();

    // Récupérer les données du formulaire
    $id_athlete = $_POST['id_athlete'];
    $id_epreuve = $_POST['id_epreuve'];
    $resultat = $_POST['resultat'];

    // Insertion dans la base de données
    try {
        $query = "INSERT INTO participer (id_athlete, id_epreuve, resultat) VALUES (:id_athlete, :id_epreuve, :resultat)";
        $statement = $connexion->prepare($query);
        $statement->bindParam(':id_athlete', $id_athlete, PDO::PARAM_INT);
        $statement->bindParam(':id_epreuve', $id_epreuve, PDO::PARAM_INT);
        $statement->bindParam(':resultat', $resultat, PDO::PARAM_STR);

        if ($statement->execute()) {
            $_SESSION['success'] = "Résultat ajouté avec succès.";
            header('Location: manage-results.php'); // Redirige vers la page de gestion des résultats
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout du résultat.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

// Récupérer les athlètes et les épreuves pour le formulaire
$athletes = [];
$epreuves = [];

try {
    $athleteQuery = "SELECT id_athlete, nom_athlete, prenom_athlete FROM athlete ORDER BY nom_athlete, prenom_athlete";
    $athleteStatement = $connexion->prepare($athleteQuery);
    $athleteStatement->execute();
    $athletes = $athleteStatement->fetchAll(PDO::FETCH_ASSOC);

    $epreuveQuery = "SELECT id_epreuve, nom_epreuve FROM epreuve ORDER BY nom_epreuve";
    $epreuveStatement = $connexion->prepare($epreuveQuery);
    $epreuveStatement->execute();
    $epreuves = $epreuveStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
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
    <title>Ajouter un Résultat - Jeux Olympiques - Los Angeles 2028</title>

    <style>
        /* Styles pour le formulaire */
        form {
            margin: 20px 0;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: black; /* Couleur de fond (noir) */
            color: white; /* Couleur du texte */
            padding: 10px 15px; /* Espacement interne */
            border: none; /* Pas de contour */
            border-radius: 5px; /* Coins arrondis */
            cursor: pointer; /* Curseur en forme de main */
            transition: background-color 0.3s; /* Transition pour l'effet hover */
        }

        button:hover {
            background-color: #D7C378; /* Couleur de fond au survol (beige) */
        }

        .success {
            color: green; /* Couleur pour les messages de succès */
        }

        .error {
            color: red; /* Couleur pour les messages d'erreur */
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
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Ajouter un Résultat</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="POST" action="add-result.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <label for="id_athlete">Sélectionner un Athlète :</label>
            <select name="id_athlete" id="id_athlete" required>
                <option value="">-- Choisir un athlète --</option>
                <?php foreach ($athletes as $athlete): ?>
                    <option value="<?= $athlete['id_athlete']; ?>"><?= htmlspecialchars($athlete['prenom_athlete'] . ' ' . $athlete['nom_athlete'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="id_epreuve">Sélectionner une Épreuve :</label>
            <select name="id_epreuve" id="id_epreuve" required>
                <option value="">-- Choisir une épreuve --</option>
                <?php foreach ($epreuves as $epreuve): ?>
                    <option value="<?= $epreuve['id_epreuve']; ?>"><?= htmlspecialchars($epreuve['nom_epreuve'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="resultat">Résultat :</label>
            <input type="text" name="resultat" id="resultat" required placeholder="Entrez le résultat (ex: 9.58, 2:05:00, etc.)">

            <button type="submit">Ajouter le Résultat</button>
        </form>
    </main> 

    <footer>
        <p>&copy; 2023 Jeux Olympiques - Los Angeles 2028</p>
    </footer>
</body>
</html>