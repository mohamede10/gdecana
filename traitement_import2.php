<?php 
session_start();
require 'config/db.php';
require 'vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des variables du formulaire
$faculte     = $_POST['faculte']    ?? '';
$departement = $_POST['departement']?? '';
$licence     = $_POST['programme']  ?? ''; // le champ "programme" = en fait une licence
$semestre    = $_POST['semestre']   ?? '';
$annee_univ  = $_POST['annee_univ'] ?? '';
$cohorte     = $_POST['cohorte']    ?? '';
$MatSigna    = $_POST['MatSigna']   ?? '';

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

    $nomProgramme = $nomDepartement; // programme = departement dans ton cas

    $stmt = $pdo->prepare("SELECT NomLic FROM licences WHERE CodeLic = ?");
    $stmt->execute([$licence]);
    $nomLicence = $stmt->fetchColumn() ?: $licence;

    // ======================
    // Sauvegarde du fichier brut
    // ======================
    $stmtFile = $pdo->prepare("
        INSERT INTO imports_notes 
            (faculte, departement, programme, licence, semestre, annee_univ, cohorte, MatSigna, 
             nom_fichier, type_fichier, taille, contenu) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtFile->execute([
        $nomFaculte, $nomDepartement, $nomProgramme, $nomLicence,
        $semestre, $annee_univ, $cohorte, $MatSigna,
        $nomFichier, $typeFichier, $tailleFichier, $contenuFichier
    ]);

    // ======================
    // Récupération/Création CodeLic
    // ======================
    $stmt = $pdo->prepare("SELECT CodeLic FROM licences WHERE CodeLic = ?");
    $stmt->execute([$licence]);
    $CodeLicExist = $stmt->fetchColumn();

    if (!$CodeLicExist) {
        $pdo->prepare("INSERT INTO licences (CodeLic, NomLic, CodeDep) VALUES (?, ?, ?)")
            ->execute([$licence, $nomLicence, $departement]);
        $CodeLicExist = $pdo->lastInsertId();
    }

    // ... (la suite reste inchangée : cohorte, année, niveau, semestre, étudiants, notes)


        // Cohorte
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cohortes WHERE CodeCoh = ?");
        $stmt->execute([$cohorte]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO cohortes (CodeCoh, Cohorte) VALUES (?, ?)")->execute([$cohorte, $cohorte]);
        }

        // Année universitaire
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM annees_univ WHERE annee = ?");
        $stmt->execute([$annee_univ]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO annees_univ (annee) VALUES (?)")->execute([$annee_univ]);
        }

        // Niveau
        $codeNiv = 'NIV-' . $semestre;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM niveaux WHERE CodeNiv = ?");
        $stmt->execute([$codeNiv]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO niveaux (CodeNiv, Niveau, AnneeUniv) VALUES (?, ?, ?)")
                ->execute([$codeNiv, $semestre, $annee_univ]);
        }

        // Semestre
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM semestres WHERE CodeSemes = ?");
        $stmt->execute([$semestre]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO semestres (CodeSemes, CodeNiv, NivauSemes, CodeLic) VALUES (?, ?, ?, ?)")
                ->execute([$semestre, $codeNiv, $semestre, $CodeLicExist]);
        }

        // ======================
        // Import des étudiants + notes
        // ======================
        foreach ($rows as $i => $row) {
            if ($i === 0) continue; // Ignorer l'entête Excel

            $ine       = trim($row[1]); // INE unique
            $nomEtu    = trim($row[2]);
            $prenomEtu = trim($row[3]);

            $noteIndiv = floatval(str_replace(',', '.', $row[4]));
            $noteGroup = floatval(str_replace(',', '.', $row[5]));
            $noteExa   = floatval(str_replace(',', '.', $row[6]));

            // Vérifier si étudiant existe déjà
            $stmt = $pdo->prepare("SELECT MatEtu FROM etudiants WHERE MatEtu = ?");
            $stmt->execute([$ine]);
            $matEtu = $stmt->fetchColumn();

            if (!$matEtu) {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO etudiants 
                        (MatEtu, INE, NomEtu, PrenomEtu, CodeFac, CodeDep, CodeProg, CodeLic, CodeSemes, CodeCoh)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtInsert->execute([
                    $ine, $ine, $nomEtu, $prenomEtu,
                    $faculte, $departement, $programme, $CodeLicExist, $semestre, $cohorte
                ]);
                $matEtu = $ine;
            }

            // Matière (à adapter si plusieurs matières)
            $codeMat = "MAT1";
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM matieres WHERE CodeMat = ?");
            $stmt->execute([$codeMat]);
            if ($stmt->fetchColumn() == 0) {
                $pdo->prepare("INSERT INTO matieres (CodeMat, CodeSemes, NomMat, CrediMat, Statut) 
                               VALUES (?, ?, ?, ?, ?)")
                    ->execute([$codeMat, $semestre, "Matière 1", 3, "Obligatoire"]);
            }

            // Insérer les notes
            $stmtNote = $pdo->prepare("
                INSERT INTO notes (MatEtu, MatSigna, CodeSemes, CodeMat, NoteIndiv, NoteGroup, NoteExa, DateNote)
                VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())
                ON DUPLICATE KEY UPDATE 
                    NoteIndiv = VALUES(NoteIndiv),
                    NoteGroup = VALUES(NoteGroup),
                    NoteExa   = VALUES(NoteExa)
            ");
            $stmtNote->execute([$matEtu, $MatSigna, $semestre, $codeMat, $noteIndiv, $noteGroup, $noteExa]);
        }

        $pdo->commit();
echo "
<!DOCTYPE html>
<html lang='fr'>
<head>
  <meta charset='UTF-8'>
  <title>Importation réussie</title>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
  <!-- Modal -->
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
        $pdo->rollBack();
        die("❌ Erreur lors de l'import : " . $e->getMessage());
    }

} else {
    die("Requête invalide.");
}
