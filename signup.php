<?php
require 'config/db.php';

$erreur = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $email = trim($_POST["email"]);
    $role = $_POST["role"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $confirmer = $_POST["confirmer_mot_de_passe"];

    if ($mot_de_passe !== $confirmer) {
        $erreur = "❌ Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "❌ Un compte avec cet email existe déjà.";
        } else {
            // Création du compte
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nom, $email, $hash, $role])) {
                $success = "✅ Compte créé avec succès. Vous pouvez vous connecter.";
            } else {
                $erreur = "❌ Erreur lors de la création du compte.";
            }
        }
    }
}
?>

<!-- HTML : même style que login.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription | SGA UL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #cbd5e1, #93c5fd);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            padding: 2rem;
            animation: fadeIn .5s ease;
        }
        .btn-primary {
            background-color: #1a73e8;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: scale(0.95);}
            to {opacity: 1; transform: scale(1);}
        }
    </style>
</head>
<body>
<div class="container">
    <div class="col-md-6 mx-auto">
        <div class="card bg-white">
            <div class="text-center mb-3">
                <img src="img/Logo_univ_labe.png" alt="UL" width="60" class="mb-2">
                <h4 class="fw-bold">Création de Compte</h4>
            </div>

            <?php if ($erreur): ?>
                <div class="alert alert-danger"><?= $erreur ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="text" name="nom" class="form-control" id="nom" placeholder="Nom complet" required>
                    <label for="nom"><i class="bi bi-person-fill me-2"></i>Nom complet</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                    <label for="email"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                </div>

                <div class="form-floating mb-3">
                    <select name="role" class="form-select" id="role">
                        <option value="scolarite">Scolarité</option>
                        <option value="admin">Administrateur</option>
                    </select>
                    <label for="role"><i class="bi bi-person-badge-fill me-2"></i>Rôle</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" name="mot_de_passe" class="form-control" id="mdp" placeholder="Mot de passe" required>
                    <label for="mdp"><i class="bi bi-lock-fill me-2"></i>Mot de passe</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" name="confirmer_mot_de_passe" class="form-control" id="cmdp" placeholder="Confirmation" required>
                    <label for="cmdp"><i class="bi bi-lock me-2"></i>Confirmer mot de passe</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Créer le compte</button>
                    
                </div>

                <div class="text-center">
                    <a href="login.php" class="text-decoration-none">J'ai un compte ?</a>
                   
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
