<?php
session_start();
require 'config/db.php';

// Facultés
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll(PDO::FETCH_ASSOC);

// Valeurs par défaut
$values = [
    'MatEtu'=>'', 'INE'=>'', 'NomEtu'=>'', 'PrenomEtu'=>'', 'DateNais'=>'', 'LieuNais'=>'',
    'Sexe'=>'', 'NomPere'=>'', 'NomMere'=>'', 'Nationalite'=>'', 'TelephoneEtu'=>'', 'Mail'=>'',
    'PVBAC'=>'', 'OptionBAC'=>'', 'SessionBAC'=>'', 'CentreExamen'=>'', 'Lycee'=>'', 'Moyenne'=>'',
    'Prefecture'=>'', 'SessionOrient'=>'',
    'CodeCoh'=>'', 'MatSigna'=>'', 'DateIns'=>date('Y-m-d'),
    'faculte'=>'', 'departement'=>'', 'programme'=>'', 'niveau'=>''
];

$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach ($values as $k => &$v) {
        $v = $_POST[$k] ?? '';
        $v = is_string($v) ? trim($v) : $v;
    }
    unset($v);

    // Vérification des champs obligatoires
    if (
        empty($values['MatEtu']) || empty($values['NomEtu']) || empty($values['PrenomEtu']) || empty($values['DateNais']) ||
        empty($values['Sexe']) || empty($values['CodeCoh']) || empty($values['MatSigna']) || empty($values['DateIns']) ||
        empty($values['faculte']) || empty($values['departement']) || empty($values['programme']) || empty($values['niveau'])
    ) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }

    if (!$error) {
        // ICI : logique d'insertion dans la base
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>📝 Inscription Étudiant - Université de Labé</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<style>
  body { background: linear-gradient(135deg, #dbeafe, #93c5fd); min-height: 100vh; display: flex; justify-content: center; align-items: center; font-family: 'Segoe UI', sans-serif; }
  .card { width: 100%; max-width: 1000px; padding: 30px; border-radius: 1rem; box-shadow: 0 8px 30px rgba(0,0,0,0.1); background: #fff; animation: fadeIn 0.6s ease-in-out; }
  @keyframes fadeIn { from{opacity:0; transform:scale(0.95);} to{opacity:1; transform:scale(1);} }
</style>
</head>
<body>
<div class="card">
  <div class="text-center mb-4">
    <img src="img/Logo_univ_labe.png" alt="Logo" width="70">
    <h4 class="fw-bold">Université de Labé</h4>
    <p class="text-muted mb-0">Formulaire d’inscription d’un étudiant admis</p>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success text-center py-2">✅ Étudiant inscrit avec succès.</div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-3">

    <h5 class="fw-bold mt-3">Informations personnelles</h5>
    <div class="col-md-3">
      <label class="form-label">Matricule *</label>
      <input type="text" name="MatEtu" value="<?= htmlspecialchars($values['MatEtu']) ?>" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">INE *</label>
      <input type="text" name="INE" value="<?= htmlspecialchars($values['INE']) ?>" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Nom *</label>
      <input type="text" name="NomEtu" value="<?= htmlspecialchars($values['NomEtu']) ?>" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Prénom *</label>
      <input type="text" name="PrenomEtu" value="<?= htmlspecialchars($values['PrenomEtu']) ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Date naissance *</label>
      <input type="date" name="DateNais" value="<?= htmlspecialchars($values['DateNais']) ?>" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Lieu naissance *</label>
      <input type="text" name="LieuNais" value="<?= htmlspecialchars($values['LieuNais']) ?>" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Sexe *</label>
      <select name="Sexe" class="form-select" required>
        <option value="">-- Choisir --</option>
        <option value="M" <?= $values['Sexe']=='M'?'selected':'' ?>>Masculin</option>
        <option value="F" <?= $values['Sexe']=='F'?'selected':'' ?>>Féminin</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Photo</label>
      <input type="file" name="photo" class="form-control">
    </div>

    <div class="col-md-6"><label class="form-label">Nom du père</label><input type="text" name="NomPere" value="<?= htmlspecialchars($values['NomPere']) ?>" class="form-control"></div>
    <div class="col-md-6"><label class="form-label">Nom de la mère</label><input type="text" name="NomMere" value="<?= htmlspecialchars($values['NomMere']) ?>" class="form-control"></div>

    <div class="col-md-4"><label class="form-label">Nationalité</label><input type="text" name="Nationalite" value="<?= htmlspecialchars($values['Nationalite']) ?>" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Téléphone</label><input type="text" name="TelephoneEtu" value="<?= htmlspecialchars($values['TelephoneEtu']) ?>" class="form-control"></div>
    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="Mail" value="<?= htmlspecialchars($values['Mail']) ?>" class="form-control"></div>

    <h5 class="fw-bold mt-3">Admission au Bac</h5>
    <?php foreach(['PVBAC'=>'PV Bac','OptionBAC'=>'Option Bac','SessionBAC'=>'Session Bac','CentreExamen'=>'Centre examen','Lycee'=>'Lycée','Moyenne'=>'Moyenne','Prefecture'=>'Préfecture','SessionOrient'=>'Session orientation'] as $field=>$label): ?>
    <div class="col-md-3">
      <label class="form-label"><?= $label ?></label>
      <input type="<?= $field=='Moyenne'?'number':'text' ?>" name="<?= $field ?>" value="<?= htmlspecialchars($values[$field]) ?>" class="form-control" <?= $field=='Moyenne'?'step="0.01"':'' ?>>
    </div>
    <?php endforeach; ?>

    <h5 class="fw-bold mt-3">Cursus et inscription</h5>
   
    <div class="col-md-3">
      <label class="form-label">Faculté *</label>
      <select name="faculte" id="faculte" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($facultes as $f): ?>
          <option value="<?= $f['CodeFac'] ?>" <?= $values['faculte']==$f['CodeFac']?'selected':'' ?>><?= htmlspecialchars($f['NomFac']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3"><label class="form-label">Département *</label><select name="departement" id="departement" class="form-select" required></select></div>
    <div class="col-md-3">
  <label class="form-label">Programme *</label>
  <input type="text" name="programme" id="programme" class="form-control" value="<?= htmlspecialchars($values['programme']) ?>" readonly required placeholder="Sélectionner un département">
</div>
<div class="col-md-3">
  <label class="form-label">Niveau *</label>
  <select name="niveau" id="niveau" class="form-select" required>
    <option value="">-- Choisir --</option>
    <option value="Licence 1" <?= $values['niveau']=='Licence 1'?'selected':'' ?>>Licence 1</option>
    <option value="Licence 2" <?= $values['niveau']=='Licence 2'?'selected':'' ?>>Licence 2</option>
    <option value="Licence 3" <?= $values['niveau']=='Licence 3'?'selected':'' ?>>Licence 3</option>
  </select>
</div>

 <div class="col-md-3"><label class="form-label">Cohorte *</label><input type="text" name="CodeCoh" value="<?= htmlspecialchars($values['CodeCoh']) ?>" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Signataire *</label><input type="text" name="MatSigna" value="<?= htmlspecialchars($values['MatSigna']) ?>" class="form-control" required></div>
    <div class="col-md-3"><label class="form-label">Date inscription *</label><input type="date" name="DateIns" value="<?= htmlspecialchars($values['DateIns']) ?>" class="form-control" required></div>


    <div class="col-12 text-center mt-3"><button type="submit" class="btn btn-primary btn-lg px-5">✅ Enregistrer</button></div>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#faculte').change(function(){
  $.post('ajax_departements.php',{faculte:this.value},function(data){
    $('#departement').html(data);
    $('#programme').val('');
  });
});

$('#departement').change(function(){
  var nomDept = $('#departement option:selected').text();
  $('#programme').val('Licence ' + nomDept);
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
