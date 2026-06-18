<?php
require 'config/db.php';

if (isset($_POST['programme'])) {
    $programme = $_POST['programme'];

    $stmt = $pdo->prepare("SELECT CodeSemes, NivauSemes 
                           FROM semestres 
                           WHERE CodeLic = ? 
                           ORDER BY CodeSemes ASC");
    $stmt->execute([$programme]);
    $semestres = $stmt->fetchAll();

    echo '<option value="">-- Sélectionnez --</option>';
    foreach ($semestres as $s) {
        echo '<option value="'.htmlspecialchars($s['CodeSemes']).'">'.htmlspecialchars($s['NivauSemes']).'</option>';
    }
}
