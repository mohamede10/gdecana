<?php
session_start();
require_once 'config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

// Message de succès
$success = $_GET['success'] ?? '';

// Recherche
$search = trim($_GET['search'] ?? '');
$params = [];
$where = "";

if (!empty($search)) {
    $where = "WHERE e.NomEtu LIKE :search OR e.PrenomEtu LIKE :search OR e.INE LIKE :search";
    $params[':search'] = "%$search%";
}

$sql = "
SELECT c.id, c.numero_attestation, c.annee_univ, c.date_generation,
       e.PrenomEtu, e.NomEtu, e.INE,
       c.programme AS programme,
       c.niveau AS niveau,
       f.NomFac AS faculte,
       d.NomDep AS departement
FROM cartes_scolaires c
JOIN etudiants e ON c.MatEtu = e.MatEtu
LEFT JOIN departements d ON e.CodeDep = d.CodeDep
LEFT JOIN facultes f ON e.CodeFac = f.CodeFac
$where
ORDER BY c.date_generation DESC

";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cartes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Cartes Scolaires</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Liste des Cartes Scolaires</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de recherche -->
    <form class="mb-3" method="get" action="">
        <div class="input-group">
            <input type="text" name="search" class="form-control"
                   placeholder="Rechercher par nom, prénom ou INE"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <a href="liste_carte.php" class="btn btn-secondary">Actualiser</a>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Étudiant</th>
                <th>INE</th>
                <th>Faculté</th>
                <th>Département</th>
                <th>Programme</th>
                <th>Niveau</th>
                <th>Année Univ.</th>
                <th>Numéro Carte</th>
                <th>Date Génération</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($cartes): ?>
            <?php foreach ($cartes as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['PrenomEtu'].' '.$row['NomEtu']) ?></td>
                    <td><?= htmlspecialchars($row['INE']) ?></td>
                    <td><?= htmlspecialchars($row['faculte'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['departement'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['programme'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['niveau'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['annee_univ']) ?></td>
                    <td><?= htmlspecialchars($row['numero_attestation']) ?></td>
                    <td><?= htmlspecialchars($row['date_generation']) ?></td>
                    <td class="d-flex gap-2">
                        <a href="voir_attestation.php?id=<?= $row['id'] ?>&type=carte"
                           class="btn btn-success btn-sm" target="_blank">Voir</a>
                        <!--<a href="voir_attestation.php?id=<?= $row['id'] ?>&type=carte&download=1"
                           class="btn btn-secondary btn-sm">Télécharger</a>-->
                        <a href="supprimer_carte.php?id=<?= $row['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Supprimer cette carte ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" class="text-center">Aucune carte trouvée.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
