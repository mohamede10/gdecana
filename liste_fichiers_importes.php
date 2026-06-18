<?php
require_once "config/db.php";

$stmt = $pdo->query("SELECT * FROM imports_notes ORDER BY faculte, departement, licence, annee_univ, semestre");
$imports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hierarchie = [];
foreach ($imports as $imp) {
    $fac = $imp['faculte'];
    $dep = $imp['departement'];
    $lic = $imp['licence'];
    $annee = $imp['annee_univ'];
    $sem = $imp['semestre'];

    $hierarchie[$fac][$dep][$lic][$annee][$sem][] = $imp;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Fichiers de notes importés</title>
  <!-- ✅ Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
   
   
  .nav-pills {
      flex-wrap: nowrap;
      overflow-x: auto;
      white-space: nowrap;
  }
  .nav-pills .nav-item {
      flex: 0 0 auto;
      margin-right: 0.5rem;
  }
  /* ✅ Barre des facultés toujours visible en haut */
  #facultesTabs {
      position: sticky;
      top: 0;
      z-index: 1020; /* au-dessus du contenu */
      background: #f8f9fa; /* couleur de fond (clair) */
      padding: 0.5rem 0;
  }


  </style>
</head>
<body class="bg-light">
<div class="container-fluid mt-4">
  <h3 class="mb-4 text-center text-white bg-primary p-3 rounded shadow">📂 Fichiers de notes importés</h3>

  <!-- Onglets Facultés -->
  <ul class="nav nav-pills mb-3" role="tablist" id="facultesTabs">
    <?php $i=0; foreach ($hierarchie as $faculte => $deps): ?>
      <li class="nav-item">
        <button class="nav-link <?= $i===0?'active':'' ?>" 
                data-bs-toggle="tab" 
                data-bs-target="#fac<?= $i ?>" 
                type="button" role="tab">
          🏛 <?= htmlspecialchars($faculte) ?>
        </button>
      </li>
    <?php $i++; endforeach; ?>
  </ul>

  <!-- Contenu -->
  <div class="tab-content">
    <?php $i=0; foreach ($hierarchie as $faculte => $deps): ?>
      <div class="tab-pane fade <?= $i===0?'show active':'' ?>" id="fac<?= $i ?>" role="tabpanel">
        <?php foreach ($deps as $dep => $lics): ?>
          <div class="card mb-4 shadow border-0">
            <div class="card-header bg-dark text-white fw-bold">
              📂 Département : <?= htmlspecialchars($dep) ?>
            </div>
            <div class="card-body">
              <?php foreach ($lics as $licence => $annees): ?>
                <h6 class="mt-2">🎓 Licence : 
                  <span class="badge bg-info text-dark"><?= htmlspecialchars($licence) ?></span>
                </h6>
                <?php foreach ($annees as $annee => $sems): ?>
                  <p class="mt-2">📅 Année : 
                    <span class="badge bg-secondary"><?= htmlspecialchars($annee) ?></span>
                  </p>
                  
                  <?php foreach ($sems as $semestre => $fichiers): ?>
                    <h6 class="mt-3">📝 Semestre : 
                      <span class="badge bg-warning text-dark"><?= htmlspecialchars($semestre) ?></span>
                      <span class="badge bg-light text-dark"><?= count($fichiers) ?> fichier(s)</span>
                    </h6>
                    
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover align-middle shadow-sm">
                        <thead class="table-primary text-center">
                          <tr>
                            <th>📄 Fichier</th>
                            <th>📦 Taille</th>
                            <th>📅 Date import</th>
                            <th>⚙️ Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($fichiers as $f): ?>
                            <tr>
                              <td><?= htmlspecialchars($f['nom_fichier']) ?></td>
                              <td class="text-center"><?= round($f['taille']/1024,1) ?> Ko</td>
                              <td class="text-center"><?= date("d/m/Y H:i", strtotime($f['date_import'])) ?></td>
                              <td class="text-center">
                                <a href="voir_notes.php?id=<?= $f['id'] ?>" target="_blank" 
                                   class="btn btn-sm btn-primary">👁️ Voir</a>
                                <a href="uploads/notes/<?= urlencode($f['nom_fichier']) ?>" download 
                                   class="btn btn-sm btn-success">⬇️ Télécharger</a>
                                <a href="supprimer_import.php?id=<?= $f['id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Voulez-vous vraiment supprimer ce fichier ?');">
                                   🗑️ Supprimer
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php $i++; endforeach; ?>
  </div>
</div>

<!-- ✅ Script pour activer la première faculté automatiquement -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let firstTab = document.querySelector('#facultesTabs .nav-link');
    if (firstTab) {
        let tab = new bootstrap.Tab(firstTab);
        tab.show();
    }
});
</script>

</body>
</html>
