<?php 
require 'config/db.php';
$message = "";

// --- Traitement du formulaire ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codeMat   = $_POST["CodeMat"] ?? null;
    $codeSemes = $_POST["CodeSemes"] ?? null;
    $nomMat    = $_POST["NomMat"] ?? null;
    $prof      = $_POST["Professeur"] ?? null;

    if ($codeMat && $codeSemes && $nomMat && $prof) {
        try {
            // ⚠️ Assure-toi que la colonne 'Professeur' existe dans la table 'matieres'
            $sql = "INSERT INTO matieres (CodeMat, CodeSemes, NomMat, Professeur) 
                    VALUES (:CodeMat, :CodeSemes, :NomMat, :Professeur)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":CodeMat"    => $codeMat,
                ":CodeSemes"  => $codeSemes,
                ":NomMat"     => $nomMat,
                ":Professeur" => $prof,
            ]);
            $message = "<div class='alert alert-success'>✅ Matière ajoutée avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'> Tous les champs sont obligatoires.</div>";
    }
}

// --- Récupérer la liste des départements ---
$stmtDep = $pdo->query("SELECT CodeDep, NomDep FROM departements ORDER BY NomDep");
$departements = $stmtDep->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une matière</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container py-5">

    <h2 class="mb-4">➕ Ajouter une matière</h2>
    <?= $message ?>

    <form method="post" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="CodeDep" class="form-label">Département</label>
            <select class="form-select" id="CodeDep" name="CodeDep" required>
                <option value="">-- Sélectionnez un département --</option>
                <?php foreach ($departements as $dep): ?>
                    <option value="<?= $dep['CodeDep'] ?>"><?= htmlspecialchars($dep['NomDep']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="CodeLic" class="form-label">Licence</label>
            <select class="form-select" id="CodeLic" name="CodeLic" required>
                <option value="">-- Sélectionnez une licence --</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="CodeSemes" class="form-label">Semestre</label>
            <select class="form-select" id="CodeSemes" name="CodeSemes" required>
                <option value="">-- Sélectionnez un semestre --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="CodeMat" class="form-label">Code Matière</label>
            <input type="text" class="form-control" id="CodeMat" name="CodeMat" required>
        </div>

        <div class="mb-3">
            <label for="NomMat" class="form-label">Nom de la matière</label>
            <input type="text" class="form-control" id="NomMat" name="NomMat" required>
        </div>

        <div class="mb-3">
            <label for="Professeur" class="form-label">Professeur</label>
            <input type="text" class="form-control" id="Professeur" name="Professeur" required>
        </div>
        <div class="mb-3">
           <button type="submit" class="btn btn-primary">Ajouter</button>
           <a href="dashboard_admin.php" class="btn btn-danger">Fermer</a>
        </div>
    </form>

    <script>
    // Charger les licences selon le département
    $("#CodeDep").on("change", function() {
        const dep = $(this).val();
        $("#CodeLic").html('<option value="">Chargement...</option>');
        $("#CodeSemes").html('<option value="">-- Sélectionnez un semestre --</option>');
        if (dep) {
            // 🔑 Ton script AJAX attend la clé 'departement'
            $.post("ajax_licences.php", { departement: dep })
             .done(function(data) { $("#CodeLic").html(data); })
             .fail(function(){ $("#CodeLic").html('<option value="">Erreur de chargement</option>'); });
        } else {
            $("#CodeLic").html('<option value="">-- Sélectionnez une licence --</option>');
        }
    });

    // Charger les semestres selon la licence
    $("#CodeLic").on("change", function() {
        const lic = $(this).val();
        $("#CodeSemes").html('<option value="">Chargement...</option>');
        if (lic) {
            // 🔑 Ton script AJAX attend la clé 'programme'
            $.post("ajax_semestres.php", { programme: lic })
             .done(function(data) { $("#CodeSemes").html(data); })
             .fail(function(){ $("#CodeSemes").html('<option value="">Erreur de chargement</option>'); });
        } else {
            $("#CodeSemes").html('<option value="">-- Sélectionnez un semestre --</option>');
        }
    });
    </script>
</body>
</html>
