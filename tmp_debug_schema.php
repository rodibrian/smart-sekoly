<?php
require_once __DIR__ . '/config/database.php';
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "No PDO\n";
    exit(1);
}
$sql = file_get_contents(__DIR__ . '/database/schema_smart_sekoly.sql');
$sql = str_replace(["\r\n", "\r"], "\n", $sql);
$stmts = preg_split('/;\s*\n+/', $sql);
echo "stmt count: " . count($stmts) . "\n";
foreach ($stmts as $idx => $stmt) {
    if (strpos($stmt, 'parametrage_etablissement') !== false) {
        echo "found stmt " . ($idx + 1) . "\n";
        echo substr(trim($stmt), 0, 500) . "\n";
    }
}
$stmt = $pdo->query('SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ' . $pdo->quote('smart_sekoly') . ' AND TABLE_NAME = ' . $pdo->quote('parametrage_etablissement'));
var_dump($stmt->fetchAll(PDO::FETCH_COLUMN));
