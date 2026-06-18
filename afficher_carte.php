<?php
require_once 'config/db.php';
session_start();

if (!isset($_GET['numero'])) {
    die("Numéro de carte manquant.");
}

$numero = $_GET['numero'];

// Récupération des infos de la carte + étudiant
$sql = "
    SELECT cs.numero_attestation, cs.annee_univ, cs.date_generation,
           cs.programme, cs.niveau,
           e.MatEtu, e.NomEtu, e.PrenomEtu, e.DateNais, e.LieuNais, 
           e.Sexe, e.Nationalite, e.photo
    FROM cartes_scolaires cs
    JOIN etudiants e ON cs.MatEtu = e.MatEtu
    WHERE cs.numero_attestation = :numero
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['numero' => $numero]);
$carte = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carte) {
    die("Carte non trouvée.");
}

// Chemins photo
$photoPath = __DIR__ . "/uploads/photos/" . $carte['photo'];
$photoUrl  = "uploads/photos/" . $carte['photo'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte Étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
    .cartes-container {
        display: flex;
        gap: 20px;
    }
    .carte {
        width: 350px;
        height: 220px;
        border: 2px solid #000;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        box-sizing: border-box;
        padding: 10px;
    }
    .recto {
        background: #e9f1f8;
    }
    .verso {
        background: #fdfdfd;
    }
    .photo {
        width: 90px;
        height: 110px;
        border: 1px solid #000;
        object-fit: cover;
    }
    </style>
</head>
<body class="container mt-5">

<div class="cartes-container">

    <!-- Recto -->
    <div class="carte recto">
        <h6 class="text-center mb-2">Carte Étudiant</h6>
        <div class="d-flex">
            <div class="me-3">
                <?php if (!empty($carte['photo']) && file_exists($photoPath)): ?>
                    <img src="<?= htmlspecialchars($photoUrl) ?>" class="photo" alt="Photo">
                <?php else: ?>
                    <div class="photo bg-secondary"></div>
                <?php endif; ?>
            </div>
            <div>
                <p><strong>Nom :</strong> <?= strtoupper(htmlspecialchars($carte['NomEtu'])) ?></p>
                <p><strong>Prénom :</strong> <?= ucfirst(strtolower(htmlspecialchars($carte['PrenomEtu']))) ?></p>
                <p><strong>Matricule :</strong> <?= htmlspecialchars($carte['MatEtu']) ?></p>
                <p><strong>Date Naiss. :</strong> <?= date('d/m/Y', strtotime($carte['DateNais'])) ?></p>
                <p><strong>Sexe :</strong> <?= htmlspecialchars($carte['Sexe']) ?></p>
            </div>
        </div>
    </div>

    <!-- Verso -->
    <div class="carte verso">
        <h6 class="text-center mb-2">Carte Étudiant</h6>
        <p><strong>Nationalité :</strong> <?= htmlspecialchars($carte['Nationalite']) ?></p>
        <p><strong>Année universitaire :</strong> <?= htmlspecialchars($carte['annee_univ']) ?></p>
        <p><strong>Programme :</strong> <?= htmlspecialchars($carte['programme']) ?></p>
        <p><strong>Niveau :</strong> <?= htmlspecialchars($carte['niveau']) ?></p>
        <hr>
        <p><strong>Numéro :</strong> <?= htmlspecialchars($carte['numero_attestation']) ?></p>
        <p><strong>Signature :</strong> ______________________</p>
    </div>

</div>

</body>
</html>
