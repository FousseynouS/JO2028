<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si un ID d'utilisateur est passé dans la requête
if (isset($_POST['id_utilisateur'])) {
    $id_utilisateur = $_POST['id_utilisateur'];

    // Suppression de l'utilisateur dans la base de données
    try {
        // Prépare la requête SQL pour supprimer l'utilisateur
        $queryDeleteUser = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :id_utilisateur";
        $statementDeleteUser = $connexion->prepare($queryDeleteUser);
        $statementDeleteUser->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $statementDeleteUser->execute();

        // Message de succès
        $_SESSION['success'] = "Utilisateur supprimé avec succès.";
    } catch (PDOException $e) {
        // Message d'erreur si la suppression échoue
        $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    }
}

// Redirige vers la page de gestion des utilisateurs après la suppression
header("Location: manage-users.php");
exit();
?>
