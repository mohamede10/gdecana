<?php 
session_start();
require 'config/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Récupération des données formulaire
$ine         = $_POST['identifiant'];
$type        = $_POST['type'];

// Charger infos étudiant
$sql = "
    SELECT 
        e.MatEtu,
        e.INE,
        e.NomEtu      AS Nom,
        e.PrenomEtu   AS Prenom,
        e.DateNais    AS DateNaissance,
        e.LieuNais    AS LieuNaissance,
        e.Sexe,
        e.Nationalite,
        e.TelephoneEtu,
        e.Mail,
        e.photo,
        e.NomPere,
        e.NomMere,
        e.SessionBAC,
        e.OptionBAC,
        e.PVBAC,
        e.CentreExamen,
        e.Lycee,
        f.NomFac      AS Faculte,
        d.NomDep      AS Departement,
        p.NomProg     AS Programme,
        l.NomLic      AS Licence,
        c.Cohorte,
        n.Niveau,
        an.annee      AS AnneeUniv
    FROM etudiants e
    LEFT JOIN facultes f     ON e.CodeFac  = f.CodeFac
    LEFT JOIN departements d ON e.CodeDep  = d.CodeDep
    LEFT JOIN programmes p   ON e.CodeProg = p.CodeProg
    LEFT JOIN licences l     ON e.CodeLic  = l.CodeLic
    LEFT JOIN cohortes c     ON e.CodeCoh  = c.CodeCoh
    LEFT JOIN inscriptions i ON e.MatEtu   = i.MatEtu
    LEFT JOIN niveaux n      ON i.CodeNiv  = n.CodeNiv
    LEFT JOIN annees_univ an ON n.AnneeUniv = an.annee
    WHERE e.INE = :ine
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':ine' => $ine]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$etudiant) {
    die("⚠️ Étudiant introuvable !");
}

// Notes si relevé
if ($type === 'releve') {
    $sqlNotes = "
        SELECT
            n.CodeMat,
            m.NomMat AS Titre,
            COALESCE(m.Statut, 'Obligatoire') AS Statut,
            COALESCE(m.CrediMat, 0) AS Credits,
            COALESCE(n.MoyenneG,
                     ROUND((COALESCE(n.NoteIndiv,0)+COALESCE(n.NoteGroup,0)+COALESCE(n.NoteExa,0))/3,2)
            ) AS Note10,
            s.NivauSemes
        FROM notes n
        JOIN matieres m  ON m.CodeMat   = n.CodeMat
        JOIN semestres s ON s.CodeSemes = n.CodeSemes
        WHERE n.MatEtu = :mat
        ORDER BY s.NivauSemes, m.CodeMat
    ";
    $st = $pdo->prepare($sqlNotes);
    $st->execute([':mat' => $etudiant['MatEtu']]);

    $notesS1 = $notesS2 = [];
    while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
        $row = [
            'code'    => $r['CodeMat'],
            'titre'   => $r['Titre'],
            'statut'  => $r['Statut'],
            'credits' => (int)$r['Credits'],
            'note'    => (float)$r['Note10'],
        ];
        if (stripos($r['NivauSemes'], '1') !== false) $notesS1[] = $row;
        elseif (stripos($r['NivauSemes'], '2') !== false) $notesS2[] = $row;
    }
}

// Récupération des semestres
$sqlSemestres = "
    SELECT GROUP_CONCAT(DISTINCT REPLACE(s.NivauSemes, 'Semestre ', '') ORDER BY s.CodeSemes SEPARATOR ' et ') AS Semestres
    FROM semestres s
    INNER JOIN licences l ON s.CodeLic = l.CodeLic
    WHERE l.NomLic = :licence
";
$stmtSem = $pdo->prepare($sqlSemestres);
$stmtSem->execute([':licence' => $etudiant['Licence']]);
$rowSem = $stmtSem->fetch(PDO::FETCH_ASSOC);
$etudiant['Semestres'] = $rowSem ? $rowSem['Semestres'] : null;

