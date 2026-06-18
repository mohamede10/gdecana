<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

// Facultés et années universitaires pour formulaire
$facultes = $pdo->query("SELECT CodeFac, NomFac FROM facultes ORDER BY NomFac")->fetchAll(PDO::FETCH_ASSOC);
$annees = $pdo->query("SELECT annee FROM annees_univ ORDER BY annee DESC")->fetchAll(PDO::FETCH_COLUMN);





session_start();
require 'config/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION["utilisateur"])) {
    header("Location: login.php");
    exit;
}

// Quand le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculte = $_POST["faculte"];
    $departement = $_POST["departement"];
    $programme = $_POST["programme"];
    $semestre = $_POST["semestre"];
    $annee = $_POST["annee"];

    $etudiants = $pdo->prepare("
        SELECT DISTINCT e.MatEtu, e.NomEtu, e.PrenomEtu
        FROM etudiants e
        JOIN inscriptions i ON i.MatEtu = e.MatEtu
        JOIN concentrations c ON c.CodeConc = i.CodeConc
        JOIN programmes p ON p.CodeProg = c.CodeProg
        JOIN departements d ON d.CodeDep = p.CodeDep
        JOIN facultes f ON f.CodeFac = d.CodeFac
        JOIN niveaux n ON n.CodeNiv = i.CodeNiv
        WHERE f.CodeFac = ? AND d.CodeDep = ? AND p.CodeProg = ? AND n.AnneeUniv = ?
    ");
    $etudiants->execute([$faculte, $departement, $programme, $annee]);
    $listeEtudiants = $etudiants->fetchAll();

    // Initialisation Dompdf
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $dompdf = new Dompdf($options);

    $html = '<h3 style="text-align:center;">📄 Relevés de notes</h3>';

    $semestresNom = [
        'S1' => 'Semestre 1',
        'S2' => 'Semestre 2',
        'S3' => 'Semestre 3',
        'S4' => 'Semestre 4',
        'S5' => 'Semestre 5',
        'S6' => 'Semestre 6'
    ];

    foreach ($listeEtudiants as $etu) {
        $notes = $pdo->prepare("
            SELECT m.CodeMat, m.NomMat, m.CrediMat, n.NoteExa
            FROM notes n
            JOIN matieres m ON m.CodeMat = n.CodeMat
            WHERE n.MatEtu = ? AND n.CodeSemes = ?
        ");
        $notes->execute([$etu['MatEtu'], $semestre]);
        $listeNotes = $notes->fetchAll();

        $html .= '
        <div style="page-break-after:always; border:1px solid #000; padding:15px; margin-bottom:20px;">
            <h4 style="color:#0d6efd;">🎓 Relevé de Notes</h4>
            <p><strong>Étudiant :</strong> '.htmlspecialchars($etu['NomEtu'].' '.$etu['PrenomEtu']).' 
            — <strong>Matricule :</strong> '.htmlspecialchars($etu['MatEtu']).'</p>
            <p><strong>Année :</strong> '.htmlspecialchars($annee).' 
            | <strong>Semestre :</strong> '.($semestresNom[$semestre] ?? htmlspecialchars($semestre)).'</p>
            <table border="1" cellspacing="0" cellpadding="4" width="100%">
                <thead>
                    <tr style="background:#eee;">
                        <th>Code</th><th>Matière</th><th>Crédits</th><th>Note</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($listeNotes as $note) {
            $html .= '<tr>
                        <td>'.htmlspecialchars($note['CodeMat']).'</td>
                        <td>'.htmlspecialchars($note['NomMat']).'</td>
                        <td>'.htmlspecialchars($note['CrediMat']).'</td>
                        <td>'.htmlspecialchars($note['NoteExa']).'</td>
                      </tr>';
        }

        $html .= '</tbody></table></div>';
    }

    $dompdf->loadHtml($html);

    // ✅ Forcer le portrait
    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();
    $dompdf->stream("releves_notes.pdf", ["Attachment" => true]);
    exit;
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>📄 Génération des Relevés de Notes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background:#f8f9fa; padding:20px; }
        .releve {
            background:#fff;
            border:1px solid #0d6efd;
            padding:20px;
            margin-bottom:30px;
            box-shadow:0 0 8px #ccc;
        }
        .releve h5 { color:#0d6efd; }
        table { font-size:14px; }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mb-4 text-center">📄 Générer les Relevés de Notes</h3>

    <form method="POST" id="formFiltre" class="row g-3 mb-5">
        <div class="col-md-3">
            <label for="faculte" class="form-label">Faculté</label>
            <select name="faculte" id="faculte" class="form-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($facultes as $fac): ?>
                    <option value="<?= htmlspecialchars($fac['CodeFac']) ?>"><?= htmlspecialchars($fac['NomFac']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label for="departement" class="form-label">Département</label>
            <select name="departement" id="departement" class="form-select" required>
                <option value="">-- Choisir Faculté d'abord --</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="programme" class="form-label">Programme</label>
            <select name="programme" id="programme" class="form-select" required>
                <option value="">-- Choisir Département d'abord --</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="semestre" class="form-label">Semestre</label>
            <select name="semestre" id="semestre" class="form-select" required>
                <option value="">-- Choisir Programme d'abord --</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="annee" class="form-label">Année universitaire</label>
            <select name="annee" id="annee" class="form-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($annees as $an): ?>
                    <option value="<?= htmlspecialchars($an) ?>"><?= htmlspecialchars($an) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary">🎓 Générer les relevés</button>
        </div>
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculte = $_POST["faculte"];
    $departement = $_POST["departement"];
    $programme = $_POST["programme"];
    $semestre = $_POST["semestre"];
    $annee = $_POST["annee"];

    // Récupérer les étudiants inscrits dans cette faculté, département, programme, année
    $etudiants = $pdo->prepare("
        SELECT DISTINCT e.MatEtu, e.NomEtu, e.PrenomEtu
        FROM etudiants e
        JOIN inscriptions i ON i.MatEtu = e.MatEtu
        JOIN concentrations c ON c.CodeConc = i.CodeConc
        JOIN programmes p ON p.CodeProg = c.CodeProg
        JOIN departements d ON d.CodeDep = p.CodeDep
        JOIN facultes f ON f.CodeFac = d.CodeFac
        JOIN niveaux n ON n.CodeNiv = i.CodeNiv
        WHERE f.CodeFac = ? AND d.CodeDep = ? AND p.CodeProg = ? AND n.AnneeUniv = ?
    ");
    $etudiants->execute([$faculte, $departement, $programme, $annee]);
    $listeEtudiants = $etudiants->fetchAll();

    if (count($listeEtudiants) === 0) {
        echo "<div class='alert alert-warning'>Aucun étudiant trouvé pour ces critères.</div>";
    }

    // Noms semestres
    $semestresNom = [
        'S1' => 'Semestre 1',
        'S2' => 'Semestre 2',
        'S3' => 'Semestre 3',
        'S4' => 'Semestre 4',
        'S5' => 'Semestre 5',
        'S6' => 'Semestre 6'
    ];

    foreach ($listeEtudiants as $etu) {
        // Récupérer notes du semestre
        $notes = $pdo->prepare("
            SELECT m.CodeMat, m.NomMat, m.CrediMat, n.NoteExa
            FROM notes n
            JOIN matieres m ON m.CodeMat = n.CodeMat
            WHERE n.MatEtu = ? AND n.CodeSemes = ?
        ");
        $notes->execute([$etu['MatEtu'], $semestre]);
        $listeNotes = $notes->fetchAll();

        // Calcul moyenne pondérée
        $total = 0;
        $totalCredits = 0;
        foreach ($listeNotes as $note) {
            $coef = 0;
            if ($note['NoteExa'] >= 8) $coef = 4;
            elseif ($note['NoteExa'] >= 7) $coef = 3;
            elseif ($note['NoteExa'] >= 6) $coef = 2;
            elseif ($note['NoteExa'] >= 5) $coef = 1;

            $total += $coef * $note['CrediMat'];
            $totalCredits += $note['CrediMat'];
        }
        $moyenne = $totalCredits > 0 ? round($total / $totalCredits, 2) : 0;
?>
        <div class="releve">
            <h5>🎓 Relevé de Notes</h5>
            <p><strong>Étudiant :</strong> <?= htmlspecialchars($etu['NomEtu'] . ' ' . $etu['PrenomEtu']) ?> — <strong>Matricule :</strong> <?= htmlspecialchars($etu['MatEtu']) ?></p>
            <p><strong>Année :</strong> <?= htmlspecialchars($annee) ?> | <strong>Semestre :</strong> <?= $semestresNom[$semestre] ?? htmlspecialchars($semestre) ?> | <strong>Moyenne :</strong> <?= $moyenne ?></p>

            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Code</th><th>Matière</th><th>Crédits</th><th>Note</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($listeNotes as $note): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['CodeMat']) ?></td>
                        <td><?= htmlspecialchars($note['NomMat']) ?></td>
                        <td><?= htmlspecialchars($note['CrediMat']) ?></td>
                        <td><?= htmlspecialchars($note['NoteExa']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php
    }
}
?>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    $('#faculte').on('change', function() {
        var fac = $(this).val();
        if(fac) {
            $.post('ajax_departements.php', {faculte: fac}, function(data){
                $('#departement').html(data);
                $('#programme').html('<option value="">-- Choisir Département d\'abord --</option>');
                $('#semestre').html('<option value="">-- Choisir Programme d\'abord --</option>');
            });
        } else {
            $('#departement').html('<option value="">-- Choisir Faculté d\'abord --</option>');
            $('#programme').html('<option value="">-- Choisir Département d\'abord --</option>');
            $('#semestre').html('<option value="">-- Choisir Programme d\'abord --</option>');
        }
    });

    $('#departement').on('change', function() {
        var dep = $(this).val();
        if(dep) {
            $.post('ajax_programmes.php', {departement: dep}, function(data){
                $('#programme').html(data);
                $('#semestre').html('<option value="">-- Choisir Programme d\'abord --</option>');
            });
        } else {
            $('#programme').html('<option value="">-- Choisir Département d\'abord --</option>');
            $('#semestre').html('<option value="">-- Choisir Programme d\'abord --</option>');
        }
    });

    $('#programme').on('change', function() {
        var prog = $(this).val();
        if(prog) {
            $.post('ajax_semestres.php', {programme: prog}, function(data){
                $('#semestre').html(data);
            });
        } else {
            $('#semestre').html('<option value="">-- Choisir Programme d\'abord --</option>');
        }
    });
});
</script>

</body>
</html>