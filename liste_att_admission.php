<?php
require_once 'config/db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../login.php');
    exit;
}

// Vérifier si un message de succès est passé en GET (après suppression)
$success = $_GET['success'] ?? '';

// Gestion de la recherche
$search = $_GET['search'] ?? '';
$search_param = "%$search%";

if ($search) {
    $stmt = $pdo->prepare("
        SELECT a.*, e.PrenomEtu, e.NomEtu, e.INE
        FROM attestations_admission a
        JOIN etudiants e ON a.MatEtu = e.MatEtu
        WHERE e.NomEtu LIKE ? OR e.PrenomEtu LIKE ? OR e.INE LIKE ?
        ORDER BY a.date_generation DESC
    ");
    $stmt->execute([$search_param, $search_param, $search_param]);
    $req = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $req = $pdo->query("
        SELECT a.*, e.PrenomEtu, e.NomEtu, e.INE
        FROM attestations_admission a
        JOIN etudiants e ON a.MatEtu = e.MatEtu
        ORDER BY a.date_generation DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Attestations d’Admission</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Liste des Attestations d’Admission</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <form class="mb-3" method="get" action="">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, prénom ou INE" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <a href="liste_admission.php" class="btn btn-secondary">Actualiser</a>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Étudiant</th>
                <th>INE</th>
                <th>Année BAC</th>
                <th>Programme</th>
                <th>Date de Génération</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($req as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['PrenomEtu']) ?> <?= htmlspecialchars($row['NomEtu']) ?></td>
                <td><?= htmlspecialchars($row['INE']) ?></td>
                <td><?= htmlspecialchars($row['session_bac']) ?></td>
                <td><?= htmlspecialchars($row['programme']) ?></td>
                <td><?= htmlspecialchars($row['date_generation']) ?></td>
                <td>
                  <a href="voir_attestation.php?id=<?= $row['id'] ?>&type=admission" target="_blank" class="btn btn-success btn-sm">Voir</a>
                    <a href="supprimer_attestation.php?id=<?= $row['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette attestation ?');">
                       Supprimer
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
