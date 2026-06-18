<?php
require 'config/db.php';

$prog = $_POST['programme'] ?? '';

if (!empty($prog)) {
    $query = "
        SELECT DISTINCT n.CodeNiv, n.Niveau
        FROM niveaux n
        JOIN semestres s ON s.CodeNiv = n.CodeNiv
        JOIN matieres m ON m.CodeSemes = s.CodeSemes
        JOIN programmes p ON p.CodeProg = n.CodeProg
        WHERE p.CodeProg = ?
        ORDER BY n.Niveau ASC
        LIMIT 3
    ";

    $niv = $pdo->prepare($query);
    $niv->execute([$prog]);

    echo '<option value="">-- Choisir --</option>';
    foreach ($niv as $n) {
        // Transformation lisible du niveau
        $niveau = $n['Niveau'];
        if (preg_match('/1$/', $niveau)) $niveau = "Licence 1";
        elseif (preg_match('/2$/', $niveau)) $niveau = "Licence 2";
        elseif (preg_match('/3$/', $niveau)) $niveau = "Licence 3";

        echo '<option value="'.htmlspecialchars($n['CodeNiv']).'">'.htmlspecialchars($niveau).'</option>';
    }
} else {
    echo '<option value="">-- Choisir --</option>';
}
