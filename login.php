<?php
session_start();
require 'config/db.php';

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $mdp = $_POST["mot_de_passe"];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mot_de_passe'])) {
        // Stocker uniquement les informations utiles dans la session
        $_SESSION["utilisateur_id"] = $user['id'];
        $_SESSION["role"] = $user['role'];
        $_SESSION["nom_utilisateur"] = $user['nom'];

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header("Location: dashboard_admin.php");
        } elseif ($user['role'] === 'scolarite') {
            header("Location: dashboard_scolarite.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $erreur = "❌ Identifiants incorrects. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - SGA Université de Labé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dbeafe, #93c5fd);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
        }
        .logo {
            width: 70px;
        }
        .btn-primary {
            background-color: #1a73e8;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0c5cd4;
        }
        .form-floating > label {
            color: #6c757d;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4 bg-white">
                <div class="text-center mb-4">
                    <img src="img/Logo_univ_labe.png" alt="Université de Labé" class="logo mb-2">
                    <h4 class="fw-bold">SGA - Université de Labé</h4>
                    <p class="text-muted mb-0">Système de Gestion Académique</p>
                </div>

                <?php if (!empty($erreur)): ?>
                    <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="email" placeholder="exemple@ulabe.edu.gn" required>
                        <label for="email"><i class="bi bi-envelope-fill me-2"></i>Adresse email</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="mot_de_passe" class="form-control" id="mot_de_passe" placeholder="Mot de passe" required>
                        <label for="mot_de_passe"><i class="bi bi-lock-fill me-2"></i>Mot de passe</label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
                    </div>

                    <div class="text-center">
                        <a href="#" class="text-decoration-none text-muted small">Mot de passe oublié ?</a><br>
                        <a href="signup.php" class="text-decoration-none small">Je n'ai pas de compte ?</a>
                    </div>
                </form>

                <div class="mt-4 text-center text-muted small">
                    © <?= date('Y') ?> Université de Labé — Tous droits réservés
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
