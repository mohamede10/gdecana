<?php
require 'config/db.php';

if (!isset($_GET['MatEtu'])) {
    die("Aucun étudiant sélectionné.");
}

$MatEtu = $_GET['MatEtu'];

// Récupérer les listes pour les selects
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll();
$departements = $pdo->query("SELECT CodeDep, NomDep FROM departements ORDER BY NomDep")->fetchAll();
$programmes = $pdo->query("SELECT CodeProg, NomProg FROM programmes ORDER BY NomProg")->fetchAll();
$licences = $pdo->query("SELECT CodeLic, NomLic FROM licences ORDER BY NomLic")->fetchAll();
$semestres = $pdo->query("SELECT CodeSemes, NivauSemes FROM semestres ORDER BY CodeSemes")->fetchAll();
$cohortes = $pdo->query("SELECT CodeCoh, Cohorte FROM cohortes ORDER BY Cohorte")->fetchAll();

// Récupérer les données de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE MatEtu = ?");
$stmt->execute([$MatEtu]);
$e = $stmt->fetch();

if (!$e) {
    die("Étudiant non trouvé.");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $NomEtu = $_POST['NomEtu'];
    $PrenomEtu = $_POST['PrenomEtu'];
    $INE = $_POST['INE'];
    $CodeFac = $_POST['CodeFac'];
    $CodeDep = $_POST['CodeDep'];
    $CodeProg = $_POST['CodeProg'];
    $CodeLic = $_POST['CodeLic'];
    $CodeSemes = $_POST['CodeSemes'];
    $CodeCoh = $_POST['CodeCoh'];
    // Ajouter d'autres champs selon besoin

    $sql = "UPDATE etudiants SET 
        NomEtu = ?, PrenomEtu = ?, INE = ?, 
        CodeFac = ?, CodeDep = ?, CodeProg = ?, 
        CodeLic = ?, CodeSemes = ?, CodeCoh = ?
        WHERE MatEtu = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$NomEtu, $PrenomEtu, $INE, $CodeFac, $CodeDep, $CodeProg, $CodeLic, $CodeSemes, $CodeCoh, $MatEtu]);

    header("Location: lister_etudiants.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier Étudiant</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Modifier Étudiant</h1>
    <form method="post">
        <div class="mb-3">
            <label>INE</label>
            <input type="text" name="INE" class="form-control" value="<?= htmlspecialchars($e['INE']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="NomEtu" class="form-control" value="<?= htmlspecialchars($e['NomEtu']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Prénom</label>
            <input type="text" name="PrenomEtu" class="form-control" value="<?= htmlspecialchars($e['PrenomEtu']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Faculté</label>
            <select name="CodeFac" class="form-select" required>
                <?php foreach ($facultes as $f): ?>
                    <option value="<?= $f['CodeFac'] ?>" <?= ($e['CodeFac']==$f['CodeFac'])?'selected':'' ?>>
                        <?= htmlspecialchars($f['NomFac']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Département</label>
            <select name="CodeDep" class="form-select" required>
                <?php foreach ($departements as $d): ?>
                    <option value="<?= $d['CodeDep'] ?>" <?= ($e['CodeDep']==$d['CodeDep'])?'selected':'' ?>>
                        <?= htmlspecialchars($d['NomDep']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Programme</label>
            <select name="CodeProg" class="form-select">
                <?php foreach ($programmes as $p): ?>
                    <option value="<?= $p['CodeProg'] ?>" <?= ($e['CodeProg']==$p['CodeProg'])?'selected':'' ?>>
                        <?= htmlspecialchars($p['NomProg']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Licence</label>
            <select name="CodeLic" class="form-select">
                <?php foreach ($licences as $l): ?>
                    <option value="<?= $l['CodeLic'] ?>" <?= ($e['CodeLic']==$l['CodeLic'])?'selected':'' ?>>
                        <?= htmlspecialchars($l['NomLic']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Semestre</label>
            <select name="CodeSemes" class="form-select">
                <?php foreach ($semestres as $s): ?>
                    <option value="<?= $s['CodeSemes'] ?>" <?= ($e['CodeSemes']==$s['CodeSemes'])?'selected':'' ?>>
                        <?= htmlspecialchars($s['NivauSemes']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Cohorte</label>
            <select name="CodeCoh" class="form-select">
                <?php foreach ($cohortes as $c): ?>
                    <option value="<?= $c['CodeCoh'] ?>" <?= ($e['CodeCoh']==$c['CodeCoh'])?'selected':'' ?>>
                        <?= htmlspecialchars($c['Cohorte']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="liste_etudiants.php" class="btn btn-secondary">Annuler</a> 
    </form><br>
</div>
</body>
</html>
