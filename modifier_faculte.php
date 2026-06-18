<?php
session_start();
require 'config/db.php';

$codeFac = $_GET['code'] ?? '';
if (!$codeFac) {
    die("Code faculté manquant.");
}

// Récupérer la faculté
$stmt = $pdo->prepare("SELECT * FROM facultes WHERE CodeFac = ?");
$stmt->execute([$codeFac]);
$faculte = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$faculte) {
    die("Faculté introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomFac = $_POST['nom_faculte'] ?? '';
    if (!$nomFac) {
        $error = "Le nom de la faculté est obligatoire.";
    } else {
        // Mettre à jour
        $stmt = $pdo->prepare("UPDATE facultes SET NomFac = ? WHERE CodeFac = ?");
        $stmt->execute([$nomFac, $codeFac]);
        header("Location: liste_facultes.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Faculté</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Modifier la faculté <?= htmlspecialchars($faculte['NomFac']) ?></h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label>Code faculté</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($faculte['CodeFac']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Nom faculté *</label>
            <input type="text" name="nom_faculte" class="form-control" value="<?= htmlspecialchars($faculte['NomFac']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="liste_facultes.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>
