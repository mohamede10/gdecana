<?php
session_start();
require_once 'config/db.php';

// Vérification connexion
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer l'ID et le type
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if (!$id || !$type) {
    die("ID ou type manquant.");
}

// Déterminer la table selon le type
$tableMap = [
    'niveau' => 'attestations_niveaux',
    'admission' => 'attestations_admission',
    'inscription' => 'attestations_inscription',
    'reinscription' => 'attestations_reinscription',
    'releve' => 'releves_notes',
    'carte' => 'cartes_scolaires'
];

if (!array_key_exists($type, $tableMap)) {
    die("Type d'attestation invalide.");
}

$table = $tableMap[$type];

$stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: liste_att_{$type}.php?success=Attestation supprimée avec succès");
exit;
