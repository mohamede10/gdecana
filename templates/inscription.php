<?php
// Ce fichier est inclus depuis generer_attestation.php
// La variable $etudiant contient toutes les infos nécessaires
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
              background: url('assets/.png') no-repeat center center;
        }
        .entete {
            width: 100%;
            margin-bottom: 20px;
        }
        .entete td {
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.4;
        }
        .titre {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .sous-titre {
            text-align: center;
            margin-bottom: 20px;
        }
        .contenu {
            text-align: justify;
            margin-top: 15px;
        }
        .signature {
            width: 100%;
            margin-top: 40px;
        }
        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .cachet {
            max-width: 120px;
            margin-top: 10px;
        }
        .sign {
            max-width: 150px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- En-tête -->
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

<!-- Numéro -->
<p style="text-align: left; font-size: 10pt;">
    N° <?= rand(1000,9999) ?>/UL/SS/<?= date("Y") ?>
</p>

<!-- Titre -->
<h2 class="titre">ATTESTATION D'INSCRIPTION</h2>

<!-- Sous-titre -->
<p class="sous-titre">
    Cohorte <?= htmlspecialchars($etudiant['Cohorte']) ?><br>
    <?= htmlspecialchars($etudiant['Licence']) ?> / Semestres <?= htmlspecialchars($etudiant['Semestres']) ?><br>
    Année Universitaire <?= htmlspecialchars($etudiant['AnneeUniv']) ?>
</p>
<!-- Corps -->
<div class="contenu">
    Je soussigné, <b>Issa SOUMARE</b>, Chef de Service de la Scolarité atteste que l’étudiant(e):<br>
    Prénoms : <b><?= htmlspecialchars($etudiant['Prenom']) ?></b> &nbsp;&nbsp; 
    Nom : <b><?= htmlspecialchars($etudiant['Nom']) ?></b><br>
    Né(e) le :<?= date("d/m/Y", strtotime($etudiant['DateNaissance'])) ?> à <?= htmlspecialchars($etudiant['LieuNaissance']) ?>,
</b><br>
    Fils/Fille de : <b><?= htmlspecialchars($etudiant['NomPere']) ?></b> et de <b><?= htmlspecialchars($etudiant['NomMere']) ?></b><br>
    Est inscrit(e) à l’Université de Labé sous l’Identifiant National Étudiant (INE) : 
    <b><?= htmlspecialchars($etudiant['INE']) ?></b>.<br>
    À la Faculté des : <b><?= htmlspecialchars($etudiant['Faculte']) ?></b><br>
    Département : <b><?= htmlspecialchars($etudiant['Departement']) ?></b><br>
    Programme : <b><?= htmlspecialchars($etudiant['Programme']) ?></b><br> 
    <p>En foi de quoi, la présente attestation est délivrée pour servir et valoir ce que de droit.</p>
</div>

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
             <br> <b>Issa SOUMARE</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
