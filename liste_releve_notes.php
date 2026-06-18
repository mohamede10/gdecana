<?php
include("config/db.php");

// Recherche
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Requête avec jointure pour avoir les infos étudiant
$sql = "SELECT rn.*, e.NomEtu, e.PrenomEtu, e.INE
        FROM releves_notes rn
        JOIN etudiants e ON rn.MatEtu = e.MatEtu
        WHERE e.NomEtu LIKE :search
           OR e.PrenomEtu LIKE :search
           OR e.INE LIKE :search
        ORDER BY rn.date_generation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':search' => "%$search%"]);
$releves = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Relevés de Notes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

    <h2 class="mb-4">📄 Liste des Relevés de Notes</h2>

    <form method="get" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, prénom ou INE" value="<?= htmlspecialchars($search) ?>">
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Étudiant</th>
                <th>INE</th>
                <th>Semestre</th>
                <th>Année Universitaire</th>
                <th>Date de Génération</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($releves): ?>
                <?php foreach ($releves as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['NomEtu'] . " " . $r['PrenomEtu']) ?></td>
                        <td><?= htmlspecialchars($r['INE']) ?></td>
                        <td><?= htmlspecialchars($r['semestre']) ?></td>
                        <td><?= htmlspecialchars($r['annee_univ']) ?></td>
                        <td><?= htmlspecialchars($r['date_generation']) ?></td>
                        <td class="d-flex gap-2">
                            <a href="voir_attestation.php?id=<?= $r['id'] ?>&type=releve"
                               class="btn btn-success btn-sm" target="_blank">Voir</a>
                            <!--<a href="voir_attestation.php?id=<?= $r['id'] ?>&type=releve&download=1"
                               class="btn btn-secondary btn-sm">Télécharger</a>-->
                            <a href="supprimer_releve.php?id=<?= $r['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer ce relevé ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Aucun relevé trouvé</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
