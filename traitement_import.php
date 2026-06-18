<?php 
session_start();
require 'config/db.php';
require 'vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ======================
    // Variables du formulaire
    // ======================
    $faculte     = $_POST['faculte']    ?? '';
    $departement = $_POST['departement']?? '';
    $licence     = $_POST['programme']  ?? ''; // programme = licence
    $semestre    = $_POST['semestre']   ?? '';
    $matiere     = $_POST['matiere']    ?? ''; // matière sélectionnée
    $annee_univ  = $_POST['annee_univ'] ?? '';
    $cohorte     = $_POST['cohorte']    ?? '';

    // Vérifier fichier uploadé
    if (!isset($_FILES['fichier_excel']) || $_FILES['fichier_excel']['error'] != 0) {
        die("⚠️ Fichier Excel non chargé correctement !");
    }

    $fileTmp        = $_FILES['fichier_excel']['tmp_name'];
    $nomFichier     = $_FILES['fichier_excel']['name'];
    $typeFichier    = $_FILES['fichier_excel']['type'];
    $tailleFichier  = $_FILES['fichier_excel']['size'];
    $contenuFichier = file_get_contents($fileTmp);

    try {
        $spreadsheet = IOFactory::load($fileTmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $pdo->beginTransaction();

        // ======================
        // Récupérer les noms lisibles
        // ======================
        $stmt = $pdo->prepare("SELECT NomFac FROM facultes WHERE CodeFac = ?");
        $stmt->execute([$faculte]);
        $nomFaculte = $stmt->fetchColumn() ?: $faculte;

        $stmt = $pdo->prepare("SELECT NomDep FROM departements WHERE CodeDep = ?");
        $stmt->execute([$departement]);
        $nomDepartement = $stmt->fetchColumn() ?: $departement;

        $stmt = $pdo->prepare("SELECT NomLic FROM licences WHERE CodeLic = ?");
        $stmt->execute([$licence]);
        $nomLicence = $stmt->fetchColumn() ?: $licence;

        // ======================
        // Sauvegarde du fichier brut
        // ======================
        $stmtFile = $pdo->prepare("
            INSERT INTO imports_notes 
                (faculte, departement, programme, licence, semestre, annee_univ, cohorte, 
                 nom_fichier, type_fichier, taille, contenu) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtFile->execute([
            $nomFaculte, $nomDepartement, $nomDepartement, $nomLicence,
            $semestre, $annee_univ, $cohorte,
            $nomFichier, $typeFichier, $tailleFichier, $contenuFichier
        ]);

        // ======================
        // Cohorte / Année / Niveau / Semestre
        // ======================
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cohortes WHERE CodeCoh = ?");
        $stmt->execute([$cohorte]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO cohortes (CodeCoh, Cohorte) VALUES (?, ?)")->execute([$cohorte, $cohorte]);
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM annees_univ WHERE annee = ?");
        $stmt->execute([$annee_univ]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO annees_univ (annee) VALUES (?)")->execute([$annee_univ]);
        }

        $codeNiv = 'NIV-' . $semestre;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM niveaux WHERE CodeNiv = ?");
        $stmt->execute([$codeNiv]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO niveaux (CodeNiv, Niveau, AnneeUniv) VALUES (?, ?, ?)")
                ->execute([$codeNiv, $semestre, $annee_univ]);
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM semestres WHERE CodeSemes = ?");
        $stmt->execute([$semestre]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO semestres (CodeSemes, CodeNiv, NivauSemes, CodeLic) VALUES (?, ?, ?, ?)")
                ->execute([$semestre, $codeNiv, $semestre, $licence]);
        }

        // ======================
        // Vérifier professeur lié à la matière
        // ======================
        $stmtProf = $pdo->prepare("SELECT Professeur FROM matieres WHERE CodeMat = ?");
        $stmtProf->execute([$matiere]);
        $professeur = $stmtProf->fetchColumn();
        if (!$professeur) {
            die("❌ Impossible de trouver le professeur pour la matière $matiere !");
        }

        // ======================
        // Import des étudiants + notes
        // ======================
        $etudiantsRejetes = [];

        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // Ignorer entête Excel

            $ine       = trim($row[1]);
            $nomEtu    = trim($row[2]);
            $prenomEtu = trim($row[3]);

            $noteIndiv = floatval(str_replace(',', '.', $row[4]));
            $noteGroup = floatval(str_replace(',', '.', $row[5]));
            $noteExa   = floatval(str_replace(',', '.', $row[6]));
            $moyenneG  = floatval(str_replace(',', '.', $row[7]));

            $stmt = $pdo->prepare("SELECT MatEtu FROM etudiants WHERE MatEtu = ?");
            $stmt->execute([$ine]);
            $matEtu = $stmt->fetchColumn();

            if (!$matEtu) {
                // Étudiant non reconnu → stockage dans table spéciale
                $stmtNonConnu = $pdo->prepare("
                    INSERT INTO etudiants_non_connus
                        (INE, NomEtu, PrenomEtu, CodeFac, CodeDep, CodeLic, CodeSemes, CodeCoh, AnneeUniv)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtNonConnu->execute([
                    $ine, $nomEtu, $prenomEtu,
                    $faculte, $departement, $licence,
                    $semestre, $cohorte, $annee_univ
                ]);

                $etudiantsRejetes[] = [
                    'INE'    => $ine,
                    'Nom'    => $nomEtu,
                    'Prenom' => $prenomEtu
                ];
                continue;
            }

            // ✅ Insérer la note avec le prof
            $stmtNote = $pdo->prepare("
                INSERT INTO notes (MatEtu, CodeSemes, CodeMat, NoteIndiv, NoteGroup, NoteExa, MoyenneG, noteProf, DateNote)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())
                ON DUPLICATE KEY UPDATE 
                    NoteIndiv = VALUES(NoteIndiv),
                    NoteGroup = VALUES(NoteGroup),
                    NoteExa   = VALUES(NoteExa),
                    MoyenneG  = VALUES(MoyenneG),
                    noteProf  = VALUES(noteProf)
            ");
            $stmtNote->execute([$matEtu, $semestre, $matiere, $noteIndiv, $noteGroup, $noteExa, $moyenneG, $professeur]);
        }

        // ✅ Un seul commit ici
        $pdo->commit();

        if (!empty($etudiantsRejetes)) {
            echo "<!DOCTYPE html>
            <html lang='fr'>
            <head>
              <meta charset='UTF-8'>
              <title>Étudiants rejetés</title>
              <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
            </head>
            <body class='p-4'>
              <div class='alert alert-warning'>
                <h4>⚠️ Étudiants non inscrits détectés</h4>
                <p>Ces étudiants n'existent pas dans la base officielle. 
                Ils ont été insérés dans la table <strong>etudiants_non_connus</strong> :</p>
                <ul>";
                foreach ($etudiantsRejetes as $etu) {
                    echo "<li><strong>{$etu['INE']}</strong> - {$etu['Nom']} {$etu['Prenom']}</li>";
                }
            echo "  </ul>
                <a href='importer_notes.php' class='btn btn-primary'>Continuer</a>
              </div>
            </body>
            </html>";
            exit;
        }

        // ✅ Message de succès normal
        echo "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
          <meta charset='UTF-8'>
          <title>Importation réussie</title>
          <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
          <div class='modal fade show' id='successModal' tabindex='-1' style='display:block; background:rgba(0,0,0,0.5);' aria-modal='true' role='dialog'>
            <div class='modal-dialog'>
              <div class='modal-content'>
                <div class='modal-header bg-success text-white'>
                  <h5 class='modal-title'>✅ Importation réussie</h5>
                </div>
                <div class='modal-body'>
                  <p>Le fichier et les notes ont été importés avec succès.</p>
                </div>
                <div class='modal-footer'>
                  <a href='importer_notes.php' class='btn btn-primary'>Voir les notes</a>
                  <a href='dashboard_admin.php' class='btn btn-secondary'>Fermer</a>
                </div>
              </div>
            </div>
          </div>
        </body>
        </html>
        ";

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("❌ Erreur lors de l'import : " . $e->getMessage());
    }

} else {
    die("Requête invalide.");
}
