<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: login.php');
    exit;
}
require 'config/db.php';

$utilisateur_id = $_SESSION['utilisateur_id'];
$role = $_SESSION['role'] ?? 'scolarite';

if (!isset($_SESSION['nom_utilisateur'])) {
    $stmt = $pdo->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
    $stmt->execute([$utilisateur_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['nom_utilisateur'] = $row['nom'] ?? 'Utilisateur';
}
$nom_utilisateur = $_SESSION['nom_utilisateur'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard - Université de Labé</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background: #f4f6fa;
        font-family: "Segoe UI", sans-serif;
    }
    /* Sidebar */
    .sidebar {
        height: 100vh;
        width: 250px;
        background: linear-gradient(180deg, #0d6efd, #084298);
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 0;
        box-shadow: 2px 0 15px rgba(0,0,0,0.1);
    }
    .sidebar .avatar {
        width: 70px;
        height: 70px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #0d6efd;
        margin-bottom: 10px;
    }
    .sidebar p {
        font-size: 0.9rem;
        margin-bottom: 30px;
        text-align: center;
    }
    .sidebar a {
        color: white;
        width: 90%;
        padding: 10px 15px;
        margin: 5px 0;
        border-radius: 8px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        cursor: pointer;
    }
    .sidebar a:hover, .sidebar a.active {
        background: rgba(255,255,255,0.2);
    }

    /* Content */
    .content {
        margin-left: 250px;
        padding: 30px;
    }
    .header {
        background: white;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .card-title {
        font-weight: 600;
        font-size: 0.95rem;
    }

    #rideau {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(180deg, #0d6efd, #084298);
    transform: translateY(-100%); /* Caché en haut */
    transition: transform 0.8s ease-in-out;
    z-index: 2000;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
}
#rideau.active {
    transform: translateY(0); /* Descend sur l'écran */
}


/* Sidebar masquable */
.sidebar {
    height: 100vh;
    width: 250px;
    background: linear-gradient(180deg, #0d6efd, #084298);
    color: white;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 0;
    box-shadow: 2px 0 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

/* Quand on cache la sidebar */
.sidebar.hidden {
    transform: translateX(-100%);
}

/* Adapter le contenu quand sidebar cachée */
.content {
    margin-left: 250px;
    transition: margin-left 0.3s ease;
}
.content.full {
    margin-left: 0;
}


#toggleBtn {
    position: absolute;
    top: 10px;
    right: -15px;
    border-radius: 50%;
    background: white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="avatar">
        <i class="bi bi-person"></i>
    </div>
    <button id="toggleBtn" class="btn btn-light btn-sm" onclick="toggleMenu()">
         <i class="bi bi-chevron-left fa-2x"></i>
    </button>

    <p><?= htmlspecialchars($nom_utilisateur) ?><br><small>(<?= htmlspecialchars($role) ?>)</small></p>
    <a onclick="showContent('statistiques')" class="active"><i class="bi bi-circle"></i> Statistiques</a>
    <a onclick="showContent('enregistrement')"><i class="bi bi-folder-plus"></i>Enregistrement</a>
    <a onclick="showContent('gestion')"><i class="bi bi-folder"></i> Gestion de fichiers</a>
    <a onclick="showContent('autres')"><i class="bi bi-gear"></i> Parametre</a>
    <a onclick="verrouiller()"><i class="bi bi-lock"></i> Verrouiller</a>
    <!-- Sidebar <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>-->
</div>

<!-- Content -->
<div class="content">
    <div class="header">
        <h4>Tableau de bord Administration</h4>
        <span class="text-muted">Bienvenue à l’Université de Labé</span>
    </div>

    <!-- Section : statistiques -->
    <div id="statistiques" class="content-section">
        
        <div class="row g-4 mt-1">
           <?php include "statistiques.php"; ?>
        </div>
    </div>

    <!-- Section : Enregistrement -->
    <div id="enregistrement" class="content-section" style="display:none">
        <h5>🗂️ Enregistrement</h5>
        <div class="row g-4 mt-1">
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Ajouter Faculté</h6><a href="ajouter_faculte_departement.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Ajouter Département</h6><a href="ajouter_departement.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Inscription Étudiant</h6><a href="ajouter_etudiant.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Générer Attestation</h6><a href="attestation_form.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title">Réinscription Étudiant</h6><a href="reins_etu.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
             <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Ajouter une maière</h6><a href="insert_matieres.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Importer Notes</h6><a href="importer_notes.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
        </div>
    </div>

    <!-- Section : Gestion -->
    <div id="gestion" class="content-section" style="display:none">
        <h5>📁 Gestion des fichiers</h5>
        <div class="row g-4 mt-1">
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Attestation Admission</h6><a href="liste_att_admission.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Attestation d'Inscription</h6><a href="liste_att_inscription.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Attestation Niveau</h6><a href="liste_att_niveau.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Attestation Réinscription</h6><a href="liste_att_reinscription.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
             <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Liste des releves de notes</h6><a href="liste_releve_notes.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title">Carte Scolaire</h6><a href="liste_cartes_scolaires.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Liste Étudiants</h6><a href="liste_etudiants.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Liste Département</h6><a href="liste_departements.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title">Liste Faculté</h6><a href="liste_facultes.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> Liste des notes</h6><a href="liste_fichiers_importes.php" class="btn btn-primary btn-sm">Continuer </a></div></div></div>
        </div>
    </div>

    <!-- Section : Autres -->
    <div id="autres" class="content-section" style="display:none">
        <h5>⚙️ Panneau de configuration</h5>
        <div class="row g-4 mt-1">
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h6 class="card-title"> À venir</h6><p class="small">Fonctionnalités supplémentaires bientôt disponibles.</p></div></div></div>
        </div>
    </div>
</div>

<footer class="pt-5">
    <div class="container">
        
        <div class="text-center border-top border-secondary mt-4 pt-3">
            <p class="mb-0">&copy; <?= date("Y") ?> <strong>Université de Labé</strong>. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script>
function showContent(sectionId) {
    document.querySelectorAll('.content-section').forEach(sec => sec.style.display = 'none');
    document.getElementById(sectionId).style.display = 'block';

    document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
    event.target.classList.add('active');
}
</script>


<div id="rideau" class="text-center">
    🔒 <br> Session verrouillée
</div>

<script>

function toggleMenu() {
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');
    const btnIcon = document.querySelector('#toggleBtn i');

    sidebar.classList.toggle('hidden');
    content.classList.toggle('full');

    // Change la flèche selon l'état
    if (sidebar.classList.contains('hidden')) {
        btnIcon.classList.remove('bi-chevron-left');
        btnIcon.classList.add('bi-chevron-right');
    } else {
        btnIcon.classList.remove('bi-chevron-right');
        btnIcon.classList.add('bi-chevron-left');
    }
}


</script>

<script>
function verrouiller() {
    document.getElementById('rideau').classList.add('active');
    setTimeout(() => {
        window.location.href = "login.php";
    }, 1000);
}

</script>
</body>
</html>
