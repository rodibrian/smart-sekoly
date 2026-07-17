<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

$sql_commands = [
    "ALTER TABLE parametrage_etablissement ADD COLUMN format_matricule VARCHAR(100) NOT NULL DEFAULT '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}'",
    "ALTER TABLE parametrage_etablissement ADD COLUMN prefixe_matricule VARCHAR(10) NOT NULL DEFAULT 'ELV'",
    "ALTER TABLE parametrage_etablissement ADD COLUMN annee_courante VARCHAR(20) NULL DEFAULT '2025-2026'",
];

foreach ($sql_commands as $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ Exécuté : $sql\n";
    } catch (Throwable $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "  → Colonne existe déjà\n";
        } else {
            echo "⚠ Erreur : " . $e->getMessage() . "\n";
        }
    }
}

echo "\n✓ Migration complète\n";
