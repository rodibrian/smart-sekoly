-- Migration pour la table echeance
CREATE TABLE IF NOT EXISTS echeance (
  id_echeance INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_facture INT UNSIGNED NOT NULL,
  date_echeance DATE NOT NULL,
  montant_prevu DECIMAL(12,2) NOT NULL,
  statut_echeance ENUM('payee','partielle','en_retard') NOT NULL DEFAULT 'en_retard',
  CONSTRAINT fk_echeance_facture FOREIGN KEY (id_facture) REFERENCES facture(id_facture) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Décision #23';
