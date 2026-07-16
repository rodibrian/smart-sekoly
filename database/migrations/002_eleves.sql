-- Migration Module II - élèves et personnes
CREATE TABLE IF NOT EXISTS personne (
  id_personne INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  date_naissance DATE NULL,
  sexe ENUM('M','F') NULL,
  telephone VARCHAR(30) NULL,
  email VARCHAR(255) NULL,
  adresse TEXT NULL,
  piece_identite VARCHAR(100) NULL,
  photo VARCHAR(255) NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_personne_nom_prenom (nom, prenom),
  INDEX idx_personne_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS eleve (
  id_eleve INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne INT UNSIGNED NOT NULL UNIQUE,
  matricule VARCHAR(50) NOT NULL UNIQUE,
  date_entree DATE NOT NULL,
  statut_scolaire ENUM('actif','ancien','transfere','diplome') NOT NULL DEFAULT 'actif',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_eleve_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
