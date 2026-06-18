<?php
require 'config/db.php';

if (isset($_GET['MatEtu'])) {
    $MatEtu = $_GET['MatEtu'];

    // Supprimer directement l'étudiant ; toutes les données liées seront supprimées automatiquement
    $stmt = $pdo->prepare("DELETE FROM etudiants WHERE MatEtu = ?");
    $stmt->execute([$MatEtu]);

    header("Location: liste_etudiants.php");
    exit;
} else {
    echo "Aucun étudiant sélectionné pour la suppression.";
}
?>
