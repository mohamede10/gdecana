<?php
require 'config/db.php';

// Récupère MatEtu depuis l'URL
$MatEtu = $_GET['MatEtu'] ?? '';

if (!$MatEtu) {
    die("❌ Matricule manquant.");
}

// Prépare et exécute la requête
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE MatEtu = ?");
$stmt->execute([$MatEtu]);
$etu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etu) {
    die("❌ Étudiant introuvable.");
}

// Formatage
$jour = date('d/m/Y');
$annee_univ = date('Y', strtotime($etu['DateNais'])); // ou autre champ approprié
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Attestation d'inscription</title>
<style>
  body { font-family: 'Times New Roman', serif; margin: 50px; line-height: 1.6; }
  .center { text-align: center; }
  .title { font-size: 18pt; font-weight: bold; margin: 20px 0; }
  .signature { margin-top: 40px; }
  .info strong { font-weight: bold; }
</style>
</head>
<body>

<div class="center">
  <div><strong>Université de Labé</strong></div>
  <div>Service Scolarité</div>
  <div class="title">ATTESTATION D’INSCRIPTION</div>
</div>

<p>Cohorte : <strong><?= htmlspecialchars($etu['CodeCoh']) ?></strong></p>
<p>Licence : <strong><!-- niveau --></strong> / Année universitaire : <strong><?= $annee_univ ?></strong></p>

<p>Je soussigné, Issa SOUMARE, Chef de Service de la Scolarité, atteste que l’étudiant(e) :</p>

<div class="info">
<ul>
  <li>Prénoms : <strong><?= htmlspecialchars($etu['PrenomEtu']) ?></strong></li>
  <li>Nom : <strong><?= htmlspecialchars($etu['NomEtu']) ?></strong></li>
  <li>Né(e) le : <strong><?= htmlspecialchars($etu['DateNais']) ?></strong> à <strong><?= htmlspecialchars($etu['LieuNais']) ?></strong></li>
  <li>Fils/Fille de : <strong><?= htmlspecialchars($etu['NomPere']) ?></strong> et de <strong><?= htmlspecialchars($etu['NomMere']) ?></strong></li>
  <li>INE : <strong><?= htmlspecialchars($etu['INE']) ?></strong></li>
</ul>
</div>

<p>Est inscrit(e) à l’Université de Labé.</p>

<p>En foi de quoi, la présente attestation est délivrée pour servir et valoir ce que de droit.</p>

<p>Labé, le <?= $jour ?></p>

<div class="signature">
  <strong>Le Chef de Service de la Scolarité</strong><br>
  Issa SOUMARE
</div>

</body>
</html>
