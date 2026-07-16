-- Migration pour la table heure_supplementaire
CREATE TABLE IF NOT EXISTS heure_supplementaire (
  id_heure_sup INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant INT UNSIGNED NOT NULL,
  id_classe INT UNSIGNED NOT NULL,
  id_matiere INT UNSIGNED NOT NULL,
  date_heure DATE NOT NULL,
  nombre_heures DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  taux DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  montant DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  statut ENUM('proposee','validee','payee') NOT NULL DEFAULT 'proposee',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_heure_sup_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT,
  CONSTRAINT fk_heure_sup_classe FOREIGN KEY (id_classe) REFERENCES classe(id_classe) ON DELETE RESTRICT,
  CONSTRAINT fk_heure_sup_matiere FOREIGN KEY (id_matiere) REFERENCES matiere(id_matiere) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
