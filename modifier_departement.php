<?php
session_start();
require 'config/db.php';

$codeDep = $_GET['code'] ?? '';
if (!$codeDep) {
    die("Code département manquant.");
}

// Récupérer département
$stmt = $pdo->prepare("SELECT * FROM departements WHERE CodeDep = ?");
$stmt->execute([$codeDep]);
$departement = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$departement) {
    die("Département introuvable.");
}

// Récupérer facultés pour sélection
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomDep = $_POST['nom_departement'] ?? '';
    $codeFac = $_POST['code_faculte'] ?? '';
    if (!$nomDep || !$codeFac) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Mettre à jour
        $stmt = $pdo->prepare("UPDATE departements SET NomDep = ?, CodeFac = ? WHERE CodeDep = ?");
        $stmt->execute([$nomDep, $codeFac, $codeDep]);
        header("Location: liste_facultes.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Département</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Modifier le département <?= htmlspecialchars($departement['NomDep']) ?></h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label>Code département</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($departement['CodeDep']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Nom département *</label>
            <input type="text" name="nom_departement" class="form-control" value="<?= htmlspecialchars($departement['NomDep']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Faculté *</label>
            <select name="code_faculte" class="form-select" required>
                <option value="">-- Choisir une faculté --</option>
                <?php foreach ($facultes as $fac): ?>
                    <option value="<?= htmlspecialchars($fac['CodeFac']) ?>" <?= $fac['CodeFac'] === $departement['CodeFac'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fac['NomFac']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="liste_facultes.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
