<?php
session_start();

// Connexion à la base de données

require 'config/db.php';

// Récupérer toutes les facultés pour la liste déroulante
$stmt = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac");
$facultes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter un Département</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body { background: linear-gradient(135deg, #dbeafe, #93c5fd); padding:30px; }
  .card { max-width:700px; margin:auto; padding:30px; border-radius:1rem; box-shadow:0 8px 30px rgba(0,0,0,0.1); background:white; }
</style>
</head>
<body>
<div class="card">
  <h4 class="text-center mb-4 text-primary">➕ Ajouter un Département à une Faculté existante</h4>

  <form method="POST" action="traitement_ajouter_departement.php" class="row g-3">
    <div class="col-12">
      <label class="form-label">Sélectionner la faculté *</label>
      <select name="code_faculte" class="form-select" required>
        <option value="">-- Choisir la faculté --</option>
        <?php foreach($facultes as $fac): ?>
          <option value="<?= htmlspecialchars($fac['CodeFac']) ?>">
            <?= htmlspecialchars($fac['NomFac']) ?> (<?= htmlspecialchars($fac['CodeFac']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Code du département *</label>
      <input type="text" name="code_departement" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Nom du département *</label>
      <input type="text" name="nom_departement" class="form-control" required>
    </div>
    
    <!-- Optionnel : ajouter ici des licences -->
   <h6 class="mt-3">Licences *</h6>
      <div class="col-12">
        <input type="text" name="licences[]" class="form-control mb-1" value="Licence 1" readonly>
        <input type="text" name="licences[]" class="form-control mb-1" value="Licence 2" readonly>
        <input type="text" name="licences[]" class="form-control mb-1" value="Licence 3" readonly>
      </div>


    <div class="col-12 text-center mt-3">
      <button type="submit" class="btn btn-success">✅ Ajouter le département</button>
    </div>
  </form>
</div>
</body>
</html> 