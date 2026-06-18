<?php
session_start();
require 'config/db.php';

// Suppression si paramètre reçu
if (isset($_GET['code'])) {
    $codeFac = $_GET['code'];
    $stmt = $pdo->prepare("DELETE FROM facultes WHERE CodeFac = ?");
    if ($stmt->execute([$codeFac])) {
        header("Location: index.php?success=Faculté supprimée avec succès");
    } else {
        header("Location: index.php?error=Erreur lors de la suppression");
    }
    exit;
}

// Facultés
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes")->fetchAll(PDO::FETCH_KEY_PAIR);

// Départements
$departements = $pdo->query("SELECT CodeDep, NomDep, CodeFac FROM departements")->fetchAll(PDO::FETCH_ASSOC);

// Licences
$licences = $pdo->query("SELECT CodeLic, NomLic, CodeDep FROM licences WHERE NomLic IS NOT NULL AND NomLic != ''")->fetchAll(PDO::FETCH_ASSOC);

// Semestres
$semestres = $pdo->query("SELECT CodeSemes, NivauSemes, CodeLic FROM semestres")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>📘 Facultés & Structures</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #eef2f7;
    font-family: 'Segoe UI', sans-serif;
    padding: 2rem;
}
.list-group-item { cursor: pointer; transition: background 0.2s; }
.list-group-item:hover { background-color: #d9edf7; }
#facList { position: sticky; top: 1rem; max-height: 80vh; overflow-y: auto; }
.tab-pane h4, .tab-pane h5, .tab-pane h6 { margin-top: 1rem; }
.semestre-badge { background-color: #28a745; color: #fff; margin-right: 0.5rem; padding: 0.3rem 0.6rem; border-radius: 0.25rem; font-size: 0.85rem; }
.card-dep { background-color: #fff; border: 1px solid #ccc; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; }
.btn-sm { margin-left: 5px; }
</style>
</head>
<body>
<div class="container my-4">
    <h3 class="text-center mb-4 text-white bg-primary p-3 rounded">
        Liste des Facultés et Départements
    </h3>

    <div class="row">
        <!-- Colonne gauche : Facultés -->
        <div class="col-md-4">
            <div class="list-group" id="facList">
                <?php foreach ($facultes as $codeFac => $nomFac): ?>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#fac_<?= $codeFac ?>">
                        🎓 <?= htmlspecialchars($nomFac) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Colonne droite : Contenu -->
        <div class="col-md-8">
            <div class="tab-content" id="facContent">
                <?php foreach ($facultes as $codeFac => $nomFac): ?>
                    <div class="tab-pane fade" id="fac_<?= $codeFac ?>">
                        <h4 class="text-primary">
                            🎓 Faculté : <?= htmlspecialchars($nomFac) ?>
                            <a href="modifier_faculte.php?code=<?= $codeFac ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <a href="#" 
                               class="btn btn-sm btn-danger" 
                               data-bs-toggle="modal" 
                               data-bs-target="#confirmModal" 
                               data-href="supprimer_faculte.php?code=<?= $codeFac ?>">
                               Supprimer
                            </a>
                        </h4>

                        <?php
                        // Départements de la faculté
                        $deps = array_filter($departements, fn($d) => $d['CodeFac'] === $codeFac);
                        if (!$deps): ?>
                            <p class="text-muted">Aucun département enregistré.</p>
                        <?php else: ?>
                            <?php foreach ($deps as $dep): ?>
                                <div class="card-dep">
                                    <h5>
                                        📂 Département : <?= htmlspecialchars($dep['NomDep']) ?>
                                        <a href="modifier_departement.php?code=<?= $dep['CodeDep'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                        <a href="#" 
                                           class="btn btn-sm btn-danger" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#confirmModal" 
                                           data-href="supprimer_departement.php?code=<?= $dep['CodeDep'] ?>">
                                           Supprimer
                                        </a>
                                    </h5>

                                    <?php
                                    // Licences du département
                                    $lics = array_filter($licences, fn($l) => $l['CodeDep'] === $dep['CodeDep']);
                                    if (!$lics): ?>
                                        <p class="ms-3 text-muted">Aucune licence.</p>
                                    <?php else: ?>
                                        <?php foreach ($lics as $lic): ?>
                                            <?php if(empty($lic['NomLic'])) continue; ?>
                                            <h6 class="ms-3">🎓 Licence : <?= htmlspecialchars($lic['NomLic']) ?></h6>

                                            <?php
                                            // Semestres de la licence
                                            $sems = array_filter($semestres, fn($s) => (int)$s['CodeLic'] === (int)$lic['CodeLic']);
                                            if (!$sems): ?>
                                                <p class="ms-4 text-muted">Aucun semestre.</p>
                                            <?php else: ?>
                                                <div class="ms-4">
                                                    <?php foreach ($sems as $sem): ?>
                                                        <span class="semestre-badge"><?= htmlspecialchars($sem['NivauSemes']) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
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
        Voulez-vous vraiment <b>supprimer</b> cet élément ?  
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
var firstTab = document.querySelector('#facList a');
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
