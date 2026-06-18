<?php
require 'config/db.php';

// Récupération des départements
$departements = $pdo->query("
    SELECT d.CodeDep, d.NomDep, f.NomFac 
    FROM departements d
    LEFT JOIN facultes f ON d.CodeFac = f.CodeFac
    ORDER BY f.NomFac, d.NomDep
")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des licences
$licences = $pdo->query("
    SELECT CodeLic, NomLic, CodeDep 
    FROM licences 
    WHERE NomLic IS NOT NULL AND NomLic != ''
")->fetchAll(PDO::FETCH_ASSOC);

// Récupération des semestres
$semestres = $pdo->query("
    SELECT CodeSemes, NivauSemes, CodeLic 
    FROM semestres
")->fetchAll(PDO::FETCH_ASSOC);

// Comptage des étudiants par semestre
$etudiantsSem = $pdo->query("
    SELECT CodeSemes, COUNT(*) as total 
    FROM etudiants 
    GROUP BY CodeSemes
")->fetchAll(PDO::FETCH_KEY_PAIR); // tableau [CodeSemes => total]
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>📘 Départements, Licences & Semestres</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #eef2f7; font-family: 'Segoe UI', sans-serif; padding: 2rem; }
.list-group-item { cursor: pointer; transition: background 0.2s; }
.list-group-item:hover { background-color: #d9edf7; }
#depList { position: sticky; top: 1rem; max-height: 80vh; overflow-y: auto; }
.card-lic { background-color: #fff; border: 1px solid #ccc; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.semestre-line { display: flex; align-items: center; justify-content: space-between; }
.semestre-line .semestres { font-weight: bold; color: #28a745; }
</style>
</head>
<body>
<div class="container my-4">
    <h3 class="text-center mb-4 text-white bg-primary p-3 rounded">
        Liste des Départements, Licences & Semestres
    </h3>

    <div class="row">
        <!-- Colonne gauche : Départements -->
        <div class="col-md-4">
            <div class="list-group" id="depList">
                <?php foreach ($departements as $dep): ?>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#dep_<?= $dep['CodeDep'] ?>">
                        📂 <?= htmlspecialchars($dep['NomDep']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Colonne droite : Contenu -->
        <div class="col-md-8">
            <div class="tab-content" id="depContent">
                <?php foreach ($departements as $dep): ?>
                    <div class="tab-pane fade" id="dep_<?= $dep['CodeDep'] ?>">
                        <h4 class="text-primary">
                            📂 Département : <?= htmlspecialchars($dep['NomDep']) ?>
                            <a href="modifier_departement.php?code=<?= $dep['CodeDep'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="#" 
                               class="btn btn-sm btn-danger" 
                               data-bs-toggle="modal" 
                               data-bs-target="#confirmModal" 
                               data-href="supprimer_departement.php?code=<?= $dep['CodeDep'] ?>">
                               Supprimer
                            </a>
                        </h4>
                        <p><b>Faculté :</b> 🎓 <?= htmlspecialchars($dep['NomFac']) ?></p>

                        <?php
                        // Licences du département
                        $lics = array_filter($licences, fn($l) => $l['CodeDep'] === $dep['CodeDep']);
                        if (!$lics): ?>
                            <p class="text-muted ms-3">Aucune licence trouvée.</p>
                        <?php else: ?>
                            <?php foreach ($lics as $lic): ?>
                                <div class="card-lic ms-3">
                                    <h5>🎓 Licence : <?= htmlspecialchars($lic['NomLic']) ?></h5>
                                    <?php
                                    // Semestres de la licence
                                    $sems = array_filter($semestres, fn($s) => (int)$s['CodeLic'] === (int)$lic['CodeLic']);
                                    if (!$sems): ?>
                                        <p class="ms-3 text-muted">Aucun semestre.</p>
                                    <?php else: ?>
                                        <div class="ms-3 semestre-line">
                                            <div class="semestres">
                                                <?php 
                                                $semList = [];
                                                $totalEtud = 0;
                                                foreach ($sems as $sem) {
                                                    $semList[] = htmlspecialchars($sem['NivauSemes']);
                                                    $totalEtud += $etudiantsSem[$sem['CodeSemes']] ?? 0;
                                                }
                                                echo implode(' ', $semList);
                                                ?>
                                            </div>
                                            <div class="text-dark">
                                                Étudiants inscrits : <b><?= $totalEtud ?></b>
                                                <a href="liste_etudiants.php?licence=<?= $lic['CodeLic'] ?>" class="btn btn-sm btn-info">👥 Voir liste</a>
                                                <a href="notes_licence.php?licence=<?= $lic['CodeLic'] ?>" class="btn btn-sm btn-success">📋 Voir notes</a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">⚠️ Confirmation</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Voulez-vous vraiment <b>supprimer</b> ce département ?  
        Cette action est <span class="text-danger">irréversible</span>.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Annuler</button>
        <a href="#" id="btnConfirmDelete" class="btn btn-danger">🗑️ Supprimer</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Active le premier onglet automatiquement
var firstTab = document.querySelector('#depList a');
if(firstTab) {
    firstTab.classList.add('active');
    var firstPane = document.querySelector(firstTab.getAttribute('href'));
    if(firstPane) firstPane.classList.add('show', 'active');
}

// Injection du lien dans la modale
var confirmModal = document.getElementById('confirmModal');
confirmModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var href = button.getAttribute('data-href'); 
  var confirmBtn = document.getElementById('btnConfirmDelete');
  confirmBtn.setAttribute('href', href);
});
</script>
</body>
</html>
