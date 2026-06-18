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
    'Prefecture'=>'', 'SessionOrient'=>'', 'CodeCoh'=>'', 'MatSigna'=>'', 'DateIns'=>date('Y-m-d'),
    'faculte'=>'', 'departement'=>'', 'programme'=>'', 'niveau'=>'', 'semestre'=>''
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
        empty($values['faculte']) || empty($values['departement']) || empty($values['programme']) || empty($values['niveau'])
    ) {
        $error = "⚠️ Veuillez remplir tous les champs obligatoires.";
    }

    if (!$error) {
        try {
            // Cohorte
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cohortes WHERE CodeCoh = ?");
            $stmt->execute([$values['CodeCoh']]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO cohortes (CodeCoh, Cohorte) VALUES (?, ?)");
                $stmt->execute([$values['CodeCoh'], $values['CodeCoh']]);
            }

            // Signataire
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM signataires WHERE MatSigna = ?");
            $stmt->execute([$values['MatSigna']]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $pdo->prepare("INSERT INTO signataires (MatSigna, NomSigna, PrenomSigna) VALUES (?, ?, ?)");
                $stmt->execute([$values['MatSigna'], $values['MatSigna'], '']);
            }

            // Programme
            $stmt = $pdo->prepare("SELECT CodeProg FROM programmes WHERE NomProg = ? AND CodeDep = ?");
            $stmt->execute([$values['programme'], $values['departement']]);
            $codeProg = $stmt->fetchColumn();
            if (!$codeProg) {
                $codeProg = 'PRG_' . time();
                $stmt = $pdo->prepare("INSERT INTO programmes (CodeProg, CodeDep, NomProg) VALUES (?, ?, ?)");
                $stmt->execute([$codeProg, $values['departement'], $values['programme']]);
            }

            // Licence automatique selon niveau
            switch ($values['niveau']) {
                case 'Licence 1': $nomLic = 'Licence 1'; break;
                case 'Licence 2': $nomLic = 'Licence 2'; break;
                case 'Licence 3': $nomLic = 'Licence 3'; break;
                default: throw new Exception("Niveau inconnu : " . htmlspecialchars($values['niveau']));
            }

            // Vérifier si la licence existe pour le département
            $stmt = $pdo->prepare("SELECT CodeLic FROM licences WHERE NomLic = ? AND CodeDep = ?");
            $stmt->execute([$nomLic, $values['departement']]);
            $codeLic = $stmt->fetchColumn();
            if (!$codeLic) {
                $stmt = $pdo->prepare("INSERT INTO licences (NomLic, CodeDep) VALUES (?, ?)");
                $stmt->execute([$nomLic, $values['departement']]);
                $codeLic = $pdo->lastInsertId();
            }

            // Niveau
            $stmtNiv = $pdo->prepare("SELECT CodeNiv FROM niveaux WHERE Niveau = ?");
            $stmtNiv->execute([$values['niveau']]);
            $codeNiv = $stmtNiv->fetchColumn();
            if (!$codeNiv) throw new Exception("Niveau introuvable pour : " . htmlspecialchars($values['niveau']));

            // Semestre
            $stmt = $pdo->prepare("SELECT CodeSemes FROM semestres WHERE NivauSemes = ? AND CodeNiv = ?");
            $stmt->execute([$values['semestre'], $codeNiv]);
            $codeSemes = $stmt->fetchColumn();
            if (!$codeSemes) {
                $codeSemes = 'SEM_' . time();
                $stmt = $pdo->prepare("INSERT INTO semestres (CodeSemes, CodeNiv, NivauSemes) VALUES (?, ?, ?)");
                $stmt->execute([$codeSemes, $codeNiv, $values['semestre']]);
            }

            // Photo
            $photoName = null;
            if (!empty($_FILES['photo']['name'])) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photoName = uniqid('photo_') . '.' . strtolower($ext);
                move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/photos/' . $photoName);
            }

            // Insertion étudiant
            $stmt = $pdo->prepare("
                INSERT INTO etudiants (
                    MatEtu, INE, NomEtu, PrenomEtu, DateNais, LieuNais, Sexe,
                    PVBAC, OptionBAC, SessionBAC, NomPere, NomMere, Nationalite, CentreExamen,
                    Lycee, Moyenne, Prefecture, SessionOrient, TelephoneEtu, Mail,
                    CodeCoh, CodeFac, CodeDep, CodeProg, CodeLic, CodeSemes, photo
                ) VALUES (
                    :MatEtu, :INE, :NomEtu, :PrenomEtu, :DateNais, :LieuNais, :Sexe,
                    :PVBAC, :OptionBAC, :SessionBAC, :NomPere, :NomMere, :Nationalite, :CentreExamen,
                    :Lycee, :Moyenne, :Prefecture, :SessionOrient, :TelephoneEtu, :Mail,
                    :CodeCoh, :CodeFac, :CodeDep, :CodeProg, :CodeLic, :CodeSemes, :photo
                )
            ");
            $stmt->execute([
                ':MatEtu' => $values['MatEtu'], ':INE' => $values['INE'], ':NomEtu' => $values['NomEtu'], ':PrenomEtu' => $values['PrenomEtu'],
                ':DateNais' => $values['DateNais'], ':LieuNais' => $values['LieuNais'], ':Sexe' => $values['Sexe'],
                ':PVBAC' => $values['PVBAC'], ':OptionBAC' => $values['OptionBAC'], ':SessionBAC' => $values['SessionBAC'],
                ':NomPere' => $values['NomPere'], ':NomMere' => $values['NomMere'], ':Nationalite' => $values['Nationalite'],
                ':CentreExamen' => $values['CentreExamen'], ':Lycee' => $values['Lycee'], ':Moyenne' => $values['Moyenne'] !== '' ? $values['Moyenne'] : null,
                ':Prefecture' => $values['Prefecture'], ':SessionOrient' => $values['SessionOrient'], ':TelephoneEtu' => $values['TelephoneEtu'],
                ':Mail' => $values['Mail'], ':CodeCoh' => $values['CodeCoh'], ':CodeFac' => $values['faculte'], ':CodeDep' => $values['departement'],
                ':CodeProg' => $codeProg, ':CodeLic' => $codeLic, ':CodeSemes' => $codeSemes, ':photo' => $photoName
            ]);

            // Insertion inscription
            $stmt = $pdo->prepare("INSERT INTO inscriptions (MatEtu, MatSigna, CodeNiv, DateIns) VALUES (:MatEtu, :MatSigna, :CodeNiv, :DateIns)");
            $stmt->execute([
                ':MatEtu' => $values['MatEtu'], ':MatSigna' => $values['MatSigna'], ':CodeNiv' => $codeNiv, ':DateIns' => $values['DateIns']
            ]);

            $success = true;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();
                $champ = '';
                if (strpos($msg, 'MatEtu') !== false) $champ = 'Matricule';
                elseif (strpos($msg, 'INE') !== false) $champ = 'INE';
                elseif (strpos($msg, 'TelephoneEtu') !== false) $champ = 'Téléphone';
                elseif (strpos($msg, 'Mail') !== false) $champ = 'Email';

                if ($champ === '') $error = "⚠️ Une valeur unique est déjà enregistrée dans la base de données. Veuillez vérifier et réessayer";
            } else {
                $error = "Erreur : " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Inscription Étudiant - Université de Labé</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
<style>
body { background: linear-gradient(135deg, #dbeafe, #93c5fd); min-height: 100vh; display: flex; justify-content: center; align-items: center; font-family: 'Segoe UI', sans-serif; }
.card { width: 100%; max-width: 1000px; padding: 30px; border-radius: 1rem; box-shadow: 0 8px 30px rgba(0,0,0,0.1); background: #fff; animation: fadeIn 0.6s ease-in-out; }
@keyframes fadeIn { from{opacity:0; transform:scale(0.95);} to{opacity:1; transform:scale(1);} }
.form-control, .form-select { transition: all 0.3s ease-in-out; }
.form-control:focus, .form-select:focus { box-shadow: 0 0 8px rgba(59,130,246,0.5); transform: scale(1.02); }
.input-group-text { background-color: #006affff; border-right: none; color: #fff; }
.input-group .form-control, .input-group .form-select { border-left: none; }
.btn-primary { transition: all 0.3s ease-in-out; }
.btn-primary:hover { background-color: #2563eb; transform: translateY(-2px); }
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


<!-- Le formulaire reste identique à ton fichier original, sans champ licence -->
<!-- ... (tous les champs HTML et JS pour faculté, département, programme, niveau, semestre, cohorte, signataire, date) ... -->
 <form method="POST" enctype="multipart/form-data" class="row g-3">
<h5 class="fw-bold mt-3">Informations personnelles</h5>

<?php
$champs = [
    ['MatEtu','Matricule *','person-badge'],
    ['INE','INE *','card-list'],
    ['NomEtu','Nom *','person'],
    ['PrenomEtu','Prénom *','person-lines-fill'],
    ['DateNais','Date naissance *','calendar-event','date'],
    ['LieuNais','Lieu naissance *','geo-alt'],
];
foreach ($champs as $c) {
    $type = $c[3] ?? 'text';
    echo "<div class='col-md-3'><label class='form-label'>{$c[1]}</label>
    <div class='input-group'>
        <span class='input-group-text'><i class='bi bi-{$c[2]}'></i></span>
        <input type='$type' name='{$c[0]}' value='".htmlspecialchars($values[$c[0]])."' class='form-control' required>
    </div></div>";
}
?>

<div class="col-md-3">
<label class="form-label">Sexe *</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
    <select name="Sexe" class="form-select" required>
        <option value="">-- Choisir --</option>
        <option value="M" <?= $values['Sexe']=='M'?'selected':'' ?>>Masculin</option>
        <option value="F" <?= $values['Sexe']=='F'?'selected':'' ?>>Féminin</option>
    </select>
</div>
</div>

<div class="col-md-3">
<label class="form-label">Photo</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-image"></i></span>
    <input type="file" name="photo" class="form-control">
</div>
</div>

<?php
$parents = [
    ['NomPere','Nom du père','person'],
    ['NomMere','Nom de la mère','person']
];
foreach ($parents as $p) {
    echo "<div class='col-md-6'><label class='form-label'>{$p[1]}</label>
    <div class='input-group'>
        <span class='input-group-text'><i class='bi bi-{$p[2]}'></i></span>
        <input type='text' name='{$p[0]}' value='".htmlspecialchars($values[$p[0]])."' class='form-control'>
    </div></div>";
}
?>

<?php
$contacts = [
    ['Nationalite','Nationalité','flag'],
    ['TelephoneEtu','Téléphone','telephone'],
    ['Mail','Email','envelope-at','email']
];
foreach ($contacts as $ct) {
    $type = $ct[3] ?? 'text';
    echo "<div class='col-md-4'><label class='form-label'>{$ct[1]}</label>
    <div class='input-group'>
        <span class='input-group-text'><i class='bi bi-{$ct[2]}'></i></span>
        <input type='$type' name='{$ct[0]}' value='".htmlspecialchars($values[$ct[0]])."' class='form-control'>
    </div></div>";
}
?>

<h5 class="fw-bold mt-3">Admission au Bac</h5>
<?php
$bac = [
    ['PVBAC','PV Bac','file-earmark-text'],
    ['OptionBAC','Option Bac','book'],
    ['SessionBAC','Session Bac','calendar3'],
    ['CentreExamen','Centre examen','geo'],
    ['Lycee','Lycée','building'],
    ['Moyenne','Moyenne','percent','number'],
    ['Prefecture','Préfecture','geo-fill'],
    ['SessionOrient','Session orientation','calendar2']
];
foreach ($bac as $b) {
    $type = $b[3] ?? 'text';
    echo "<div class='col-md-3'><label class='form-label'>{$b[1]}</label>
    <div class='input-group'>
        <span class='input-group-text'><i class='bi bi-{$b[2]}'></i></span>
        <input type='$type' name='{$b[0]}' value='".htmlspecialchars($values[$b[0]])."' class='form-control' ".($type=='number'?'step="0.01"':'').">
    </div></div>";
}
?>

<h5 class="fw-bold mt-3">Cursus et inscription</h5>
<div class="col-md-3">
<label class="form-label">Faculté *</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-building-check"></i></span>
    <select name="faculte" id="faculte" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php foreach ($facultes as $f): ?>
        <option value="<?= $f['CodeFac'] ?>" <?= $values['faculte']==$f['CodeFac']?'selected':'' ?>><?= htmlspecialchars($f['NomFac']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
</div>

<div class="col-md-3">
<label class="form-label">Département *</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
    <select name="departement" id="departement" class="form-select" required></select>
</div>
</div>

<div class="col-md-3">
<label class="form-label">Programme *</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-journal-text"></i></span>
    <input type="text" name="programme" id="programme" class="form-control" value="<?= htmlspecialchars($values['programme']) ?>" readonly required placeholder="Sélectionner un département">
</div>
</div>

<div class="col-md-3">
<label class="form-label">Niveau *</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-layers"></i></span>
    <select name="niveau" id="niveau" class="form-select" required>
        <option value="">-- Choisir --</option>
        <option value="Licence 1" <?= $values['niveau']=='Licence 1'?'selected':'' ?>>Licence 1</option>
        <option value="Licence 2" <?= $values['niveau']=='Licence 2'?'selected':'' ?>>Licence 2</option>
        <option value="Licence 3" <?= $values['niveau']=='Licence 3'?'selected':'' ?>>Licence 3</option>
    </select>
</div>
</div>

<div class="col-md-3">
<label class="form-label">Semestre *</label>
<div class="input-group">
    <span class="input-group-text"><i class="bi bi-calendar-range"></i></span>
    <select name="semestre" id="semestre" class="form-select" required>
        <option value="">-- Choisir --</option>
        <?php for($i=1;$i<=6;$i++): ?>
        <option value="Semestre <?= $i ?>" <?= $values['semestre']=="Semestre $i"?'selected':'' ?>>Semestre <?= $i ?></option>
        <?php endfor; ?>
    </select>
</div>
</div>

<?php
$autres = [
    ['CodeCoh','Cohorte *','hash'],
    ['MatSigna','Signataire *','pencil'],
    ['DateIns','Date inscription *','calendar-plus','date']
];
foreach ($autres as $a) {
    $type = $a[3] ?? 'text';
    echo "<div class='col-md-3'><label class='form-label'>{$a[1]}</label>
    <div class='input-group'>
        <span class='input-group-text'><i class='bi bi-{$a[2]}'></i></span>
        <input type='$type' name='{$a[0]}' value='".htmlspecialchars($values[$a[0]])."' class='form-control' required>
    </div></div>";
}
?>

<div class="col-12 text-center mt-3">
    <button type="submit" class="btn btn-primary btn-lg px-5">
        <i class="bi bi-check-circle"></i> Enregistrer
    </button>
    <a href="dashboard_admin.php" class="btn btn-danger btn-lg px-5">
        <i class="bi bi-close"></i> Fermer
    </a>
</div>
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
