<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

$sql_commands = [
    "ALTER TABLE remise ADD COLUMN statut ENUM('attente','approuvee','rejetee') NOT NULL DEFAULT 'attente' AFTER motif",
    "ALTER TABLE remise ADD COLUMN id_createur INT UNSIGNED NOT NULL AFTER statut",
    "ALTER TABLE remise ADD COLUMN date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER id_createur",
    "ALTER TABLE remise ADD COLUMN date_validation DATETIME NULL AFTER id_utilisateur_validation",
    "ALTER TABLE remise ADD CONSTRAINT fk_remise_createur FOREIGN KEY (id_createur) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT",
];

foreach ($sql_commands as $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ Exécuté : $sql\n";
    } catch (Throwable $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'already exists') !== false) {
            echo "  → Colonne/contrainte existe déjà\n";
        } else {
            echo "⚠ Erreur : " . $e->getMessage() . "\n";
        }
    }
}

echo "\n✓ Migration remise complète\n";
