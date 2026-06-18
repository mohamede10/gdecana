<?php
// templates/reinscription.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attestation de Réinscription</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .numero {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 20px;
        }
        .titre {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14pt;
        }
        .sous-titre {
            text-align: center;
            margin-bottom: 25px;
            font-size: 12pt;
        }
        .contenu {
            text-align: justify;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        .cachet-signature {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cachet-signature img {
            max-height: 100px;
        }
    </style>
</head>
<body>
   <table style="width: 100%; border-collapse: collapse;">
  <tr>
    <!-- Gauche -->
    <td style="text-align: left; font-size: 9pt; width: 30%;">
      <b>République de Guinée</b><br>
      Travail · Justice · Solidarité
    </td>
    <!-- Centre -->
    <td style="text-align: center; width: 30%;">
      <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 70px;">
    </td>
    <!-- Droite -->
    <td style="text-align: center; font-size: 9pt; width: 30%; line-height: 1.4;">
      Service Scolarité <br>
      +224 629005813 / 629005814 <br>
      scolarite@univ-labe.edu.gn <br>
      www.univ-labe.edu.gn
    </td>
  </tr>
</table>
<hr>

    <p class="numero">
        N° <?= rand(1000,9999) ?>/UNIV-LAB/<?= date('Y') ?>
    </p>

    <p class="titre">ATTESTATION DE RÉINSCRIPTION</p>

    <p class="sous-titre">
        Réinscription en <b><?= htmlspecialchars($etudiant['Niveau']) ?></b><br>
        <?= htmlspecialchars($etudiant['Licence']) ?> (semestres <?= htmlspecialchars($etudiant['Semestres']) ?>)<br>
        Année Universitaire <?= htmlspecialchars($etudiant['AnneeUniv']) ?>
    </p>

    <p class="contenu">
        Le Recteur de l’Université de Labé atteste que l’étudiant(e) 
        <b><?= htmlspecialchars($etudiant['Prenom']) ?> <?= htmlspecialchars($etudiant['Nom']) ?></b>, 
        né(e) le <?= date("d/m/Y", strtotime($etudiant['DateNaissance'])) ?> à <?= htmlspecialchars($etudiant['LieuNaissance']) ?>,
</b>, 
        fils/fille de <b><?= htmlspecialchars($etudiant['NomPere']) ?></b> et de 
        <b><?= htmlspecialchars($etudiant['NomMere']) ?></b>, 
        matricule INE : <b><?= htmlspecialchars($etudiant['INE']) ?></b>,
        est régulièrement réinscrit(e) en <b><?= htmlspecialchars($etudiant['Niveau']) ?></b> 
        pour l’année universitaire <b><?= htmlspecialchars($etudiant['AnneeUniv']) ?></b>, 
        dans la <b><?= htmlspecialchars($etudiant['Faculte']) ?></b>, 
        département de <b><?= htmlspecialchars($etudiant['Departement']) ?></b>, 
        programme <b><?= htmlspecialchars($etudiant['Programme']) ?></b>.
    </p>

    <p class="contenu">
        En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.
    </p>

<!-- Signature + Cachet -->
<table style="width: 100%; margin-top: 50px; border-collapse: collapse;">
  <tr>
    <td style="text-align: center; width: 60%; vertical-align: top;">
      Labé, le <?= date("d/m/Y") ?><br>
      <b>Le Chef de Service de la Scolarité</b><br>
      <table style="margin: 0 auto; border-collapse: collapse;">
        <tr>
          <!-- Cachet -->
          <td style="text-align: right; padding-right: 15px; vertical-align: middle;">
           
          </td>
          <!-- Signature -->
          <td style="text-align: left; vertical-align: middle;">
            <br>
            <b>Issa SOUMARE</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
