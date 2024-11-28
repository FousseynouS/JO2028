<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du lieu à supprimer est passé en paramètre
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Vérifiez si l'ID est valide
    if ($id) {
        try {
            // Requête pour supprimer le lieu
            $query = "DELETE FROM lieu WHERE id = :id";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":id", $id, PDO::PARAM_INT);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le lieu a été supprimé avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression du lieu.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "ID de lieu invalide.";
    }
} else {
    $_SESSION['error'] = "Aucun ID de lieu spécifié.";
}

// Redirigez vers la page de gestion des lieux
header("Location: manage-places.php");
exit();
?>