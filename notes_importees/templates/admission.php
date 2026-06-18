<!DOCTYPE html> 
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Attestation d’Admission</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
  body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12pt;
      line-height: 1.6;
      background: url('assets/.png') no-repeat center center;
      background-size: 100%;
      margin: 50px;
  }

  .numero {
      margin-top: 15px;
      font-size: 10pt;
      font-weight: bold;
  }

  h2 {
      text-align: center;
      text-transform: uppercase;
      font-size: 15pt;
      text-decoration: underline;
      margin: 30px 0 20px 0;
  }

  .attestation {
      margin-top: 15px;
      text-align: justify;
      font-size: 11pt;
  }

  .signature {
      text-align: center;
      font-size: 11pt;
  }

  .cachet {
      text-align: left;
  }

/* Responsive : sur mobile, tout centré en colonne */
@media (max-width: 768px) {
  .bloc-gauche, .bloc-centre, .bloc-droite {
    text-align: center !important;
    margin-bottom: 10px;
  }
}

  /* Section entête */

    .bloc-gauche {
      text-align: left;
    }
    .bloc-centre {
      text-align: center;
    }
    .bloc-centre img {
      max-height: 70px;
    }
    .bloc-droite {
      text-align: right;
      font-size: 11pt;
      line-height: 1.5;
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


<!-- Numéro -->
<div class="numero">
  N° <?= rand(1000,9999) ?>/UL/SS/<?= date("Y") ?>
</div>

<!-- Titre -->
<h2>
ATTESTATION D’ADMISSION À LA SÉLECTION ET À L’ORIENTATION À L’ENSEIGNEMENT SUPÉRIEUR<br>
SESSION <?= date("Y") ?>
</h2>

<!-- Corps -->
<div class="attestation">
  Je soussigné, <b>Issa SOUMARE</b>, Chef de Service de la Scolarité de l’Université de Labé,
  atteste que <b><?= htmlspecialchars(strtoupper($etudiant['Prenom']." ".$etudiant['Nom'])) ?></b>, 
  né(e) le <?= date("d/m/Y", strtotime($etudiant['Naissance'])) ?> à <?= htmlspecialchars($etudiant['Lieu']) ?>,
  fils/fille de <b><?= htmlspecialchars($etudiant['NomPere']) ?></b> et de <b><?= htmlspecialchars($etudiant['NomMere']) ?></b>,
  est effectivement <b>SÉLECTIONNÉ(E)</b> et <b>ADMIS(E)</b> à l’enseignement supérieur,
  conformément aux renseignements ci-après :<br> <br>

  Session : <?= htmlspecialchars($etudiant['SessionBAC']) ?><br>
  INE : <b><?= htmlspecialchars($etudiant['INE']) ?></b><br>
  École d’origine : <?= htmlspecialchars($etudiant['Lycee']) ?><br>
  PV Baccalauréat : <?= htmlspecialchars($etudiant['PVBAC']) ?><br>
  Centre : <?= htmlspecialchars($etudiant['CentreExamen']) ?><br>
  Profil : <?= htmlspecialchars($etudiant['OptionBAC']) ?><br>
  Orienté à l’Université de Labé, à la Faculté des <b><?= htmlspecialchars($etudiant['Faculte']) ?></b><br>
  Programme : <b><?= htmlspecialchars($etudiant['Programme']) ?></b>
</div>

<!-- Signature + Cachet -->
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
            <img src="assets/Logo_univ_labe.png" alt="cachet" style="max-height: 60px;">
          </td>
          <!-- Signature -->
          <td style="text-align: left; vertical-align: middle;">
            <img src="assets/Logo_univ_labe.png" alt="signature" style="max-height: 40px;"><br>
            <b>Issa SOUMARE</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
