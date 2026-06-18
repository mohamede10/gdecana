<?php
// Variables attendues : $etudiant, $notesS1, $notesS2
$notesS1 = $notesS1 ?? [];
$notesS2 = $notesS2 ?? [];

// --- Helpers (mêmes seuils que l’exemple photo) --- //
function gradeFrom10(float $n): array {
    if ($n >= 8.0) return ['lettre' => 'A', 'mention' => 'Très Bien'];
    if ($n >= 7.0) return ['lettre' => 'B', 'mention' => 'Bien'];
    if ($n >= 6.0) return ['lettre' => 'C', 'mention' => 'Assez Bien'];
    if ($n >= 5.0) return ['lettre' => 'D', 'mention' => 'Passable'];
    return ['lettre' => 'E', 'mention' => 'Échec'];
}

// Calcul de la moyenne d’un ensemble de notes
function moyenneSemestre(array $rows): float {
    $total = 0;
    $count = 0;
    foreach ($rows as $r) {
        $note = (float)$r['note'];
        $total += $note;
        $count++;
    }
    return $count ? round($total / $count, 2) : 0;
}

// Détermination de la mention selon la moyenne sur 10
function mentionFinale(float $moy): string {
    if ($moy >= 8.0) return "Très Bien";
    if ($moy >= 7.0) return "Bien";
    if ($moy >= 6.0) return "Assez Bien";
    if ($moy >= 5.0) return "Passable";
    return "Échec";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>RELEVÉ DE NOTES</title>
<style>
    body{font-family:DejaVu Sans, sans-serif; font-size:10pt;}
    table{width:100%; border-collapse:collapse; margin-top:6px;}
    th,td{border:1px solid #000; padding:4px;}
    th{background:#eee;}
    .center{text-align:center;}
    .small{font-size:9pt;}
    .no-border, .no-border th, .no-border td{border:none !important;}
</style>
</head>
<body>

<!-- En-tête -->
<table class="no-border" style="width:100%;">
  <tr>
    <td style="text-align:left; font-size:9pt; width:30%;">
      <b>République de Guinée</b><br>Travail · Justice · Solidarité
    </td>
    <td style="text-align:center; width:30%;">
      <img src="assets/Logo_univ_labe.png" alt="Logo Université de Labé" style="max-height:70px;">
    </td>
    <td style="text-align:center; font-size:9pt; width:30%; line-height:1.4;">
      Service Scolarité<br>+224 629005813 / 629005814<br>
      scolarite@univ-labe.edu.gn<br>www.univ-labe.edu.gn
    </td>
  </tr>
</table>
<hr>

<!-- Titre -->
<h2 style="text-align:center;">RELEVÉ DE NOTES <?= htmlspecialchars(strtoupper($etudiant['Licence'] ?? '')) ?></h2>
<div style="text-align:center;">
  Cohorte <?= htmlspecialchars($etudiant['Cohorte'] ?? '') ?><br>
  Année Universitaire <?= htmlspecialchars($etudiant['AnneeUniv'] ?? '') ?>
</div>

<!-- Infos étudiant -->
<p>
<b>Nom :</b> <?= htmlspecialchars($etudiant['Nom']) ?><br>
<b>Prénoms :</b> <?= htmlspecialchars($etudiant['Prenom']) ?><br>
<b>INE :</b> <?= htmlspecialchars($etudiant['INE']) ?><br>
<b>Matricule :</b> <?= htmlspecialchars($etudiant['MatEtu']) ?><br>
<b>Faculté :</b> <?= htmlspecialchars($etudiant['Faculte']) ?><br>
<b>Programme :</b> <?= htmlspecialchars($etudiant['Programme']) ?>
</p>

<?php
$moy1 = moyenneSemestre($notesS1);
$moy2 = moyenneSemestre($notesS2);
$moyenneGenerale = round(($moy1 + $moy2) / 2, 2);
$mentionGlobale = mentionFinale($moyenneGenerale);
?>

<!-- Semestre 1 -->
<h4>Semestre 1</h4>
<table>
<thead>
  <tr>
    <th>Code</th><th>Titre</th><th>Statut</th><th>Crédits</th>
    <th>Note/10</th><th>Note littérale</th><th>Mention</th>
  </tr>
</thead>
<tbody>
<?php foreach($notesS1 as $r):
      $g = gradeFrom10((float)$r['note']); ?>
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
<p class="small"><b>Moyenne Semestre 1 :</b> <?= number_format($moy1,2,',',' ') ?></p>

<!-- Semestre 2 -->
<h4>Semestre 2</h4>
<table>
<thead>
  <tr>
    <th>Code</th><th>Titre</th><th>Statut</th><th>Crédits</th>
    <th>Note/10</th><th>Note littérale</th><th>Mention</th>
  </tr>
</thead>
<tbody>
<?php foreach($notesS2 as $r):
      $g = gradeFrom10((float)$r['note']); ?>
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
<p class="small"><b>Moyenne Semestre 2 :</b> <?= number_format($moy2,2,',',' ') ?></p>

<!-- Moyenne finale -->
<h3 style="text-align:center; margin-top:15px;">RÉCAPITULATIF FINAL</h3>
<table style="width:60%; margin:0 auto; border-collapse:collapse; border:1px solid #000;">
  <tr style="background:#eee;">
    <th>Moyenne Générale /10</th>
    <th>Mention Finale</th>
  </tr>
  <tr>
    <td class="center"><?= number_format($moyenneGenerale, 2, ',', ' ') ?></td>
    <td class="center"><?= htmlspecialchars($mentionGlobale) ?></td>
  </tr>
</table>

<!-- Signature -->
<p style="text-align:right; margin-top:30px;">
Labé, le <?= date("d/m/Y") ?><br>
<b>Le Chef de Service de la Scolarité</b><br>
<b>Drissa CONDÉ</b>
</p>

</body>
</html>
