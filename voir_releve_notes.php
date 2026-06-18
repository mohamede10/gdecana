<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Relevé non spécifié.");

// Récupération du relevé
$stmt = $pdo->prepare("
    SELECT r.*, e.PrenomEtu, e.NomEtu, e.INE, e.DateNais, e.LieuNais,
           e.NomPere, e.NomMere, p.NomProg, d.NomDep, f.NomFac, c.Cohorte
    FROM releves_notes r
    JOIN etudiants e ON r.MatEtu = e.MatEtu
    LEFT JOIN programmes p ON e.CodeProg = p.CodeProg
    LEFT JOIN departements d ON p.CodeDep = d.CodeDep
    LEFT JOIN facultes f ON d.CodeFac = f.CodeFac
    LEFT JOIN cohortes c ON e.CodeCoh = c.CodeCoh
    WHERE r.id = :id
");
$stmt->execute(['id' => $id]);
$attestation = $stmt->fetch();

if (!$attestation) die("Relevé non trouvé.");

// Variables
$nom = strtoupper($attestation['NomEtu']);
$prenom = ucfirst(strtolower($attestation['PrenomEtu']));
$ine = $attestation['INE'];
$programme = $attestation['NomProg'];
$faculte = $attestation['NomFac'];
$departement = $attestation['NomDep'];
$cohorte = $attestation['Cohorte'];
$annee_univ = $attestation['annee_univ'];
$semestre = $attestation['semestre'];
$numero_attestation = $attestation['numero_attestation'];
$date_jour = date('d/m/Y');

// Récupération des notes détaillées
$stmtNotes = $pdo->prepare("
    SELECT m.CodeMat, m.NomMat, m.CrediMat, 
           n.NoteCC, n.NoteTP, n.NoteExa,
           ROUND((n.NoteCC + n.NoteTP + n.NoteExa) / 3, 2) AS MoyenneMatiere
    FROM notes n
    JOIN matieres m ON n.CodeMat = m.CodeMat
    WHERE n.MatEtu = :mat AND n.CodeSemes = :sem
");
$stmtNotes->execute(['mat' => $attestation['MatEtu'], 'sem' => $semestre]);
$notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

// Calcul moyenne générale
$total = 0;
$count = 0;
foreach ($notes as $n) {
    $total += $n['MoyenneMatiere'];
    $count++;
}
$moyenneGenerale = $count > 0 ? round($total / $count, 2) : 0;
$decision = $moyenneGenerale >= 5 ? "Semestre Validé ✅" : "Semestre Non Validé ❌";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Relevé de Notes - <?= $prenom ?> <?= $nom ?></title>
<style>
    body { font-family: 'Georgia', serif; margin:0; padding:0; background:#f5f5f5; }
    .attestation {
        width: 210mm;
        height: 297mm;
        margin: 20px auto;
        padding: 40px;
        position: relative;
        background: #fff;
        border: 2px solid #000;
        box-sizing: border-box;
        overflow: hidden;
    }
    .attestation::before {
        content: "UNIVERSITE DE LABE";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 100px;
        font-weight: bold;
        color: rgba(0,0,0,0.07);
        z-index: 0;
        pointer-events: none;
        white-space: nowrap;
    }
    .logo { position: absolute; top: 20px; right: 20px; width: 100px; }
    .cachet { display: block; margin: 10px auto 0 auto; opacity: 0.7; width: 120px; }
    .center { text-align:center; z-index:1; position:relative; }
    .titre { font-size:26px; margin:20px 0; text-decoration:underline; font-weight:600; color:#2c3e50; }
    .contenu { margin-top:25px; font-size:16px; line-height:1.6; color:#000; z-index:1; position:relative; }
    table { width:100%; border-collapse: collapse; margin-top:15px; }
    th, td { border: 1px solid #bdc3c7; padding:8px; text-align:center; }
    th { background:#ecf0f1; color:#003e7c; }
    .footer { margin-top:40px; font-size:14px; text-align:center; }
    .signature { margin-top:60px; text-align:right; }
    @media print {
        body { background: none; }
        .attestation { box-shadow:none; border:none; margin:0; page-break-after: always; }
    }
</style>
</head>
<body>
<div class="attestation">
    <img src="img/Logo_univ_labe.png" class="logo" alt="Logo Université">
    <div class="center">
        <p>UL /SS/2025</p>
        <p class="titre">RELEVÉ DE NOTES</p>
    </div>
    <div class="contenu">
        <p><strong>Nom :</strong> <?= $nom ?> — <strong>Prénom :</strong> <?= $prenom ?></p>
        <p><strong>INE :</strong> <?= $ine ?> — <strong>Cohorte :</strong> <?= $cohorte ?></p>
        <p><strong>Programme :</strong> <?= $programme ?> — <strong>Faculté :</strong> <?= $faculte ?> — <strong>Département :</strong> <?= $departement ?></p>
        <p><strong>Semestre :</strong> <?= $semestre ?> — <strong>Année universitaire :</strong> <?= $annee_univ ?></p>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Matière</th>
                    <th>Crédits</th>
                    <th>CC</th>
                    <th>TP</th>
                    <th>Examen</th>
                    <th>Moyenne</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes as $n): ?>
                    <tr>
                        <td><?= htmlspecialchars($n['CodeMat']) ?></td>
                        <td><?= htmlspecialchars($n['NomMat']) ?></td>
                        <td><?= htmlspecialchars($n['CrediMat']) ?></td>
                        <td><?= $n['NoteCC'] ?></td>
                        <td><?= $n['NoteTP'] ?></td>
                        <td><?= $n['NoteExa'] ?></td>
                        <td><strong><?= $n['MoyenneMatiere'] ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6">Moyenne Générale</th>
                    <th><?= $moyenneGenerale ?></th>
                </tr>
            </tfoot>
        </table>

        <p><strong>Décision :</strong> <?= $decision ?></p>
    </div>
    <div class="footer">
        <p>Labé, le <?= $date_jour ?></p>
        <div class="signature">
            <p>Le Recteur</p>
            <br>
            <img src="images/cachet.png" class="cachet" alt="Cachet officiel">
            <br>
            ______________________
        </div>
        <p>Numéro du relevé : <strong><?= $numero_attestation ?></strong></p>
    </div>
</div>
</body>
</html>
