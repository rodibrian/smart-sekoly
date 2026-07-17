<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

$sql_commands = [
    "ALTER TABLE echeance ADD COLUMN numero_ordre TINYINT UNSIGNED DEFAULT 1 AFTER id_facture",
    "ALTER TABLE echeance ADD COLUMN montant_paye DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER montant_prevu",
    "ALTER TABLE echeance RENAME COLUMN date_echeance TO date_prevue",
    "ALTER TABLE echeance RENAME COLUMN statut_echeance TO statut",
    "ALTER TABLE echeance MODIFY statut ENUM('a_venir','payee','partielle','en_retard') NOT NULL DEFAULT 'a_venir'",
];

foreach ($sql_commands as $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ Exécuté : $sql\n";
    } catch (Throwable $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false || 
            strpos($e->getMessage(), 'already exists') !== false ||
            strpos($e->getMessage(), 'Syntax error') !== false) {
            echo "  → Colonne/modification existe déjà ou invalide\n";
        } else {
            echo "⚠ Erreur : " . $e->getMessage() . "\n";
        }
    }
}

echo "\n✓ Migration echeance (colonnes additionnelles) complète\n";
