<?php 
require 'config/db.php'; // Connexion

// Chargement des données
$cohortes = $pdo->query("SELECT CodeCoh, Cohorte FROM cohortes ORDER BY Cohorte")->fetchAll();
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll();
$departements = $pdo->query("SELECT CodeDep, NomDep, CodeFac FROM departements ORDER BY NomDep")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Génération Attestation</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f0f2f5;
        padding: 40px;
    }
    h2 {
        text-align: center;
        color: #0d6efd;
        margin-bottom: 25px;
    }
    form {
        max-width: 600px;
        margin: auto;
        background: #fff;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    label {
        display: inline-block;
        width: 150px;
        margin-top: 12px;
        font-weight: 500;
        color: #333;
    }
    input, select {
        padding: 8px;
        width: calc(100% - 160px);
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fafafa;
        transition: border-color 0.2s;
    }
    input:focus, select:focus {
        border-color: #0d6efd;
        background: #fff;
        outline: none;
    }
    fieldset {
        margin-top: 20px;
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 6px;
    }
    legend {
        font-weight: 600;
        color: #555;
    }
    button {
        margin-top: 25px;
        width: 100%;
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 10px;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }
    button:hover {
        background: #0b5ed7;
    }
</style>
</head>
<body>

<h2>📄 Génération de documents étudiant</h2>

<form action="generer_attestation.php" method="POST">
    <!-- INE -->
    <label for="identifiant">INE :</label>
    <input type="text" name="identifiant" id="identifiant" placeholder="Saisir INE" required>
    <br><br>

    <!-- Type d’attestation -->
    <label for="type">Type d’attestation :</label>
    <select name="type" id="type" required>
        <option value="">-- Sélectionner --</option>
        <option value="admission">Attestation d'admission</option>
        <option value="niveau">Attestation de niveau </option>
        <option value="inscription">Attestation d'inscription</option>
        <option value="reinscription">Attestation de réinscription</option>
        <option value="releve">Relevé de notes</option>
        <option value="carte">Carte scolaire</option>
    </select>
    <br><br>

    <!-- Filtrage complémentaire -->
    <fieldset id="filtrage">
        <legend>🔍 Filtrage complémentaire (facultatif)</legend>

        <div id="cohorteDiv">
            <label for="cohorte">Cohorte :</label>
            <select name="cohorte" id="cohorte">
                <option value="">-- Sélectionner Cohorte --</option>
                <?php foreach ($cohortes as $coh): ?>
                    <option value="<?= htmlspecialchars($coh['CodeCoh']) ?>">
                        <?= htmlspecialchars($coh['Cohorte']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br><br>
        </div>

        <label for="faculte">Faculté :</label>
        <select name="faculte" id="faculte">
            <option value="">-- Sélectionner Faculté --</option>
            <?php foreach ($facultes as $fac): ?>
                <option value="<?= htmlspecialchars($fac['CodeFac']) ?>">
                    <?= htmlspecialchars($fac['NomFac']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="departement">Département :</label>
        <select name="departement" id="departement">
            <option value="">-- Sélectionner Département --</option>
            <?php foreach ($departements as $dep): ?>
                <option value="<?= htmlspecialchars($dep['CodeDep']) ?>" data-fac="<?= htmlspecialchars($dep['CodeFac']) ?>">
                    <?= htmlspecialchars($dep['NomDep']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Licence (visible seulement si Relevé sélectionné) -->
        <div id="licenceDiv" style="display:none;">
            <label for="licence">Licence :</label>
            <select name="licence" id="licence">
                <option value="">-- Sélectionner Licence --</option>
                <option value="Licence1">Licence 1</option>
                <option value="Licence2">Licence 2</option>
                <option value="Licence3">Licence 3</option>
            </select>
            <br><br>
        </div>

        <!-- Semestre (visible seulement si Relevé sélectionné) 
        <div id="semestreDiv" style="display:none;">
            <label for="semestre">Semestre :</label>
            <select name="semestre" id="semestre">
                <option value="">-- Sélectionner Semestre --</option>
                <option value="Semestre1">Semestre 1</option>
                <option value="Semestre2">Semestre 2</option>
                <option value="Semestre3">Semestre 3</option>
                <option value="Semestre4">Semestre 4</option>
                <option value="Semestre5">Semestre 5</option>
                <option value="Semestre6">Semestre 6</option>
            </select>
        </div>-->

    </fieldset>

    <button type="submit">✨ Générer</button>
</form>

<script>
// Gestion des affichages dynamiques
document.getElementById('type').addEventListener('change', function() {
    const filtrage = document.getElementById('filtrage');
    const cohorteDiv = document.getElementById('cohorteDiv');
    const licenceDiv = document.getElementById('licenceDiv');
    const semestreDiv = document.getElementById('semestreDiv');

    if (this.value === 'carte') {
        filtrage.style.display = 'none';
        licenceDiv.style.display = 'none';
        semestreDiv.style.display = 'none';
    } else {
        filtrage.style.display = 'block';
        cohorteDiv.style.display = (this.value === 'admission') ? 'none' : 'block';

        if (this.value === 'releve') {
            licenceDiv.style.display = 'block';
            semestreDiv.style.display = 'block';
        } else {
            licenceDiv.style.display = 'none';
            semestreDiv.style.display = 'none';
        }
    }
});

// Filtrage dynamique des départements selon faculté
document.getElementById('faculte').addEventListener('change', function () {
    const facCode = this.value;
    const depSelect = document.getElementById('departement');
    Array.from(depSelect.options).forEach(option => {
        option.style.display = (option.dataset.fac === facCode || option.value === '') ? 'block' : 'none';
    });
    depSelect.value = '';
});
</script>

</body>
</html>
