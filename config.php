<?php
// config.php - Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'cjp_affiche');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour générer le prochain code_id au format #CJP-FODR-0001
function getNextCodeId($pdo) {
    $stmt = $pdo->query("SELECT code_id FROM participants ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch();
    
    if ($last && preg_match('/CJP-FODR-(\d+)/', $last['code_id'], $matches)) {
        $nextNum = intval($matches[1]) + 1;
    } else {
        $nextNum = 1;
    }
    
    return '#CJP-FODR-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
}
/*

<?php
// config.php - Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'u571860992_cjp_affiche');
define('DB_USER', 'u571860992_cjp_affiche');
define('DB_PASS', '12345.MKc');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour générer le prochain code_id au format #CJP-FODR-0001
function getNextCodeId($pdo) {
    $stmt = $pdo->query("SELECT code_id FROM participants ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch();
    
    if ($last && preg_match('/CJP-FODR-(\d+)/', $last['code_id'], $matches)) {
        $nextNum = intval($matches[1]) + 1;
    } else {
        $nextNum = 1;
    }
    
    return '#CJP-FODR-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
}
?>

*/
?>

