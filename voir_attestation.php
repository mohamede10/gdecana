<?php
session_start();
require 'config/db.php';

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    die("⚠️ Paramètres manquants !");
}

$id   = (int) $_GET['id'];
$type = $_GET['type'];

// Choix de la table selon le type
switch ($type) {
    case 'admission':
        $table = 'attestations_admission';
        break;
    case 'inscription':
        $table = 'attestations_inscription';
        break;
    case 'reinscription':
        $table = 'attestations_reinscription';
        break;
    case 'niveau':
        $table = 'attestations_niveaux';
        break;
    case 'carte':
        $table = 'cartes_scolaires';
        break;
    case 'releve':
        $table = 'releves_notes';
        break;
    default:
        die("⚠️ Type invalide !");
}

$sql  = "SELECT chemin_pdf FROM $table WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || empty($row['chemin_pdf'])) {
    die("⚠️ Fichier introuvable en base !");
}

$chemin = $row['chemin_pdf'];

if (!file_exists($chemin)) {
    die("⚠️ Fichier introuvable sur le serveur !");
}

// Envoyer le fichier PDF au navigateur
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($chemin) . '"');
header('Content-Length: ' . filesize($chemin));

readfile($chemin);
exit;
