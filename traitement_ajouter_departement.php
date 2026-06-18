<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeFac = $_POST['code_faculte'] ?? '';
    $codeDep = strtoupper(trim($_POST['code_departement'] ?? ''));
    $nomDep  = trim($_POST['nom_departement'] ?? '');
    $licences = $_POST['licences'] ?? ['Licence 1','Licence 2','Licence 3']; // par défaut

    if ($codeFac && $codeDep && $nomDep) {
        try {
            $pdo->beginTransaction();

            // Vérifier si le département existe
            $check = $pdo->prepare("SELECT COUNT(*) FROM departements WHERE CodeDep = ?");
            $check->execute([$codeDep]);
            if ($check->fetchColumn() > 0) {
                throw new Exception("❌ Le code du département existe déjà.");
            }

            // Insérer le département
            $stmt = $pdo->prepare("INSERT INTO departements (CodeDep, NomDep, CodeFac) VALUES (?, ?, ?)");
            $stmt->execute([$codeDep, $nomDep, $codeFac]);

            $annee = date('Y') . '-' . (date('Y') + 1);
            $pdo->prepare("INSERT IGNORE INTO annees_univ (annee) VALUES (?)")->execute([$annee]);

            // Créer licences, niveaux et semestres
            foreach ($licences as $index => $licence) {
                if (empty($licence)) continue;

                // Créer licence
                $stmt = $pdo->prepare("INSERT INTO licences (NomLic, CodeDep) VALUES (?, ?)");
                $stmt->execute([$licence, $codeDep]);
                $codeLic = $pdo->lastInsertId();

                // Créer niveau
                $niveauLabel = $licence;
                $codeNiv = $codeDep . "_L" . ($index + 1);
                $stmt = $pdo->prepare("INSERT INTO niveaux (CodeNiv, Niveau, AnneeUniv) VALUES (?, ?, ?)");
                $stmt->execute([$codeNiv, $niveauLabel, $annee]);

                // Déterminer semestres
                $sem_start = ($index) * 2 + 1;
                $sem_end = $sem_start + 1;

                for ($sem = $sem_start; $sem <= $sem_end; $sem++) {
                    $codeSem = $codeNiv . "_S" . $sem;
                    $libelleSem = "Semestre " . $sem;
                    $stmt = $pdo->prepare("INSERT INTO semestres (CodeSemes, CodeNiv, NivauSemes, CodeLic) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$codeSem, $codeNiv, $libelleSem, $codeLic]);
                }
            }

            $pdo->commit();
            header("Location: liste_facultes.php");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur : " . $e->getMessage());
        }

    } else {
        die("Tous les champs obligatoires doivent être remplis !");
    }
} else {
    die("Requête invalide.");
}
?>
