<?php
session_start();

if (!isset($_POST['fichier']) || !isset($_POST['data'])) {
    die("Données manquantes.");
}

$fichierRelatif = $_POST['fichier'];
$fichier = __DIR__ . '/' . $fichierRelatif;

if (!file_exists($fichier)) {
    die("Fichier introuvable.");
}

$data = $_POST['data'];

// Lire l’en-tête
$lines = file($fichier);
$header = str_getcsv(array_shift($lines));

// Réécrire le fichier
$fp = fopen($fichier, 'w');

fputcsv($fp, $header);

foreach ($data as $row) {
    fputcsv($fp, $row);
}

fclose($fp);

// Redirection ou message
echo "<script>alert('✅ Fichier mis à jour avec succès'); window.location.href = 'liste_fichiers_importes.php';</script>";
