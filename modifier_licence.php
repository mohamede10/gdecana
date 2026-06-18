<?php
session_start();
require 'config/db.php';

$codeProg = $_GET['code'] ?? '';
if (!$codeProg) {
    die("Code licence manquant.");
}

// Récupérer licence
$stmt = $pdo->prepare("SELECT * FROM programmes WHERE CodeProg = ?");
$stmt->execute([$codeProg]);
$licence = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$licence) {
    die("Licence introuvable.");
}

// Récupérer départements pour sélection
$departements = $pdo->query("SELECT CodeDep, NomDep FROM departements ORDER BY NomDep")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomProg = $_POST['nom_licence'] ?? '';
    $codeDep = $_POST['code_departement'] ?? '';
    if (!$nomProg || !$codeDep) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        // Mettre à jour
        $stmt = $pdo->prepare("UPDATE programmes SET NomProg = ?, CodeDep = ? WHERE CodeProg = ?");
        $stmt->execute([$nomProg, $codeDep, $codeProg]);
        header("Location: liste_facultes.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Licence</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Modifier la licence <?= htmlspecialchars($licence['NomProg']) ?></h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label>Code licence</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($licence['CodeProg']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Nom licence *</label>
            <input type="text" name="nom_licence" class="form-control" value="<?= htmlspecialchars($licence['NomProg']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Département *</label>
            <select name="code_departement" class="form-select" required>
                <option value="">-- Choisir un département --</option>
                <?php foreach ($departements as $dep): ?>
                    <option value="<?= htmlspecialchars($dep['CodeDep']) ?>" <?= $dep['CodeDep'] === $licence['CodeDep'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dep['NomDep']) ?>
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
