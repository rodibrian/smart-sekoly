<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

$sql_commands = [
    "ALTER TABLE remise MODIFY id_utilisateur_validation INT UNSIGNED NULL",
];

foreach ($sql_commands as $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ Exécuté : $sql\n";
    } catch (Throwable $e) {
        echo "⚠ Erreur : " . $e->getMessage() . "\n";
    }
}

echo "\n✓ Migration remise (modification colonnes) complète\n";
