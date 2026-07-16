-- Migration Module II - transferts
CREATE TABLE IF NOT EXISTS transfert (
  id_transfert INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_inscription INT UNSIGNED NOT NULL,
  motif VARCHAR(255) NOT NULL,
  date_transfert DATE NOT NULL,
  etablissement_origine_destination VARCHAR(255) NULL,
  statut ENUM('en_cours','valide','rejetee') NOT NULL DEFAULT 'en_cours',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_transfert_inscription FOREIGN KEY (id_inscription) REFERENCES inscription(id_inscription) ON DELETE CASCADE,
  INDEX idx_transfert_date (date_transfert),
  INDEX idx_transfert_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
