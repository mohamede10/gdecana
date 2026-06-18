<?php
require_once "config/db.php"; // connexion PDO

// Récupération des statistiques
$total_fac = $pdo->query("SELECT COUNT(*) FROM facultes")->fetchColumn();
$total_dep = $pdo->query("SELECT COUNT(*) FROM departements")->fetchColumn();
$total_etu = $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn();
// Nombre de matières
$total_mat = $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn();

// Nombre de licences
$total_lic = $pdo->query("SELECT COUNT(*) FROM licences")->fetchColumn();


// Étudiants en reprise (Moyenne < 5)
$total_reprise = $pdo->query("
    SELECT COUNT(DISTINCT MatEtu) 
    FROM notes 
    WHERE MoyenneG < 5
")->fetchColumn();

// Étudiants valides (Moyenne >= 5)
$total_valide = $total_etu - $total_reprise;
?>

<style>
    .small-card .card-body {
    padding: 10px;
}
.small-card h6 {
    font-size: 0.8rem;
}
.small-card h4, 
.small-card h3 {
    font-size: 1.2rem;
}

</style>
<!-- Section Statistiques -->
<div id="statistiques" class="">
  
    <div class="row g-2 mt-1">
    <div class="col-md-2">
        <div class="card text-center bg-primary text-white small-card">
            <div class="card-body">
                <h6 class="card-title">Facultés inscrites</h6>
                <h4><?= $total_fac ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center bg-success text-white small-card">
            <div class="card-body">
                <h6 class="card-title">Départements inscrits</h6>
                <h4><?= $total_dep ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center bg-info text-white small-card">
            <div class="card-body">
                <h6 class="card-title">Étudiants inscrits</h6>
                <h4><?= $total_etu ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center bg-danger text-white small-card">
            <div class="card-body">
                <h6 class="card-title">Étudiants en reprise</h6>
                <h4><?= $total_reprise ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
    <div class="card text-center bg-warning text-white small-card">
        <div class="card-body">
            <h6 class="card-title">Matières</h6>
            <h4><?= $total_mat ?></h4>
        </div>
    </div>
</div>

<div class="col-md-2">
    <div class="card text-center bg-secondary text-white small-card">
        <div class="card-body">
            <h6 class="card-title">Licences</h6>
            <h4><?= $total_lic ?></h4>
        </div>
    </div>
</div>

</div>

 <br>
    <!-- Graphiques -->
    <div class="row">
    <!-- Camembert -->
    <div class="col-md-6 d-flex flex-column align-items-center">
        <h5 class="text-center">Répartition des étudiants</h5>
        <div style="width:300px; height:300px;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <!-- Histogramme -->
    <div class="col-md-6 d-flex flex-column align-items-center">
        <h5 class="text-center">Statistiques générales</h5>
        <div style="width:400px; height:300px;">
            <canvas id="barChart"></canvas>
        </div>
    </div>
</div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données PHP vers JS
    const totalEtu = <?= $total_etu ?>;
    const totalReprise = <?= $total_reprise ?>;
    const totalValide = <?= $total_valide ?>;
    const totalFac = <?= $total_fac ?>;
    const totalDep = <?= $total_dep ?>;

    // Camembert Étudiants
    new Chart(document.getElementById("pieChart"), {
        type: "pie",
        data: {
            labels: ["Étudiants valides", "Étudiants en reprise"],
            datasets: [{
                data: [totalValide, totalReprise],
                backgroundColor: ["#28a745", "#dc3545"]
            }]
        }
    });

    // Histogramme
    new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: ["Facultés", "Départements", "Étudiants", "Reprises"],
            datasets: [{
                label: "Statistiques",
                data: [totalFac, totalDep, totalEtu, totalReprise],
                backgroundColor: ["#007bff", "#17a2b8", "#28a745", "#dc3545"]
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
