-- Migration pour la table affectation
CREATE TABLE IF NOT EXISTS affectation (
  id_affectation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant INT UNSIGNED NOT NULL,
  id_matiere INT UNSIGNED NOT NULL,
  id_classe INT UNSIGNED NOT NULL,
  id_annee INT UNSIGNED NOT NULL,
  date_affectation DATE NOT NULL,
  statut ENUM('active','terminee','reaffectee') NOT NULL DEFAULT 'active',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_affectation_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT,
  CONSTRAINT fk_affectation_matiere FOREIGN KEY (id_matiere) REFERENCES matiere(id_matiere) ON DELETE RESTRICT,
  CONSTRAINT fk_affectation_classe FOREIGN KEY (id_classe) REFERENCES classe(id_classe) ON DELETE RESTRICT,
  CONSTRAINT fk_affectation_annee FOREIGN KEY (id_annee) REFERENCES annee_scolaire(id_annee) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
