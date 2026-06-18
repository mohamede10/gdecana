<?php
require 'config/db.php';

if (isset($_POST['faculte'])) {
    $codeFac = $_POST['faculte'];

    $stmt = $pdo->prepare("SELECT CodeDep, NomDep FROM departements WHERE CodeFac = ?");
    $stmt->execute([$codeFac]);
    $departements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($departements) > 0) {
        echo '<option value="">-- Sélectionnez --</option>';
        foreach ($departements as $dep) {
            echo '<option value="' . htmlspecialchars($dep['CodeDep']) . '">' . htmlspecialchars($dep['NomDep']) . '</option>';
        }
    } else {
        echo '<option value="">Aucun département</option>';
    }
}
