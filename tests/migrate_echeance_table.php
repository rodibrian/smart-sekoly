<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

// Créer la table echeance avec statuts CALCULÉS (pas saisis)
$sql_create = "
CREATE TABLE IF NOT EXISTS echeance (
  id_echeance INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_facture INT UNSIGNED NOT NULL,
  numero_ordre TINYINT UNSIGNED NOT NULL COMMENT 'ordre d''apparition (1, 2, 3...)',
  date_prevue DATE NOT NULL COMMENT 'date de règlement prévu',
  montant_prevu DECIMAL(12,2) NOT NULL COMMENT 'montant à payer à cette échéance',
  montant_paye DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'montant effectivement payé (agrégé depuis paiements liés)',
  statut ENUM('a_venir','payee','partielle','en_retard') NOT NULL DEFAULT 'a_venir' COMMENT 'CALCULÉ : ne JAMAIS modifier à la main',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_echeance_facture FOREIGN KEY (id_facture) REFERENCES facture(id_facture) ON DELETE CASCADE,
  UNIQUE KEY uk_echeance_facture_ordre (id_facture, numero_ordre),
  INDEX idx_echeance_statut (statut),
  INDEX idx_echeance_date (date_prevue)
) ENGINE=InnoDB COMMENT='Échéances : répartition du paiement d''une facture sur plusieurs dates (décision #23)';
";

try {
    $pdo->exec($sql_create);
    echo "✓ Table echeance créée\n";
} catch (Throwable $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "  → Table echeance existe déjà\n";
    } else {
        echo "⚠ Erreur : " . $e->getMessage() . "\n";
    }
}

echo "\n✓ Migration echeance complète\n";
