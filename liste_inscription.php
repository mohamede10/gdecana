<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$req = $pdo->query("
    SELECT a.*, e.prenom, e.nom 
    FROM attestations_inscription a
    JOIN etudiants e ON a.etudiant_id = e.id
    ORDER BY a.date_creation DESC
");
?>

<h2>Liste des Attestations d’Inscription</h2>
<table border="1">
    <tr>
        <th>Étudiant</th>
        <th>Programme</th>
        <th>Cohorte</th>
        <th>Date</th>
    </tr>
    <?php foreach ($req as $row): ?>
    <tr>
        <td><?= $row['prenom'] ?> <?= $row['nom'] ?></td>
        <td><?= $row['programme'] ?></td>
        <td><?= $row['cohorte'] ?></td>
        <td><?= $row['date_creation'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
