<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Attestation non spécifiée.");

// Suppression
$stmt = $pdo->prepare("DELETE FROM attestations_niveaux WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: liste_niveau.php?success=Attestation supprimée avec succès");
exit;
