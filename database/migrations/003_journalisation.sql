-- Migration pour les journaux d'audit et de connexion
CREATE TABLE IF NOT EXISTS journal_audit (
  id_audit BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT UNSIGNED NOT NULL,
  date_action DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type_action VARCHAR(100) NOT NULL COMMENT 'ex. suppression_logique, modification, validation',
  table_concernee VARCHAR(100) NOT NULL COMMENT 'table métier concernée par l\'action',
  id_enregistrement_concerne INT UNSIGNED NULL COMMENT 'PK de l\'enregistrement concerné dans la table ci-dessus',
  ancienne_valeur JSON NULL,
  nouvelle_valeur JSON NULL,
  CONSTRAINT fk_journal_audit_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT,
  INDEX idx_journal_audit_table (table_concernee, id_enregistrement_concerne)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS journal_connexion (
  id_connexion BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT UNSIGNED NOT NULL,
  date_connexion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  adresse_ip VARCHAR(45) NOT NULL,
  navigateur VARCHAR(255) NULL,
  CONSTRAINT fk_journal_connexion_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
