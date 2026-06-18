<?php
require 'config/db.php';

if (!empty($_POST['matiere'])) {
    $codeMat = $_POST['matiere'];
    $stmt = $pdo->prepare("SELECT Professeur FROM matieres WHERE CodeMat = ?");
    $stmt->execute([$codeMat]);
    $prof = $stmt->fetchColumn();

    if ($prof) {
        echo $prof;
    } else {
        echo "Non défini";
    }
}
?>
