<?php
require 'config/db.php';

$dep      = $_POST['departement'] ?? '';
$licence  = $_POST['programme'] ?? '';
$semestre = $_POST['semestre'] ?? '';

if ($dep && $licence && $semestre) {
    // Récupérer les matières liées à la combinaison Dép + Licence + Semestre
    $stmt = $pdo->prepare("
        SELECT m.CodeMat, m.NomMat
        FROM matieres m
        INNER JOIN semestres s ON m.CodeSemes = s.CodeSemes
        INNER JOIN licences l ON s.CodeLic = l.CodeLic
        WHERE l.CodeDep = ? AND l.CodeLic = ? AND s.CodeSemes = ?
        ORDER BY m.NomMat
    ");
    $stmt->execute([$dep, $licence, $semestre]);
    $matieres = $stmt->fetchAll();

    if ($matieres) {
        echo '<option value="">-- Sélectionnez --</option>';
        foreach ($matieres as $m) {
            echo '<option value="'.htmlspecialchars($m['CodeMat']).'">'
               .htmlspecialchars($m['NomMat']).'</option>';
        }
    } else {
        echo '<option value="">(Aucune matière trouvée)</option>';
    }
}
?>
