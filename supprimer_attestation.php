<?php
require_once 'config/db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit;
}

// Vérifier si l'ID est fourni
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Supprimer l'attestation
    $stmt = $pdo->prepare("DELETE FROM attestations_admission WHERE id = ?");
    $stmt->execute([$id]);

    // Rediriger vers la liste avec message de succès
    header('Location: liste_admission.php?success=Attestation supprimée avec succès');
    exit;
}

// Si aucun ID, revenir à la liste
header('Location: liste_admission.php');
exit;
