<?php
if (!isset($_GET['fichier'])) {
    die("Fichier non spécifié.");
}

$fichierRelatif = $_GET['fichier'];
$fichier = __DIR__ . '/' . $fichierRelatif;

if (!file_exists($fichier)) {
    die("Fichier introuvable.");
}

if (unlink($fichier)) {
    header("Location: liste_fichiers_importes.php");
    exit;
} else {
    die("Erreur lors de la suppression du fichier.");
}
?>
