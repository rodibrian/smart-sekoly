-- Migration pour la table contrat
CREATE TABLE IF NOT EXISTS contrat (
  id_contrat INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant INT UNSIGNED NOT NULL,
  type_contrat ENUM('permanent','forfaitaire','horaire','vacataire','stagiaire','benevole') NOT NULL,
  date_debut DATE NOT NULL,
  date_fin DATE NULL,
  salaire DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  statut ENUM('actif','termine','annule') NOT NULL DEFAULT 'actif',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_contrat_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
