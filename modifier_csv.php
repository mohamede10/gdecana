<?php
session_start();

if (!isset($_GET['fichier'])) {
    die("⚠️ Fichier non spécifié.");
}

$fichierRelatif = $_GET['fichier'];

// Vérifier sécurité : doit commencer par notes_importees/
if (strpos($fichierRelatif, 'notes_importees/') !== 0) {
    die("❌ Accès interdit.");
}

// Vérifier extension
if (pathinfo($fichierRelatif, PATHINFO_EXTENSION) !== 'csv') {
    die("❌ Seuls les fichiers CSV sont autorisés.");
}

$fichier = __DIR__ . '/' . $fichierRelatif;

if (!file_exists($fichier)) {
    die("⚠️ Fichier introuvable.");
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    $nouveauContenu = $_POST['data'];
    $handle = fopen($fichier, 'w');

    if ($handle) {
        foreach ($nouveauContenu as $row) {
            fputcsv($handle, $row, ';');
        }
        fclose($handle);
        $message = "✅ Modifications enregistrées avec succès.";
    } else {
        $message = "❌ Impossible de sauvegarder les modifications.";
    }
}

// Lecture pour affichage
$csv = fopen($fichier, 'r');
if (!$csv) die("⚠️ Impossible d'ouvrir le fichier.");

$rows = [];
while (($data = fgetcsv($csv, 0, ";")) !== false) {
    $rows[] = $data;
}
fclose($csv);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le fichier CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f9ff; }
        table { font-size: 13px; }
        input[type="text"] {
            border: none;
            background: transparent;
            width: 100%;
        }
        input[type="text"]:focus {
            outline: 1px solid #0d6efd;
            background: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <h4 class="mb-4">✏️ Édition : <code><?= htmlspecialchars(basename($fichier)) ?></code></h4>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-primary">
                <tr>
                    <?php foreach ($rows[0] as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach (array_slice($rows, 1) as $i => $row): ?>
                    <tr>
                        <?php foreach ($row as $j => $cell): ?>
                            <td><input type="text" name="data[<?= $i + 1 ?>][<?= $j ?>]" value="<?= htmlspecialchars($cell) ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php foreach ($rows[0] as $j => $col): ?>
            <input type="hidden" name="data[0][<?= $j ?>]" value="<?= htmlspecialchars($col) ?>">
        <?php endforeach; ?>

        <button type="submit" class="btn btn-success">💾 Enregistrer les modifications</button>
        <a href="liste_fichiers_importes.php" class="btn btn-secondary">↩ Retour</a>
    </form>
</div>

</body>
</html>
