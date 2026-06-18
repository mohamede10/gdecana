<?php
require 'config/db.php';

// Récupération des filtres
$faculte = $_GET['faculte'] ?? '';
$departement = $_GET['departement'] ?? '';
$licence = $_GET['licence'] ?? '';

// Facultés
$stmtFac = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac");
$facultes = $stmtFac->fetchAll();

// Départements
$departements = [];
if (!empty($faculte)) {
    $stmtDep = $pdo->prepare("SELECT CodeDep, NomDep FROM departements WHERE CodeFac = ? ORDER BY NomDep");
    $stmtDep->execute([$faculte]);
    $departements = $stmtDep->fetchAll();
}

// Licences
$licences = [];
if (!empty($departement)) {
    $stmtLic = $pdo->prepare("SELECT CodeLic, NomLic FROM licences WHERE CodeDep = ? ORDER BY NomLic");
    $stmtLic->execute([$departement]);
    $licences = $stmtLic->fetchAll();
}

// Étudiants
$sql = "SELECT e.*, f.NomFac, d.NomDep, l.NomLic
        FROM etudiants e
        LEFT JOIN facultes f ON e.CodeFac = f.CodeFac
        LEFT JOIN departements d ON e.CodeDep = d.CodeDep
        LEFT JOIN licences l ON e.CodeLic = l.CodeLic
        WHERE 1";

$params = [];

if (!empty($faculte)) {
    $sql .= " AND e.CodeFac = ?";
    $params[] = $faculte;
}
if (!empty($departement)) {
    $sql .= " AND e.CodeDep = ?";
    $params[] = $departement;
}
if (!empty($licence)) {
    $sql .= " AND e.CodeLic = ?";
    $params[] = $licence;
}

$sql .= " ORDER BY e.NomEtu, e.PrenomEtu";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des Étudiants</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
    body { padding: 20px; }
    h1 { margin-bottom: 20px; }
</style>
</head>
<body>
<h1>Liste des Étudiants</h1>

<form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Faculté</label>
        <select name="faculte" class="form-select" onchange="this.form.submit()">
            <option value="">Toutes</option>
            <?php foreach ($facultes as $f): ?>
                <option value="<?= htmlspecialchars($f['CodeFac']) ?>" <?= ($faculte == $f['CodeFac']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['NomFac']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Département</label>
        <select name="departement" class="form-select" onchange="this.form.submit()">
            <option value="">Tous</option>
            <?php foreach ($departements as $d): ?>
                <option value="<?= htmlspecialchars($d['CodeDep']) ?>" <?= ($departement == $d['CodeDep']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['NomDep']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Licence</label>
        <select name="licence" class="form-select" onchange="this.form.submit()">
            <option value="">Toutes</option>
            <?php foreach ($licences as $l): ?>
                <option value="<?= htmlspecialchars($l['NomLic']) ?>" <?= ($licence == $l['NomLic']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['NomLic']) ?> (<?= htmlspecialchars($l['NomLic']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="dashboard_admin.php" class="btn btn-danger ms-2">Fermer</a>
    </div>
</form>

<?php if ($etudiants): ?>
<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>INE</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Faculté</th>
            <th>Département</th>
            <th>Licence</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($etudiants as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['INE']) ?></td>
            <td><?= htmlspecialchars($e['NomEtu']) ?></td>
            <td><?= htmlspecialchars($e['PrenomEtu']) ?></td>
            <td><?= htmlspecialchars($e['NomFac']) ?></td>
            <td><?= htmlspecialchars($e['NomDep']) ?></td>
            <td><?= !empty($e['CodeLic']) ? htmlspecialchars($e['NomLic']) : '<span class="text-danger">Non défini</span>' ?></td>
            <td>
                <a href="modifier_etudiant.php?MatEtu=<?= urlencode($e['MatEtu']) ?>" class="btn btn-sm btn-warning">Modifier</a>
                <a href="supprimer_etudiant.php?MatEtu=<?= urlencode($e['MatEtu']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>

            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="alert alert-warning">Aucun étudiant trouvé pour les filtres sélectionnés.</p>
<?php endif; ?>

</body>
</html>
