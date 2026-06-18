<?php
session_start();
require 'config/db.php';

// Récupération des facultés, années et cohortes
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes")->fetchAll();
$annees   = $pdo->query("SELECT annee FROM annees_univ ORDER BY id DESC")->fetchAll();
$cohortes = $pdo->query("SELECT CodeCoh, Cohorte FROM cohortes ORDER BY Cohorte DESC")->fetchAll();
$signataires = $pdo->query("SELECT MatSigna, NomSigna, PrenomSigna FROM signataires")->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Importer des Notes - Décanat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h4>Importation des Notes</h4>

    <form method="POST" action="traitement_import.php" enctype="multipart/form-data">
        <div class="row g-3">
            <!-- Faculté -->
            <div class="col-md-6">
                <label class="form-label">Faculté</label>
                <select name="faculte" id="faculte" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach($facultes as $fac): ?>
                        <option value="<?= htmlspecialchars($fac['CodeFac']) ?>"><?= htmlspecialchars($fac['NomFac']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Département -->
            <div class="col-md-6">
                <label class="form-label">Département</label>
                <select name="departement" id="departement" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                </select>
            </div>

            <!-- Licence / Programme -->
            <div class="col-md-6">
                <label class="form-label">Licence / Programme</label>
                <select name="programme" id="programme" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                </select>
            </div>

            <!-- Semestre -->
            <div class="col-md-6">
                <label class="form-label">Semestre</label>
                <select name="semestre" id="semestre" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                </select>
            </div>
                        <!-- Matière -->
            <div class="col-md-6">
                <label class="form-label">Matière</label>
                <select name="matiere" id="matiere" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                </select>
            </div>
            <!-- Professeur -->
            <div class="col-md-6">
                <label class="form-label">Professeur</label>
                <input type="text" id="professeur" name="professeur" class="form-control" readonly>
            </div>
            <!-- Année universitaire -->
            <div class="col-md-6">
                <label class="form-label">Année universitaire</label>
                <select name="annee_univ" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach($annees as $a): ?>
                        <option value="<?= htmlspecialchars($a['annee']) ?>"><?= htmlspecialchars($a['annee']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Cohorte -->
            <div class="col-md-6">
                <label class="form-label">Cohorte</label>
                <select name="cohorte" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach($cohortes as $c): ?>
                        <option value="<?= htmlspecialchars($c['CodeCoh']) ?>"><?= htmlspecialchars($c['Cohorte']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Fichier Excel -->
            <div class="col-md-6">
                <label class="form-label">Fichier Excel</label>
                <input type="file" name="fichier_excel" accept=".xlsx,.xls" class="form-control" required>
            </div>

            <!-- Signataire -->
            <div class="col-md-6">
                <label class="form-label">Signataire</label>
                <select name="MatSigna" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach($signataires as $s): ?>
                        <option value="<?= htmlspecialchars($s['MatSigna']) ?>">
                            <?= htmlspecialchars($s['NomSigna']." ".$s['PrenomSigna']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">Importer les notes</button>
                <a href="dashboard_admin.php" class="btn btn-danger">Annuller l'imprtation</a>
            </div>
        </div>
    </form>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Charger départements quand faculté change
    $('#faculte').on('change', function () {
        let fac = $(this).val();
        if (fac) {
            $.post("ajax_departements.php", { faculte: fac }, function (data) {
                $('#departement').html(data);
                $('#programme').html('<option value="">-- Sélectionnez --</option>');
                $('#semestre').html('<option value="">-- Sélectionnez --</option>');
            });
        }
    });
    // Charger programmes/licences quand département change
    $('#departement').on('change', function () {
        let dep = $(this).val();
        if (dep) {
            $.post("ajax_licences.php", { departement: dep }, function (data) {
                $('#programme').html(data);
                $('#semestre').html('<option value="">-- Sélectionnez --</option>');
            });
        }
    });

    // Charger semestres quand programme change
    $('#programme').on('change', function () {
        let prog = $(this).val();
        if (prog) {
            $.post("ajax_semestres.php", { programme: prog }, function (data) {
                $('#semestre').html(data);
            });
        }
    });
    });

    // Charger matières quand semestre change
    $('#semestre').on('change', function () {
        let dep  = $('#departement').val();
        let prog = $('#programme').val();
        let sem  = $(this).val();
        if (dep && prog && sem) {
            $.post("ajax_matieres.php", { departement: dep, programme: prog, semestre: sem }, function (data) {
                $('#matiere').html(data);
            });
        }
    });

    // Charger le professeur quand matière change
    $('#matiere').on('change', function () {
        let mat = $(this).val();
        if (mat) {
            $.post("ajax_professeur.php", { matiere: mat }, function (data) {
                $('#professeur').val(data);
            });
        } else {
            $('#professeur').val('');
        }
    });

</script>

</body>
</html>
