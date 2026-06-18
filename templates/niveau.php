<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Attestation de Niveau</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12pt;
      line-height: 1.6;
      margin: 50px;
        background: url('assets/.png') no-repeat center center;
    }
    h2 {
      text-align: center;
      text-transform: uppercase;
      font-size: 15pt;
      text-decoration: underline;
      margin: 20px 0;
    }
    .sub {
      text-align: center;
      font-size: 11pt;
      margin-bottom: 20px;
    }
    .attestation {
      text-align: justify;
      font-size: 11pt;
    }
    .numero {
      margin-top: 10px;
      font-size: 10pt;
      font-weight: bold;
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
<div class="numero">
  N° <?= rand(1000,9999) ?>/UL/SS/<?= date("Y") ?>
</div>

<!-- Titre -->
<h2>
ATTESTATION DE NIVEAU <?= htmlspecialchars($etudiant['Licence']) ?> <br>(semestres <?= htmlspecialchars($etudiant['Semestres']) ?>)
</h2>
<div class="sub">
Cohorte <?= htmlspecialchars($etudiant['Cohorte'] ?? '') ?><br>
  Année Universitaire <?= htmlspecialchars($etudiant['AnneeUniv'] ?? '') ?>
</div>

<!-- Corps -->
<div class="attestation">
  Je soussigné, <b>Issa SOUMARE</b>, Chef de Service de la Scolarité, 
  atteste que l’étudiant(e) <b><?= htmlspecialchars(strtoupper($etudiant['Prenom']." ".$etudiant['Nom'])) ?></b> 
  est inscrit(e) dans les registres de notre institution suivant l’Identifiant National Étudiant (INE) 
  <b><?= htmlspecialchars($etudiant['INE']) ?></b>.<br> Il (Elle) a fréquenté la Faculté des <b><?= htmlspecialchars($etudiant['Faculte']) ?></b>, 
  Département de <b><?= htmlspecialchars($etudiant['Departement']) ?></b>, 
  Programme de <b><?= htmlspecialchars($etudiant['Programme']) ?></b> et a suivi avec succès 
les cours de la <b><?= htmlspecialchars($etudiant['Licence']) ?> (semestres <?= htmlspecialchars($etudiant['Semestres']) ?>)</b>  au titre de l’année universitaire <?= date("Y") ?>/<?= date("Y")+1 ?>.<br>
  En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.
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