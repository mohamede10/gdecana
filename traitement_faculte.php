<?php
session_start();
require 'config/db.php'; // doit créer $pdo

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codeFac = strtoupper(trim($_POST['code_faculte']));
    $nomFac = trim($_POST['nom_faculte']);
    $codesDep = $_POST['code_departement'];
    $nomsDep = $_POST['nom_departement'];

    try {
        $pdo->beginTransaction();

        // Vérifier si la faculté existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM facultes WHERE CodeFac = ? OR NomFac = ?");
        $stmt->execute([$codeFac, $nomFac]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("⚠️ La faculté existe déjà.");
        }

        // Insérer faculté
        $stmt = $pdo->prepare("INSERT INTO facultes (CodeFac, NomFac) VALUES (?, ?)");
        $stmt->execute([$codeFac, $nomFac]);

        // Parcourir les départements
        $departements_uniques = [];
        foreach ($codesDep as $i => $codeDep) {
            $codeDep = strtoupper(trim($codeDep));
            $nomDep = trim($nomsDep[$i]);
            if ($codeDep === '' || $nomDep === '') continue;

            $cleUnique = strtolower($codeDep . '-' . $nomDep);
            if (isset($departements_uniques[$cleUnique])) continue;
            $departements_uniques[$cleUnique] = true;

            // Vérifier si le département existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM departements WHERE CodeDep = ? OR NomDep = ?");
            $stmt->execute([$codeDep, $nomDep]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("⚠️ Le département '$nomDep' existe déjà.");
            }

            // Insérer département
            $stmt = $pdo->prepare("INSERT INTO departements (CodeDep, CodeFac, NomDep) VALUES (?, ?, ?)");
            $stmt->execute([$codeDep, $codeFac, $nomDep]);

            // Créer les 3 licences
            for ($j = 1; $j <= 3; $j++) {
                $nomLic = "Licence " . $j;

                // Vérifier si la licence existe
                $stmt = $pdo->prepare("SELECT CodeLic FROM licences WHERE NomLic = ? AND CodeDep = ?");
                $stmt->execute([$nomLic, $codeDep]);
                $codeLic = $stmt->fetchColumn();

                if (!$codeLic) {
                    $stmt = $pdo->prepare("INSERT INTO licences (NomLic, CodeDep) VALUES (?, ?)");
                    $stmt->execute([$nomLic, $codeDep]);
                    $codeLic = $pdo->lastInsertId();
                }

                // Créer un niveau pour la licence
                $codeNiv = $codeDep . "_L" . $j;
                $niveauLabel = "Licence " . $j;
                $annee = date('Y') . '-' . (date('Y') + 1);

                // Ajouter année universitaire si absente
                $pdo->prepare("INSERT IGNORE INTO annees_univ (annee) VALUES (?)")->execute([$annee]);

                // Vérifier si le niveau existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM niveaux WHERE CodeNiv = ?");
                $stmt->execute([$codeNiv]);
                if ($stmt->fetchColumn() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO niveaux (CodeNiv, Niveau, AnneeUniv) VALUES (?, ?, ?)");
                    $stmt->execute([$codeNiv, $niveauLabel, $annee]);
                }

                // Définir les semestres selon la licence
                switch ($j) {
                    case 1: $sem_list = [1, 2]; break;
                    case 2: $sem_list = [3, 4]; break;
                    case 3: $sem_list = [5, 6]; break;
                }

                foreach ($sem_list as $sem) {
                    $codeSem = $codeNiv . "_S" . $sem;
                    $libelleSem = "Semestre " . $sem;

                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM semestres WHERE CodeSemes = ?");
                    $stmt->execute([$codeSem]);
                    if ($stmt->fetchColumn() == 0) {
                        $stmt = $pdo->prepare("INSERT INTO semestres (CodeSemes, CodeNiv, NivauSemes, CodeLic) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$codeSem, $codeNiv, $libelleSem, $codeLic]);
                    }
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('✅ Faculté et ses départements/licences/semestres créés avec succès !'); window.location.href='dashboard_admin.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>
