<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Créer une faculté - SGA Université de Labé</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    background: linear-gradient(135deg, #dbeafe, #93c5fd);
    font-family: 'Segoe UI', sans-serif;
    padding: 30px;
  }
  .card {
    max-width: 900px;
    margin: auto;
    padding: 30px;
    border-radius: 1rem;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    background: white;
  }
  .departement-block {
    border: 1px solid #e0e0e0;
    padding: 15px;
    border-radius: 0.5rem;
    background: #f9fafb;
  }
</style>
</head>
<body>
<div class="card">
  <h4 class="text-center mb-4 text-primary">🎓 Créer une Faculté, ses Départements et Licences</h4>
  <form method="POST" action="traitement_faculte.php" class="row g-3">

    <div class="col-md-6">
      <label class="form-label">Code de la faculté *</label>
      <input type="text" name="code_faculte" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Nom de la faculté *</label>
      <input type="text" name="nom_faculte" class="form-control" required>
    </div>

    <h6 class="mt-3">Départements et Licences</h6>
    <div id="departements-container">
      <div class="departement-block mb-3">
        <div class="row g-2 mb-2">
          <div class="col-md-6">
            <input type="text" name="code_departement[]" class="form-control" placeholder="Code du département" required>
          </div>
          <div class="col-md-6">
            <input type="text" name="nom_departement[]" class="form-control" placeholder="Nom du département" required>
          </div>
        </div>
        <div class="licences mb-2">
          <input type="text" name="licences_0[]" class="form-control mb-1" value="Licence 1" required>
          <input type="text" name="licences_0[]" class="form-control mb-1" value="Licence 2" required>
          <input type="text" name="licences_0[]" class="form-control mb-1" value="Licence 3" required>
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-departement">🗑 Supprimer</button>
      </div>
    </div>

    <div class="text-end">
      <button type="button" id="add-departement" class="btn btn-outline-primary">➕ Ajouter un département</button>
    </div>

    <div class="col-12 text-center mt-3">
      <button type="submit" class="btn btn-success px-4">✅ Enregistrer la faculté et les départements</button>
    </div>
  </form>
</div>

<script>
let depIndex=1;
document.getElementById('add-departement').onclick=()=>{
  const container=document.getElementById('departements-container');
  const block=document.createElement('div');
  block.className='departement-block mb-3';
  block.innerHTML=`
    <div class="row g-2 mb-2">
      <div class="col-md-6">
        <input type="text" name="code_departement[]" class="form-control" placeholder="Code du département" required>
      </div>
      <div class="col-md-6">
        <input type="text" name="nom_departement[]" class="form-control" placeholder="Nom du département" required>
      </div>
    </div>
    <div class="licences mb-2">
      <input type="text" name="licences_${depIndex}[]" class="form-control mb-1" value="Licence 1" required>
      <input type="text" name="licences_${depIndex}[]" class="form-control mb-1" value="Licence 2" required>
      <input type="text" name="licences_${depIndex}[]" class="form-control mb-1" value="Licence 3" required>
    </div>
    <button type="button" class="btn btn-danger btn-sm remove-departement">🗑 Supprimer</button>`;
  container.appendChild(block);
  depIndex++;
};
document.addEventListener('click',e=>{
  if(e.target.classList.contains('remove-departement')){
    e.target.parentElement.remove();
  }
});
</script>
</body>
</html>
