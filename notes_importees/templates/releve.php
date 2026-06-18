<?php
// Variables attendues : $etudiant, $notesS1, $notesS2
// Si les notes ne sont pas encore injectées, éviter les notices :
$notesS1 = $notesS1 ?? [];
$notesS2 = $notesS2 ?? [];

// --- Helpers (mêmes seuils que l’exemple photo) ---
function gradeFrom10(float $n): array {
    if ($n >= 8.0) return ['lettre' => 'A', 'mention' => 'Très Bien', 'num' => 4.0];
    if ($n >= 7.0) return ['lettre' => 'B', 'mention' => 'Bien', 'num' => 3.0];
    if ($n >= 6.0) return ['lettre' => 'C', 'mention' => 'Assez Bien', 'num' => 2.0];
    if ($n >= 5.0) return ['lettre' => 'D', 'mention' => 'Passable', 'num' => 1.0];
    return ['lettre' => 'E', 'mention' => 'Échec', 'num' => 0.0];
}
function cumulAvg(array $rows): array {
    $sum = 0; $cred = 0;
    foreach ($rows as $r) {
        $g = gradeFrom10((float)$r['note']);
        $c = (int)($r['credits'] ?? $r['CrediMat'] ?? 0);
        $sum  += $g['num'] * $c;
        $cred += $c;
    }
    $avg = $cred ? round($sum / $cred, 2) : 0;
    return [$avg, $cred];
}
function mentionFromAvg(float $avg): string {
    if ($avg >= 3.5) return 'Très Bien';
    if ($avg >= 2.5) return 'Bien';
    if ($avg >= 1.5) return 'Assez Bien';
    if ($avg >= 0.5) return 'Passable';
    return 'Échec';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>RELEVE DE NOTES</title>
<style>
    body{font-family:DejaVu Sans, sans-serif; font-size:10pt;}
    .topbar{width:100%; border-bottom:1px solid #000; margin-bottom:8px}
    .topbar td{vertical-align:top; font-size:9pt; line-height:1.3}
    .num{font-size:9pt}
    h2{margin:6px 0 2px 0; text-align:center; font-size:14pt}
    .sub{ text-align:center; margin:2px 0 8px 0; }
    .bloc{margin:8px 0}
  /* Style global pour tout le document */
    table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td { border: 1px solid #000; padding: 4px; }
    th { background: #eee; }

    /* Exceptions : en-tête et signature ne doivent pas avoir de bordures */
    .no-border, .no-border th, .no-border td {
        border: none !important;
    }
    .center{text-align:center}
    .right{text-align:right}
    .small{font-size:9pt}
    .signature{margin-top:20px; width:100%}
    .stamp{max-height:70px}
    .sign{max-height:40px}
</style>
</head>
<body>


<!-- En-tête -->
<table class="no-border" style="width: 100%;">
  <tr>
    <td style="text-align: left; font-size: 9pt; width: 30%;">
      <b>République de Guinée</b><br>
      Travail · Justice · Solidarité
    </td>
    <td style="text-align: center; width: 30%;">
      <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 70px;">
    </td>
    <td style="text-align: center; font-size: 9pt; width: 30%; line-height: 1.4;">
      Service Scolarité <br>
      +224 629005813 / 629005814 <br>
      scolarite@univ-labe.edu.gn <br>
      www.univ-labe.edu.gn
    </td>
  </tr>
</table>
<hr>


<!-- Titre -->
<h2>RELEVE DE NOTES <?= htmlspecialchars(strtoupper($etudiant['Licence'] ?? '')) ?></h2>
<div class="sub">
  Cohorte <?= htmlspecialchars($etudiant['Cohorte'] ?? '') ?><br>
  Année Universitaire <?= htmlspecialchars($etudiant['AnneeUniv'] ?? '') ?>
</div>

<!-- Paragraphe d’intro -->
<div class="bloc">
  Je soussigné, <b>Drissa CONDÉ</b>, Chef de Service de la Scolarité, délivre le présent
  <b>RELEVE DE NOTES</b> à l’étudiant(e) inscrit(e) dans les registres de notre institution
  suivant les références ci-après :
</div>

<!-- Identité -->
<div class="bloc">
  <b>Nom :</b> <?= htmlspecialchars($etudiant['Nom']) ?><br>
  <b>Prénoms :</b> <?= htmlspecialchars($etudiant['Prenom']) ?><br>
  <b>INE :</b> <?= htmlspecialchars($etudiant['INE']) ?><br>
  <b>Matricule :</b> <?= htmlspecialchars($etudiant['MatEtu']) ?><br>
  <b>Faculté :</b> <?= htmlspecialchars($etudiant['Faculte']) ?><br>
  <b>Programme :</b> <?= htmlspecialchars($etudiant['Programme']) ?>
</div>

<?php
// Calculs
list($avg1, $cred1) = cumulAvg($notesS1);
list($avg2, $cred2) = cumulAvg($notesS2);
?>

<!-- Semestre 1 -->
<table>
  <thead>
    <tr>
      <th style="width:12%">Code</th>
      <th>Titre</th>
      <th style="width:14%">Statut</th>
      <th style="width:10%">Crédits</th>
      <th style="width:10%">Note/10</th>
      <th style="width:12%">Note littérale</th>
      <th style="width:14%">Mention</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($notesS1 as $r):
        $g = gradeFrom10((float)$r['note']);
  ?>
    <tr>
      <td class="center"><?= htmlspecialchars($r['code']) ?></td>
      <td><?= htmlspecialchars($r['titre']) ?></td>
      <td class="center"><?= htmlspecialchars($r['statut'] ?? 'Obligatoire') ?></td>
      <td class="center"><?= (int)$r['credits'] ?></td>
      <td class="center"><?= number_format((float)$r['note'], 2, ',', ' ') ?></td>
      <td class="center"><?= $g['lettre'] ?></td>
      <td class="center"><?= $g['mention'] ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<p class="small"><b>Moyenne Cumulative (S1) :</b> <?= number_format($avg1, 2, ',', ' ') ?> &nbsp;&nbsp;—&nbsp;&nbsp; <b>Crédits :</b> <?= (int)$cred1 ?> &nbsp;&nbsp;—&nbsp;&nbsp; <b>Mention :</b> <?= mentionFromAvg($avg1) ?></p>

<!-- Semestre 2 -->
<table>
  <thead>
    <tr>
      <th style="width:12%">Code</th>
      <th>Titre</th>
      <th style="width:14%">Statut</th>
      <th style="width:10%">Crédits</th>
      <th style="width:10%">Note/10</th>
      <th style="width:12%">Note littérale</th>
      <th style="width:14%">Mention</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($notesS2 as $r):
        $g = gradeFrom10((float)$r['note']);
  ?>
    <tr>
      <td class="center"><?= htmlspecialchars($r['code']) ?></td>
      <td><?= htmlspecialchars($r['titre']) ?></td>
      <td class="center"><?= htmlspecialchars($r['statut'] ?? 'Obligatoire') ?></td>
      <td class="center"><?= (int)$r['credits'] ?></td>
      <td class="center"><?= number_format((float)$r['note'], 2, ',', ' ') ?></td>
      <td class="center"><?= $g['lettre'] ?></td>
      <td class="center"><?= $g['mention'] ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<p class="small"><b>Moyenne Cumulative (S2) :</b> <?= number_format($avg2, 2, ',', ' ') ?> &nbsp;&nbsp;—&nbsp;&nbsp; <b>Crédits :</b> <?= (int)$cred2 ?> &nbsp;&nbsp;—&nbsp;&nbsp; <b>Mention :</b> <?= mentionFromAvg($avg2) ?></p>

<!-- NB + Tableau de notation -->
<p class="small">
  <b>NB :</b> Seules les lettres A, B, C, D et E entrent dans le calcul de la moyenne cumulative avec les valeurs numériques suivantes :
</p>
<style>
  /* Bordures uniquement pour le tableau des notations */
  .table-notations {
    width: 260px;
    border-collapse: collapse;
  }
 /* Forcer les bordures visibles pour le tableau des notations */
.table-notations,
.table-notations th,
.table-notations td {
  border: 1px solid black !important;
}

  .table-notations th {
    background-color: #f2f2f2;
  }

  /* Supprimer les bordures pour les tableaux de signature et cachet */
  .no-border, .no-border td, .no-border th {
    border: none !important;
  }
</style>

<!-- Bloc final : tableau des notations à gauche et signature/cachet à droite -->
<table class="no-border" style="width: 100%; margin-top: 40px; border-collapse: collapse;">
  <tr>
    <!-- Colonne gauche : tableau des notations -->
    <td style="width: 50%; vertical-align: top;">
      <table class="table-notations">
        <thead>
          <tr>
            <th>Notations<br>Littérales</th>
            <th>Notations<br>numériques</th>
            <th>Mention</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>A</td><td>4,00</td><td>Très Bien</td></tr>
          <tr><td>B</td><td>3,00</td><td>Bien</td></tr>
          <tr><td>C</td><td>2,00</td><td>Assez Bien</td></tr>
          <tr><td>D</td><td>1,00</td><td>Passable</td></tr>
          <tr><td>E</td><td>0,00</td><td>Échec</td></tr>
        </tbody>
      </table>
    </td>

    <!-- Colonne droite : signature + cachet -->
    <td class="no-border" style="width: 50%; text-align: center; vertical-align: top;">
      Labé, le <?= date("d/m/Y") ?><br>
      <b>Le Chef de Service de la Scolarité</b><br>
      <table class="no-border" style="margin: 10px auto; border-collapse: collapse;">
        <tr>
          <td class="no-border" style="text-align: right; padding-right: 15px; vertical-align: middle;">
            <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 50px;">
          </td>
          <td class="no-border" style="text-align: left; vertical-align: middle;">
            <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height: 40px;"><br>
            <b>Issa SOUMARE</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</body>
</html>
