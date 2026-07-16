-- Migration pour la table salaire
CREATE TABLE IF NOT EXISTS salaire (
  id_salaire INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant INT UNSIGNED NOT NULL,
  periode VARCHAR(50) NOT NULL COMMENT 'ex. 2026-09',
  montant_brut DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  montant_net DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  retenues DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  statut ENUM('en_attente','valide','paye') NOT NULL DEFAULT 'en_attente',
  date_paiement DATE NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_salaire_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
