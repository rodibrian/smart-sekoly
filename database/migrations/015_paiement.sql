-- Migration pour la table paiement
CREATE TABLE IF NOT EXISTS paiement (
  id_paiement INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_echeance INT UNSIGNED NOT NULL,
  numero_recu VARCHAR(50) NOT NULL UNIQUE COMMENT 'décision #14',
  date_paiement DATETIME NOT NULL,
  montant DECIMAL(12,2) NOT NULL,
  mode_paiement ENUM('espece','banque','mobile_money') NOT NULL COMMENT 'décision #5',
  id_utilisateur_enregistrement INT UNSIGNED NOT NULL,
  id_caisse INT UNSIGNED NOT NULL,
  statut ENUM('actif','annule') NOT NULL DEFAULT 'actif' COMMENT 'suppression logique — décision #26',
  date_annulation DATETIME NULL,
  id_utilisateur_annulation INT UNSIGNED NULL,
  CONSTRAINT fk_paiement_echeance FOREIGN KEY (id_echeance) REFERENCES echeance(id_echeance) ON DELETE RESTRICT,
  CONSTRAINT fk_paiement_utilisateur_enregistrement FOREIGN KEY (id_utilisateur_enregistrement) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT,
  CONSTRAINT fk_paiement_caisse FOREIGN KEY (id_caisse) REFERENCES caisse(id_caisse) ON DELETE RESTRICT,
  CONSTRAINT fk_paiement_utilisateur_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL,
  INDEX idx_paiement_doublon (id_echeance, montant, date_paiement) COMMENT 'accélère la détection de doublon — décision #24'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
