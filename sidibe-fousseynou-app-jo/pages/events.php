<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles-computer.css">
    <link rel="stylesheet" href="../css/styles-responsive.css">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <title>Calendrier des épreuves - Jeux Olympiques - Los Angeles 2028</title>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="sports.php">Sports</a></li>
                <li><a href="events.php">Calendrier des épreuves</a></li>
                <li><a href="results.php">Résultats</a></li>
                <li><a href="login.php">Accès administrateur</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Calendrier des épreuves</h1>

        <?php
        require_once("../database/database.php");  // Inclusion de la connexion à la base de données

        try {
            // Requête pour récupérer les événements (épreuves) avec leur sport, date et heure
            $query = "SELECT e.nom_epreuve, s.nom_sport, e.date_epreuve, e.heure_epreuve
                      FROM epreuve e
                      INNER JOIN sport s ON e.id_sport = s.id_sport
                      ORDER BY e.date_epreuve, e.heure_epreuve";
            
            // Préparation et exécution de la requête SQL
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des événements dans la base de données
            if ($statement->rowCount() > 0) {
                echo "<table>";
                echo "<tr><th class='color'>Épreuve</th><th class='color'>Sport</th><th class='color'>Date</th><th class='color'>Heure</th></tr>";

                // Affichage des événements sous forme de lignes dans le tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_sport'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_epreuve'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['heure_epreuve'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun événement trouvé.</p>";
            }
        } catch (PDOException $e) {
            // Gestion d'erreur améliorée
            echo "<p style='color: red;'>Erreur : Impossible de récupérer les événements. Veuillez réessayer plus tard.</p>";
            error_log("Erreur PDO : " . $e->getMessage()); // Enregistrer l'erreur dans un fichier de log pour le débogage
        }

        // Activer l'affichage des erreurs en phase de développement
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ?>

        <p class="paragraph-link">
            <a class="link-home" href="../index.php">Retour à l'Accueil</a>
        </p>
    </main>

    <footer>
        <figure>
            <img src="../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
        </figure>
    </footer>
</body>

</html>
