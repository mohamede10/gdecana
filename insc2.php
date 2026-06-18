<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

// Facultés, cohortes, signataires
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll(PDO::FETCH_ASSOC);
$cohortes = $pdo->query("SELECT CodeCoh, Cohorte FROM cohortes ORDER BY Cohorte")->fetchAll(PDO::FETCH_ASSOC);
$signataires = $pdo->query("SELECT MatSigna, CONCAT(NomSigna, ' ', PrenomSigna) AS nom FROM signataires")->fetchAll(PDO::FETCH_ASSOC);

$success = false;
$messageErreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $MatEtu     = trim($_POST['MatEtu'] ?? '');
    $INE        = trim($_POST['INE'] ?? '');
    $NomEtu     = trim($_POST['NomEtu'] ?? '');
    $PrenomEtu  = trim($_POST['PrenomEtu'] ?? '');
    $CodeCoh    = trim($_POST['CodeCoh'] ?? '');
    $CodeNiv    = trim($_POST['niveau'] ?? '');
    $DateIns    = $_POST['DateIns'] ?? date('Y-m-d');
    $MatSigna   = $_POST['MatSigna'] ?? null;
    $programme  = $_POST['programme'] ?? '';
    $photoPath  = null;

    // 1. Traitement de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $filename = uniqid('photo_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $photoPath = $destination;
            } else {
                $messageErreur = "Erreur lors de l'upload de la photo.";
            }
        } else {
            $messageErreur = "Format de photo non valide (jpg, jpeg, png uniquement).";
        }
    }

    // 2. Traitement DB
    if (empty($messageErreur)) {
        $stmt = $pdo->prepare("SELECT CodeConc FROM concentrations WHERE CodeProg = ? LIMIT 1");
        $stmt->execute([$programme]);
        $CodeConc = $stmt->fetchColumn();

        if (!$CodeConc) {
            $messageErreur = "❌ Aucune concentration trouvée pour ce programme.";
        } else {
            try {
                $pdo->beginTransaction();

                $check = $pdo->prepare("SELECT COUNT(*) FROM etudiants WHERE MatEtu = ?");
                $check->execute([$MatEtu]);

                if ($check->fetchColumn() == 0) {
                    $insertEtu = $pdo->prepare("INSERT INTO etudiants (MatEtu, INE, NomEtu, PrenomEtu, CodeCoh, photo) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertEtu->execute([$MatEtu, $INE, $NomEtu, $PrenomEtu, $CodeCoh, $photoPath]);
                }

                $checkIns = $pdo->prepare("SELECT COUNT(*) FROM inscriptions WHERE MatEtu = ? AND CodeConc = ? AND CodeNiv = ?");
                $checkIns->execute([$MatEtu, $CodeConc, $CodeNiv]);

                if ($checkIns->fetchColumn() == 0) {
                    $insertIns = $pdo->prepare("INSERT INTO inscriptions (MatEtu, MatSigna, CodeConc, CodeNiv, DateIns) VALUES (?, ?, ?, ?, ?)");
                    $insertIns->execute([$MatEtu, $MatSigna, $CodeConc, $CodeNiv, $DateIns]);
                    $success = true;
                } else {
                    throw new Exception("⚠️ Étudiant déjà inscrit à ce niveau.");
                }

                $pdo->commit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $messageErreur = $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>📝 Inscription Étudiant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h3 class="mb-4 text-center">📝 Formulaire d'Inscription Étudiant avec Photo</h3>

  <?php if ($success): ?>
    <div class="alert alert-success text-center">✅ Étudiant inscrit avec succès !</div>
  <?php elseif (!empty($messageErreur)): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($messageErreur) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
      <label>Matricule</label>
      <input type="text" name="MatEtu" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>INE</label>
      <input type="text" name="INE" class="form-control">
    </div>
    <div class="col-md-6">
      <label>Nom</label>
      <input type="text" name="NomEtu" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>Prénom</label>
      <input type="text" name="PrenomEtu" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label>Faculté</label>
      <select name="faculte" id="faculte" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($facultes as $fac): ?>
          <option value="<?= $fac['CodeFac'] ?>"><?= $fac['NomFac'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label>Département</label>
      <select name="departement" id="departement" class="form-select" required>
        <option value="">-- Faculté d'abord --</option>
      </select>
    </div>
    <div class="col-md-3">
      <label>Programme</label>
      <select name="programme" id="programme" class="form-select" required>
        <option value="">-- Département d'abord --</option>
      </select>
    </div>
    <div class="col-md-3">
      <label>Niveau</label>
      <select name="niveau" id="niveau" class="form-select" required>
        <option value="">-- Programme d'abord --</option>
      </select>
    </div>
    <div class="col-md-4">
      <label>Cohorte</label>
      <select name="CodeCoh" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($cohortes as $coh): ?>
          <option value="<?= $coh['CodeCoh'] ?>"><?= $coh['Cohorte'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label>Signataire</label>
      <select name="MatSigna" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($signataires as $sig): ?>
          <option value="<?= $sig['MatSigna'] ?>"><?= $sig['nom'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label>Date d'inscription</label>
      <input type="date" name="DateIns" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label>📸 Photo de l'étudiant (JPEG/PNG)</label>
      <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
    </div>
    <div class="col-12 text-center mt-4">
      <button type="submit" class="btn btn-success px-5">✅ Enregistrer</button>
    </div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#faculte').on('change', function() {
  $.post('ajax_departements.php', {faculte: $(this).val()}, function(data){
    $('#departement').html(data);
    $('#programme').html('<option value="">-- Département d\'abord --</option>');
    $('#niveau').html('<option value="">-- Programme d\'abord --</option>');
  });
});
$('#departement').on('change', function() {
  $.post('ajax_programmes.php', {departement: $(this).val()}, function(data){
    $('#programme').html(data);
    $('#niveau').html('<option value="">-- Programme d\'abord --</option>');
  });
});
$('#programme').on('change', function() {
  $.post('ajax_niveaux.php', {programme: $(this).val()}, function(data){
    $('#niveau').html(data);
  });
});
</script>
</body>
</html>
