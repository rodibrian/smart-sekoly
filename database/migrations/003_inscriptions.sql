-- Migration Module II - inscriptions
CREATE TABLE IF NOT EXISTS inscription (
  id_inscription INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve INT UNSIGNED NOT NULL,
  id_classe INT UNSIGNED NULL,
  id_annee INT UNSIGNED NULL,
  date_inscription DATE NOT NULL,
  statut_inscription ENUM('actif','redoublant','transfere','diplome','annule') NOT NULL DEFAULT 'actif',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  date_annulation DATETIME NULL,
  id_utilisateur_annulation INT UNSIGNED NULL,
  CONSTRAINT fk_inscription_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE CASCADE,
  CONSTRAINT fk_inscription_classe FOREIGN KEY (id_classe) REFERENCES classe(id_classe) ON DELETE SET NULL,
  CONSTRAINT fk_inscription_annee FOREIGN KEY (id_annee) REFERENCES annee_scolaire(id_annee) ON DELETE SET NULL,
  INDEX idx_inscription_statut (statut_inscription),
  INDEX idx_inscription_date (date_inscription)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
