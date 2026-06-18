<?php
session_start();
require_once 'config/db.php'; // fichier contenant $pdo

if (isset($_GET['code'])) {
    $codeFac = $_GET['code'];

    try {
        // Commencer transaction
        $pdo->beginTransaction();

        // ⚠️ Supprimer d'abord les départements et les licences associées
        // Si tu as mis ON DELETE CASCADE sur les foreign keys, tu peux directement supprimer la faculté
        // Sinon, il faut supprimer manuellement
        // Exemple: supprimer d'abord dans departements, licences...

        // Supprimer la faculté
        $stmt = $pdo->prepare("DELETE FROM facultes WHERE CodeFac = ?");
        $stmt->execute([$codeFac]);

        $pdo->commit();

        $_SESSION['success'] = "Faculté supprimée avec succès.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Code faculté manquant.";
}

// Rediriger vers la liste
header('Location: liste_facultes.php');
exit;
?>
