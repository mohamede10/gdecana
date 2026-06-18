<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

require 'config/db.php';

$success = $error = '';

// Récupérer la liste des étudiants
$etudiants = $pdo->query("SELECT MatEtu, NomEtu, PrenomEtu FROM etudiants ORDER BY NomEtu")->fetchAll();

// Récupérer les niveaux disponibles
$niveaux = $pdo->query("SELECT CodeNiv, Niveau FROM niveaux ORDER BY Niveau")->fetchAll();

// Récupérer les signataires
$signataires = $pdo->query("SELECT MatSigna, NomSigna, PrenomSigna FROM signataires ORDER BY NomSigna")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $MatEtu = $_POST['MatEtu'];
    $CodeNiv = $_POST['CodeNiv'];
    $MatSigna = $_POST['MatSigna'];
    $DateIns = date('Y-m-d');

    // Vérifier si l'étudiant est déjà inscrit à ce niveau
    $stmt = $pdo->prepare("SELECT * FROM inscriptions WHERE MatEtu = ? AND CodeNiv = ?");
    $stmt->execute([$MatEtu, $CodeNiv]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Cet étudiant est déjà inscrit à ce niveau.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO inscriptions (MatEtu, MatSigna, CodeNiv, DateIns) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$MatEtu, $MatSigna, $CodeNiv, $DateIns])) {
            $success = "Réinscription effectuée avec succès.";
        } else {
            $error = "Erreur lors de la réinscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réinscription Étudiant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">🔁 Réinscription d'un étudiant</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label class="form-label">Etudiant :</label>
      <select name="MatEtu" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($etudiants as $etu): ?>
          <option value="<?= $etu['MatEtu'] ?>">
            <?= htmlspecialchars($etu['NomEtu'] . ' ' . $etu['PrenomEtu']) ?> (<?= $etu['MatEtu'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Niveau :</label>
      <select name="CodeNiv" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($niveaux as $niv): ?>
          <option value="<?= $niv['CodeNiv'] ?>">
            <?= htmlspecialchars($niv['Niveau']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Signataire :</label>
      <select name="MatSigna" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($signataires as $signa): ?>
          <option value="<?= $signa['MatSigna'] ?>">
            <?= htmlspecialchars($signa['NomSigna'] . ' ' . $signa['PrenomSigna']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Réinscrire</button>
  </form>
</div>
</body>
</html>
