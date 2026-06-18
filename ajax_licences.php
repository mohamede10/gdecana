<?php
session_start();
require 'config/db.php';

if (!empty($_POST['departement'])) {
    $codeDep = trim($_POST['departement']);

    // Récupérer les licences/programmes du département
    $stmt = $pdo->prepare("SELECT CodeLic, NomLic FROM licences WHERE CodeDep = ? ORDER BY NomLic ASC");
    $stmt->execute([$codeDep]);
    $licences = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<option value="">-- Sélectionnez --</option>';
    if ($licences) {
        foreach ($licences as $lic) {
            echo '<option value="'.htmlspecialchars($lic['CodeLic']).'">'.htmlspecialchars($lic['NomLic']).'</option>';
        }
    } else {
        echo '<option value="">Aucune licence trouvée</option>';
    }
} else {
    echo '<option value="">-- Sélectionnez --</option>';
}
