<?php
require_once __DIR__ . '/config/database.php';
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "No PDO\n";
    exit(1);
}
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "SHOW TABLES count=" . count($tables) . "\n";
foreach ($tables as $table) {
    echo "table: {$table}\n";
}
try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    echo "SET FOREIGN_KEY_CHECKS = 0\n";
    foreach ($tables as $table) {
        echo "truncating {$table}\n";
        $pdo->exec("TRUNCATE TABLE `{$table}`");
        $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        echo "count after truncate {$table} = {$count}\n";
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "SET FOREIGN_KEY_CHECKS = 1\n";
    $count = $pdo->query("SELECT COUNT(*) FROM annee_scolaire")->fetchColumn();
    echo "annee_scolaire count after truncate = {$count}\n";
    $pdo->exec("INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES ('2026-2027','2026-09-01','2027-06-30','active')");
    echo "insert succeeded\n";
} catch (Throwable $e) {
    echo "error: " . $e->getMessage() . "\n";
}
