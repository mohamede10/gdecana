<?php
session_start();
require 'config/db.php';

$codeDep = $_GET['code'] ?? '';
if (!$codeDep) {
    die("Code département manquant.");
}

try {
    // Vérifier si département lié à des programmes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM programmes WHERE CodeDep = ?");
    $stmt->execute([$codeDep]);
    if ($stmt->fetchColumn() > 0) {
        die("Impossible de supprimer ce département : il contient des licences/programmes.");
    }

    // Supprimer département
    $stmt = $pdo->prepare("DELETE FROM departements WHERE CodeDep = ?");
    $stmt->execute([$codeDep]);

    header("Location: liste_facultes.php");
    exit;

} catch (PDOException $e) {
    die("Erreur lors de la suppression : " . $e->getMessage());
}
