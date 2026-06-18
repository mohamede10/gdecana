<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$req = $pdo->query("
    SELECT a.*, e.prenom, e.nom, n.nom_niveau 
    FROM attestations_niveaux a
    JOIN etudiants e ON a.etudiant_id = e.id
    JOIN niveaux n ON a.niveau_id = n.id
    ORDER BY a.date_creation DESC
");
?>

<h2>Liste des Attestations de Niveau</h2>
<table border="1">
    <tr>
        <th>Étudiant</th>
        <th>Niveau</th>
        <th>Année</th>
        <th>Date</th>
    </tr>
    <?php foreach ($req as $row): ?>
    <tr>
        <td><?= $row['prenom'] ?> <?= $row['nom'] ?></td>
        <td><?= $row['nom_niveau'] ?></td>
        <td><?= $row['annee_universitaire'] ?></td>
        <td><?= $row['date_creation'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
