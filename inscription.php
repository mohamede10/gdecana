<?php
session_start();
require 'config/db.php';

// Facultés
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll(PDO::FETCH_ASSOC);

$values = [
    'MatEtu'=>'', 'INE'=>'', 'NomEtu'=>'', 'PrenomEtu'=>'', 'DateNais'=>'', 'LieuNais'=>'', 'NomPere'=>'', 'NomMere'=>'',
    'Sexe'=>'', 'CodeCoh'=>'', 'MatSigna'=>'', 'DateIns'=>date('Y-m-d'), 'programme'=>'', 'niveau'=>'',
    'faculte'=>'', 'departement'=>''
];
$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($values as $k => &$v) {
        $v = $_POST[$k] ?? '';
        $v = is_string($v) ? trim($v) : $v;
    }
    unset($v);

    if (
        empty($values['MatEtu']) || empty($values['NomEtu']) || empty($values['PrenomEtu']) || empty($values['DateNais']) ||
        empty($values['Sexe']) || empty($values['CodeCoh']) || empty($values['MatSigna']) || empty($values['DateIns']) ||
        empty($values['programme']) || empty($values['niveau'])
    ) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }

    if (!$error) {
        // Ici ta logique d'insertion dans la base (cohorte, signataire, photo, etc.)
        // Exemple simple d'insertion (à adapter selon ta base et logique):
        /*
        $sql = "INSERT INTO etudiants (MatEtu, INE, NomEtu, PrenomEtu, DateNais, LieuNais, NomPere, NomMere, Sexe, CodeCoh, MatSigna, DateIns, programme, niveau, faculte, departement)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $values['MatEtu'], $values['INE'], $values['NomEtu'], $values['PrenomEtu'], $values['DateNais'], $values['LieuNais'],
            $values['NomPere'], $values['NomMere'], $values['Sexe'], $values['CodeCoh'], $values['MatSigna'], $values['DateIns'],
            $values['programme'], $values['niveau'], $values['faculte'], $values['departement']
        ]);
        */
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>📝 Inscription Étudiant - SGA Université de Labé</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      background: linear-gradient(135deg, #dbeafe, #93c5fd);
      font-family: 'Segoe UI', sans-serif;

      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      width: 100%;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 30px rgba(0,0,0,0.1);
      animation: fadeIn 0.6s ease-in-out;
      background: white;
      padding: 30px;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    .form-label i {
      color: #1a73e8;
      margin-right: 5px;
    }
    .btn-primary {
      background-color: #1a73e8;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0c5cd4;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="card">
    <div class="text-center mb-4">
      <img src="img/Logo_univ_labe.png" alt="Université de Labé" style="width: 70px;" class="mb-2" />
      <h4 class="fw-bold">SGA - Université de Labé</h4>
      <p class="text-muted mb-0">Inscription d’un Étudiant</p>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success text-center py-2">✅ Étudiant inscrit avec succès.</div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" autocomplete="off" class="row g-3">
      <div class="col-md-3">
        <label for="MatEtu" class="form-label"><i class="bi bi-person-badge-fill"></i>Matricule *</label>
        <input type="text" name="MatEtu" id="MatEtu" class="form-control" value="<?= htmlspecialchars($values['MatEtu']) ?>" required />
      </div>
      <div class="col-md-3">
        <label for="INE" class="form-label"><i class="bi bi-card-text"></i>INE</label>
        <input type="text" name="INE" id="INE" class="form-control" value="<?= htmlspecialchars($values['INE']) ?>" />
      </div>
      <div class="col-md-3">
        <label for="NomEtu" class="form-label"><i class="bi bi-person-fill"></i>Nom *</label>
        <input type="text" name="NomEtu" id="NomEtu" class="form-control" value="<?= htmlspecialchars($values['NomEtu']) ?>" required />
      </div>
      <div class="col-md-3">
        <label for="PrenomEtu" class="form-label"><i class="bi bi-person"></i>Prénom *</label>
        <input type="text" name="PrenomEtu" id="PrenomEtu" class="form-control" value="<?= htmlspecialchars($values['PrenomEtu']) ?>" required />
      </div>

      <div class="col-md-3">
        <label for="DateNais" class="form-label"><i class="bi bi-calendar-event"></i>Date Naissance *</label>
        <input type="date" name="DateNais" id="DateNais" class="form-control" value="<?= htmlspecialchars($values['DateNais']) ?>" required />
      </div>
      <div class="col-md-3">
        <label for="LieuNais" class="form-label"><i class="bi bi-geo-alt-fill"></i>Lieu Naissance *</label>
        <input type="text" name="LieuNais" id="LieuNais" class="form-control" value="<?= htmlspecialchars($values['LieuNais']) ?>" required />
      </div>
      <div class="col-md-3">
        <label for="Sexe" class="form-label"><i class="bi bi-gender-ambiguous"></i>Sexe *</label>
        <select name="Sexe" id="Sexe" class="form-select" required>
          <option value="">-- Choisir --</option>
          <option value="M" <?= $values['Sexe']=='M'?'selected':'' ?>>Masculin</option>
          <option value="F" <?= $values['Sexe']=='F'?'selected':'' ?>>Féminin</option>
        </select>
      </div>
      <div class="col-md-3">
        <label for="photo" class="form-label"><i class="bi bi-card-image"></i>Photo</label>
        <input type="file" name="photo" id="photo" class="form-control" accept="image/*" />
      </div>

      <div class="col-md-6">
        <label for="NomPere" class="form-label"><i class="bi bi-person-lines-fill"></i>Nom du Père</label>
        <input type="text" name="NomPere" id="NomPere" class="form-control" value="<?= htmlspecialchars($values['NomPere']) ?>" />
      </div>
      <div class="col-md-6">
        <label for="NomMere" class="form-label"><i class="bi bi-person-lines-fill"></i>Nom de la Mère</label>
        <input type="text" name="NomMere" id="NomMere" class="form-control" value="<?= htmlspecialchars($values['NomMere']) ?>" />
      </div>

      <div class="col-md-3">
        <label for="faculte" class="form-label"><i class="bi bi-building"></i>Faculté *</label>
        <select name="faculte" id="faculte" class="form-select" required>
          <option value="">-- Choisir --</option>
          <?php foreach ($facultes as $f): ?>
            <option value="<?= $f['CodeFac'] ?>" <?= $values['faculte']==$f['CodeFac']?'selected':'' ?>><?= $f['NomFac'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label for="departement" class="form-label"><i class="bi bi-building"></i>Département *</label>
        <select name="departement" id="departement" class="form-select" required></select>
      </div>
      <div class="col-md-3">
        <label for="programme" class="form-label"><i class="bi bi-book"></i>Programme (auto) *</label>
        <input type="text" name="programme" id="programme" class="form-control" value="<?= htmlspecialchars($values['programme']) ?>" readonly required />
      </div>
      <div class="col-md-3">
        <label for="niveau" class="form-label"><i class="bi bi-mortarboard"></i>Niveau *</label>
        <select name="niveau" id="niveau" class="form-select" required>
          <option value="">-- Choisir --</option>
          <option value="L1" <?= $values['niveau']=='L1'?'selected':'' ?>>Licence 1</option>
          <option value="L2" <?= $values['niveau']=='L2'?'selected':'' ?>>Licence 2</option>
          <option value="L3" <?= $values['niveau']=='L3'?'selected':'' ?>>Licence 3</option>
        </select>
      </div>

      <div class="col-md-4">
        <label for="CodeCoh" class="form-label"><i class="bi bi-calendar-check"></i>Cohorte *</label>
        <input type="text" name="CodeCoh" id="CodeCoh" class="form-control" value="<?= htmlspecialchars($values['CodeCoh']) ?>" required placeholder="Ex: 2024-2025" />
      </div>
      <div class="col-md-4">
        <label for="MatSigna" class="form-label"><i class="bi bi-person-badge"></i>Signataire *</label>
        <input type="text" name="MatSigna" id="MatSigna" class="form-control" value="<?= htmlspecialchars($values['MatSigna']) ?>" required placeholder="Ex: Nom Prénom" />
      </div>
      <div class="col-md-4">
        <label for="DateIns" class="form-label"><i class="bi bi-calendar-plus"></i>Date d’inscription *</label>
        <input type="date" name="DateIns" id="DateIns" class="form-control" value="<?= htmlspecialchars($values['DateIns']) ?>" required />
      </div>

      <div class="col-12 text-center mt-3">
        <button type="submit" class="btn btn-primary btn-lg px-5">✅ Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#faculte').change(function () {
  $.post('ajax_departements.php', { faculte: this.value }, function (data) {
    $('#departement').html(data);
    $('#programme').val('');
  });
});

$('#departement').change(function () {
  const text = $('#departement option:selected').text();
  $('#programme').val(text);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>