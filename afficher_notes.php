<?php
session_start();

if (!isset($_GET['fichier'])) {
    die("⚠️ Fichier non spécifié.");
}

$fichierRelatif = $_GET['fichier'];
$fichier = __DIR__ . '/' . $fichierRelatif;

if (!file_exists($fichier)) {
    die("⚠️ Fichier introuvable.");
}

// Lire le fichier CSV
$rows = [];
if (($handle = fopen($fichier, 'r')) !== false) {
    while (($data = fgetcsv($handle, 0, ";")) !== false) {
        $rows[] = $data;
    }
    fclose($handle);
} else {
    die("⚠️ Impossible d'ouvrir le fichier.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Visualiser le fichier CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            padding: 2rem;
            background: #f4f8fb;
            font-family: 'Segoe UI', sans-serif;
        }
        .table-responsive {
            max-height: 500px; /* hauteur max avec scroll */
            overflow-y: auto;
        }
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: #0d6efd; /* bleu bootstrap */
            color: white;
            cursor: pointer; /* montrer que c'est cliquable */
        }
        .table thead th.sort-asc::after {
            content: " ▲";
        }
        .table thead th.sort-desc::after {
            content: " ▼";
        }
    </style>
</head>
<body>
<div class="container">
    <h4 class="mb-4">📄 Contenu du fichier : <code><?= htmlspecialchars(basename($fichier)) ?></code></h4>

    <?php if (count($rows) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="csvTable">
                <thead>
                    <tr>
                        <?php foreach ($rows[0] as $cell): ?>
                            <th><?= htmlspecialchars($cell) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i < count($rows); $i++): ?>
                        <tr>
                            <?php foreach ($rows[$i] as $cell): ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">⚠️ Le fichier est vide.</div>
    <?php endif; ?>

    <a href="javascript:history.back()" class="btn btn-secondary mt-3">↩ Retour</a>
</div>

<script>
// Fonction de tri
document.addEventListener("DOMContentLoaded", function() {
    const table = document.getElementById("csvTable");
    if (!table) return;

    const headers = table.querySelectorAll("th");
    headers.forEach((header, index) => {
        header.addEventListener("click", () => {
            sortTable(table, index);
        });
    });

    function sortTable(table, columnIndex) {
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.querySelectorAll("tr"));
        const currentHeader = table.querySelectorAll("th")[columnIndex];

        // Déterminer l'ordre
        const ascending = !currentHeader.classList.contains("sort-asc");
        table.querySelectorAll("th").forEach(th => th.classList.remove("sort-asc", "sort-desc"));

        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.trim();
            const cellB = b.children[columnIndex].innerText.trim();

            const numA = parseFloat(cellA.replace(",", "."));
            const numB = parseFloat(cellB.replace(",", "."));

            if (!isNaN(numA) && !isNaN(numB)) {
                return ascending ? numA - numB : numB - numA;
            } else {
                return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            }
        });

        // Réinjection des lignes triées
        rows.forEach(row => tbody.appendChild(row));
        currentHeader.classList.add(ascending ? "sort-asc" : "sort-desc");
    }
});
</script>

</body>
</html>
