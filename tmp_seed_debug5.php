<?php
require_once __DIR__ . '/config/database.php';
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "No PDO\n";
    exit(1);
}
function countTable(PDO $pdo, string $table) {
    return (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
}
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "tables count=" . count($tables) . "\n";
foreach (['role', 'annee_scolaire'] as $table) {
    echo "$table before truncate=" . countTable($pdo, $table) . "\n";
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach ($tables as $table) {
    echo "truncating {$table}\n";
    $pdo->exec("TRUNCATE TABLE `{$table}`");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
foreach (['role', 'annee_scolaire'] as $table) {
    echo "$table after truncate=" . countTable($pdo, $table) . "\n";
}
$roles = ['directeur', 'comptable', 'enseignant', 'secretaire', 'surveillant', 'drh', 'caissiere', 'parent'];
$stmtRole = $pdo->prepare('INSERT INTO role (libelle) VALUES (?)');
foreach ($roles as $role) {
    try {
        echo "insert role {$role}\n";
        $stmtRole->execute([$role]);
        echo "inserted {$role} id=" . $pdo->lastInsertId() . "\n";
    } catch (Throwable $e) {
        echo "role error {$role}: " . $e->getMessage() . "\n";
        break;
    }
}
$stmtAnnee = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (?, ?, ?, ?)');
try {
    $stmtAnnee->execute(['2026-2027', '2026-09-01', '2027-06-30', 'active']);
    echo "inserted 2026-2027\n";
    $stmtAnnee->execute(['2025-2026', '2025-09-01', '2026-06-30', 'cloturee']);
    echo "inserted 2025-2026\n";
} catch (Throwable $e) {
    echo "annee error: " . $e->getMessage() . "\n";
}
