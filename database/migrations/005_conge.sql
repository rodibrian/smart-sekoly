-- Migration pour la table conge
CREATE TABLE IF NOT EXISTS conge (
  id_conge INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant INT UNSIGNED NOT NULL,
  type_conge ENUM('maladie','personnel','formation','conge_sans_solde','maternite','paternite') NOT NULL,
  date_debut DATE NOT NULL,
  date_fin DATE NOT NULL,
  statut ENUM('demande','accepte','refuse','termine') NOT NULL DEFAULT 'demande',
  raison TEXT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_conge_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
