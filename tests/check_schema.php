<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();
$stmt = $pdo->query('SHOW COLUMNS FROM parametrage_etablissement');
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Colonnes dans parametrage_etablissement:\n";
foreach ($cols as $c) {
    echo "  - {$c['Field']}\n";
}
