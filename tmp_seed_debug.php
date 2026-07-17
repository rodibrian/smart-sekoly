<?php
require_once __DIR__ . '/config/database.php';
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "NO PDO\n";
    exit(1);
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
try {
    $pdo->exec('TRUNCATE TABLE parametrage_etablissement');
    echo "truncate ok\n";
    $stmt = $pdo->prepare('INSERT INTO parametrage_etablissement (nom_etablissement, logo, monnaie, langue_par_defaut, theme_par_defaut, chemin_stockage_documents) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(['Smart-Sekoly', '', 'MGA', 'fr', 'light', 'documents/']);
    echo "insert ok id=" . $pdo->lastInsertId() . "\n";
} catch (Throwable $e) {
    echo "error: " . $e->getMessage() . "\n";
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
