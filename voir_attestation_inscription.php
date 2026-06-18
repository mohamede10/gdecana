<?php
session_start();
require 'config/db.php';

// Vérifier la connexion
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

// Récupération de l'ID de l'attestation depuis GET
$id = $_GET['id'] ?? null;
if (!$id) die("Attestation non spécifiée.");

// Récupérer l'attestation d'inscription et les infos de l'étudiant
$stmt = $pdo->prepare("
    SELECT a.*, e.PrenomEtu, e.NomEtu, e.INE, e.DateNais, e.LieuNais,
           e.NomPere, e.NomMere, p.NomProg, d.NomDep, f.NomFac, c.Cohorte, a.annee_univ
    FROM attestations_inscription a
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

// Variables
$nom = strtoupper($attestation['NomEtu']);
$prenom = ucfirst(strtolower($attestation['PrenomEtu']));
$naissance = date('d/m/Y', strtotime($attestation['DateNais']));
$lieu = $attestation['LieuNais'];
$ine = $attestation['INE'];
$cohorte = $attestation['Cohorte'];
$faculte = $attestation['NomFac'];
$departement = $attestation['NomDep'];
$programme = $attestation['NomProg'];
$annee_univ = $attestation['annee_univ'];
$numero_attestation = $attestation['numero_attestation'];
$date_jour = date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Attestation d’Inscription - <?= $prenom ?> <?= $nom ?></title>
<style>
body { font-family: 'Georgia', serif; margin:0; padding:0; background:#f5f5f5; }
.attestation {
    width: 210mm;
    height: 297mm;
    margin: 20px auto;
    padding: 40px;
    background: #fff;
    border: 2px solid #000;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
}
.attestation::before {
    content: "UNIVERSITE DE LABE";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-size: 100px;
    font-weight: bold;
    color: rgba(0,0,0,0.07);
    z-index: 0;
    pointer-events: none;
    white-space: nowrap;
}
.logo {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 100px;
}
.center { text-align:center; z-index:1; position:relative; }
.titre { font-size:26px; margin:20px 0; text-decoration:underline; font-weight:600; color:#2c3e50; }
.contenu { margin-top:25px; font-size:16px; line-height:1.6; color:#000; z-index:1; position:relative; }
.footer { margin-top:50px; font-size:14px; z-index:1; position:relative; text-align:center; }
.signature { margin-top:60px; text-align:right; }
.cachet { display:block; margin:10px auto 0 auto; opacity:0.7; width:120px; height:auto; }
@media print {
    body { background:none; }
    .attestation { box-shadow:none; border:none; margin:0; page-break-after: always; }
}
</style>
</head>
<body>
<div class="attestation">
    <img src="img/Logo_univ_labe.png" class="logo" alt="Logo Université">
    <div class="center">
        <p>UL /SS/2025</p>
        <p class="titre">ATTESTATION D’INSCRIPTION</p>
    </div>
    <div class="contenu">
        <p>Je soussigné, <strong>Issa SOUMARE</strong>, atteste que <strong><?= $prenom ?> <?= $nom ?></strong>, né(e) le <?= $naissance ?> à <?= $lieu ?>, fils/fille de <?= $attestation['NomPere'] ?> et <?= $attestation['NomMere'] ?>,</p>
        <p>est inscrit(e) à l’Université de Labé sous l’INE : <strong><?= $ine ?></strong>.</p>
        <p>Faculté : <strong><?= $faculte ?></strong>, Département : <strong><?= $departement ?></strong>, Programme : <strong><?= $programme ?></strong>.</p>
        <p>Cohorte : <strong><?= $cohorte ?></strong>, Année universitaire : <strong><?= $annee_univ ?></strong></p>
    </div>
    <div class="footer">
        <p>Labé, le <?= $date_jour ?></p>
        <div class="signature">
            <p>Le Recteur</p>
            <br>
            <img src="images/cachet.png" class="cachet" alt="Cachet officiel">
            <br>
            ______________________
        </div>
        <p>Numéro d'attestation : <strong><?= $numero_attestation ?></strong></p>
    </div>
</div>
</body>
</html>
