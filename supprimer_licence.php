<?php
session_start();
require 'config/db.php';

$codeProg = $_GET['code'] ?? '';
if (!$codeProg) {
    die("Code licence manquant.");
}

try {
    // Vérifier si la licence a des niveaux ou dépendances (selon ta structure, ici on ne le fait pas mais tu peux)

    // Supprimer licence
    $stmt = $pdo->prepare("DELETE FROM programmes WHERE CodeProg = ?");
    $stmt->execute([$codeProg]);

    header("Location: liste_facultes.php");
    exit;

} catch (PDOException $e) {
    die("Erreur lors de la suppression : " . $e->getMessage());
}
