
<?php
require 'config/db.php';

if (!empty($_POST['departement'])) {
    $stmt = $pdo->prepare("SELECT CodeProg, NomProg FROM programmes WHERE CodeDep = ?");
    $stmt->execute([$_POST['departement']]);
    foreach ($stmt as $row) {
        echo "<option value=\"".htmlspecialchars($row['CodeProg'])."\">".htmlspecialchars($row['NomProg'])."</option>";
    }
}
?>
