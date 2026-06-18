<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Attestation non spécifiée.");

$stmt = $pdo->prepare("
    SELECT a.*, e.PrenomEtu, e.NomEtu, e.INE, e.DateNais, e.LieuNais,
           e.NomPere, e.NomMere, p.NomProg, d.NomDep, f.NomFac, c.Cohorte
    FROM attestations_niveaux a
    JOIN etudiants e ON a.MatEtu = e.MatEtu
    LEFT JOIN programmes p ON e.CodeProg = p.CodeProg
    LEFT JOIN departements d ON p.CodeDep = d.CodeDep
    LEFT JOIN facultes f ON d.CodeFac = f.CodeFac
    LEFT JOIN cohortes c ON e.CodeCoh = c.CodeCoh
    WHERE a.id = :id
");
$stmt->execute(['id' => $id]);
$attestation = $stmt->fetch();
if (!$attestation) die("Attestation non trouvée.");

// Variables utiles
$nom = strtoupper($attestation['NomEtu']);
$prenom = ucfirst(strtolower($attestation['PrenomEtu']));
$ine = $attestation['INE'];
$niveau = $attestation['niveau'];
$programme = $attestation['NomProg'];
$faculte = $attestation['NomFac'];
$departement = $attestation['NomDep'];
$cohorte = $attestation['Cohorte'];
$annee_univ = $attestation['annee_univ'];
$numero_attestation = $attestation['numero_attestation'];
$date_jour = date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Attestation de Niveau - <?= $prenom ?> <?= $nom ?></title>
<style>
body {
    font-family: 'Georgia', serif;
    margin: 0;
    padding: 0;
    background: #f5f5f5;
}

/* Conteneur A4 portrait */
.attestation {
    width: 210mm;
    height: 297mm;
    margin: 20px auto;
    padding: 40px;
    position: relative;
    background: #fff;
    border: 2px solid #000;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
    box-sizing: border-box;
    overflow: hidden;
}

/* Filigrane */
.attestation::before {
    content: "UNIVERSITE DE LABE";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-size: 100px;
    font-weight: bold;
    color: rgba(0,0,0,0.07);
    white-space: nowrap;
    z-index: 0;
    pointer-events: none;
}

/* Logo université */
.logo {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 100px;
    height: auto;
}

/* Cachet officiel */
.cachet {
    display: block;
    margin: 10px auto 0 auto;
    opacity: 0.7;
    width: 120px;
    height: auto;
}

/* Texte */
.center { text-align: center; }
.titre { font-size: 24px; margin: 20px 0; text-decoration: underline; font-weight: bold; }
.contenu { margin-top: 25px; font-size: 16px; line-height: 1.6; text-align: justify; }
.footer { margin-top: 40px; font-size: 14px; }
.signature { margin-top: 60px; text-align: right; }

/* Impression A4 */
@media print {
    body { background: none; }
    .attestation {
        box-shadow: none;
        border: none;
        margin: 0;
        page-break-after: always;
    }
}
</style>
</head>
<body>
<div class="attestation">
    <img src="img/Logo_univ_labe.png" class="logo" alt="Logo Université">
    <div class="overlay">
        <div class="center">
            <p>Numéro d'attestation : <strong><?= $numero_attestation ?></strong></p>
            <p class="titre">ATTESTATION DE NIVEAU</p>
        </div>
        <div class="contenu">
            <p>Je soussigné, <strong>Issa SOUMARE</strong>, atteste que <strong><?= $prenom ?> <?= $nom ?></strong> (INE : <?= $ine ?>)</p>
            <p>a suivi avec succès les cours du <strong><?= $niveau ?></strong> pour l'année universitaire <strong><?= $annee_univ ?></strong>.</p>
            <p>Faculté : <strong><?= $faculte ?></strong>, Département : <strong><?= $departement ?></strong>, Programme : <strong><?= $programme ?></strong>.</p>
            <p>Cohorte : <strong><?= $cohorte ?></strong></p>
        </div>
        <div class="footer center">
            <p>Labé, le <?= $date_jour ?></p>
            <div class="signature">
                <p>Le Recteur</p>
                <br>
                <img src="images/cachet.png" class="cachet" alt="Cachet officiel">
                <br>
                ______________________
            </div>
        </div>
    </div>
</div>
</body>
</html>
