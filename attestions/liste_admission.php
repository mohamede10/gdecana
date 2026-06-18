<?php
require_once '../config/db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit;
}

$req = $pdo->query("
    SELECT a.*, e.prenom, e.nom 
    FROM attestations_admission a
    JOIN etudiants e ON a.etudiant_id = e.id
    ORDER BY a.date_creation DESC
");
?>

<h2>Liste des Attestations d’Admission</h2>
<table border="1">
    <tr>
        <th>Étudiant</th>
        <th>Année BAC</th>
        <th>Programme</th>
        <th>Date</th>
    </tr>
    <?php foreach ($req as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['prenom']) ?> <?= htmlspecialchars($row['nom']) ?></td>
        <td><?= $row['annee_bac'] ?></td>
        <td><?= $row['programme'] ?></td>
        <td><?= $row['date_creation'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>
