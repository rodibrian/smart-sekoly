-- Migration Module II — Rôles et associations de personnes
CREATE TABLE IF NOT EXISTS role (
  id_role INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(100) NOT NULL UNIQUE COMMENT 'Rôle utilisateur : élève, enseignant, parent, directeur, secrétaire, comptable, surveillant, DRH, caissière…',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS personne_role (
  id_personne INT UNSIGNED NOT NULL,
  id_role INT UNSIGNED NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_personne, id_role),
  CONSTRAINT fk_personne_role_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE,
  CONSTRAINT fk_personne_role_role FOREIGN KEY (id_role) REFERENCES role(id_role) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