// Numéro attestation
$numeroAttestation = strtoupper($type) . '-' . uniqid();

// Options Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('chroot', __DIR__ . '/../');
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);

// Charger template
ob_start();
include "templates/$type.php";
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Sauvegarde du PDF
$output = $dompdf->output();
$dir = __DIR__ . "/uploads/attestations/";
if (!is_dir($dir)) mkdir($dir, 0777, true);

$nom_fichier = $numeroAttestation . ".pdf";
$chemin_pdf  = $dir . $nom_fichier;
file_put_contents($chemin_pdf, $output);

// --- Sauvegarde en base ---
$utilisateurId = $_SESSION['id_utilisateur'] ?? null; 
$signataireId  = $etudiant['MatSigna'] ?? null; 

switch ($type) {
    case 'admission':
        $sql = "INSERT INTO attestations_admission
                (MatEtu, session_bac, option_bac, pv_bac, programme, annee_univ,
                 numero_attestation, chemin_pdf, utilisateur_id, MatSigna)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $etudiant['MatEtu'],
            $etudiant['SessionBAC'],
            $etudiant['OptionBAC'],
            $etudiant['PVBAC'],
            $etudiant['Programme'],
            $etudiant['AnneeUniv'],
            $numeroAttestation,
            $chemin_pdf,
            $utilisateurId,
            $signataireId
        ]);
        break;

    case 'inscription':
        $sql = "INSERT INTO attestations_inscription
                (MatEtu, programme, cohorte, annee_univ,
                 numero_attestation, chemin_pdf, utilisateur_id, MatSigna)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $etudiant['MatEtu'],
            $etudiant['Programme'],
            $etudiant['Cohorte'],
            $etudiant['AnneeUniv'],
            $numeroAttestation,
            $chemin_pdf,
            $utilisateurId,
            $signataireId
        ]);
        break;

    case 'reinscription':
        $sql = "INSERT INTO attestations_reinscription
                (MatEtu, programme, niveau, annee_univ,
                 numero_attestation, chemin_pdf, utilisateur_id, MatSigna)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $etudiant['MatEtu'],
            $etudiant['Programme'],
            $etudiant['Niveau'],
            $etudiant['AnneeUniv'],
            $numeroAttestation,
            $chemin_pdf,
            $utilisateurId,
            $signataireId
        ]);
        break;

    case 'niveau':
        $sql = "INSERT INTO attestations_niveaux
                (MatEtu, niveau, annee_univ,
                 numero_attestation, chemin_pdf, utilisateur_id, MatSigna)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $etudiant['MatEtu'],
            $etudiant['Niveau'],
            $etudiant['AnneeUniv'],
            $numeroAttestation,
            $chemin_pdf,
            $utilisateurId,
            $signataireId
        ]);
        break;

    case 'carte':
        $sql = "INSERT INTO cartes_scolaires
                (MatEtu, programme, niveau, annee_univ,
                 numero_attestation, chemin_pdf, utilisateur_id, MatSigna)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $etudiant['MatEtu'],
            $etudiant['Programme'],
            $etudiant['Niveau'],
            $etudiant['AnneeUniv'],
            $numeroAttestation,
            $chemin_pdf,
            $utilisateurId,
            $signataireId
        ]);
        break;

    case 'releve':
        $sql = "INSERT INTO releves_notes
                (MatEtu, semestre, annee_univ, moyenne, decision,
                 numero_attestation, chemin_pdf, utilisateur_id, MatSigna)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $etudiant['MatEtu'],
            $etudiant['Semestres'],
            $etudiant['AnneeUniv'],
            null,        // moyenne calculée si besoin
            null,        // décision (admis/ajourné)
            $numeroAttestation,
            $chemin_pdf,
            $utilisateurId,
            $signataireId
        ]);
        break;
}

// --- Affichage PDF ---
$dompdf->stream("attestation_$type.pdf", ["Attachment" => false]);
exit;
