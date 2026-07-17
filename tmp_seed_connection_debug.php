<?php
require_once __DIR__ . '/config/database.php';
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "No PDO\n";
    exit(1);
}
$db = $pdo->query('SELECT DATABASE()')->fetchColumn();
echo "database=" . $db . "\n";
$stmt = $pdo->query("SHOW TABLES LIKE 'parametrage_etablissement'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
var_dump($tables);
try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    echo "foreign key checks disabled\n";
    $pdo->exec('TRUNCATE TABLE parametrage_etablissement');
    echo "truncate succeeded\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "foreign key checks enabled\n";
} catch (Throwable $e) {
    echo 'error: ' . $e->getMessage() . "\n";
}
