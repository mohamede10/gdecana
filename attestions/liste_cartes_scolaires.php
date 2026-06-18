<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit;
}

$req = $pdo->query("
    SELECT c.*, e.prenom, e.nom 
    FROM cartes_scolaires c
    JOIN etudiants e ON c.etudiant_id = e.id
    ORDER BY c.date_creation DESC
");
?>

<h2>Liste des Cartes Scolaires</h2>
<table border="1">
    <tr>
        <th>Étudiant</th>
        <th>Année universitaire</th>
        <th>Date de création</th>
    </tr>
    <?php foreach ($req as $row): ?>
    <tr>
        <td><?= $row['prenom'] ?> <?= $row['nom'] ?></td>
        <td><?= $row['annee_universitaire'] ?></td>
        <td><?= $row['date_creation'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
