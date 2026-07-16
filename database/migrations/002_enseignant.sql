-- Migration pour la table enseignant
CREATE TABLE IF NOT EXISTS enseignant (
  id_enseignant INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne INT UNSIGNED NOT NULL UNIQUE,
  matricule VARCHAR(50) NOT NULL UNIQUE,
  date_embauche DATE NOT NULL,
  statut_enseignant ENUM('actif','en_conge','sorti') NOT NULL DEFAULT 'actif',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_enseignant_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
