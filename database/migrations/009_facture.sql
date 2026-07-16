-- Migration pour la table facture
CREATE TABLE IF NOT EXISTS facture (
  id_facture INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve INT UNSIGNED NOT NULL,
  numero_sequentiel VARCHAR(50) NOT NULL UNIQUE COMMENT 'décision #14 — non réutilisable, non modifiable après émission',
  date_emission DATE NOT NULL,
  montant_total DECIMAL(12,2) NOT NULL,
  statut ENUM('active','annulee') NOT NULL DEFAULT 'active',
  date_annulation DATETIME NULL,
  id_utilisateur_annulation INT UNSIGNED NULL,
  CONSTRAINT fk_facture_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE RESTRICT,
  CONSTRAINT fk_facture_utilisateur_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
