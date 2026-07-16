-- Migration pour la table personnel_administratif
CREATE TABLE IF NOT EXISTS personnel_administratif (
  id_personnel INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne INT UNSIGNED NOT NULL UNIQUE,
  fonction VARCHAR(100) NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_personnel_admin_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
