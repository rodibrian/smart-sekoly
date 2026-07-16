-- Migration pour la table remise
CREATE TABLE IF NOT EXISTS remise (
  id_remise INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type_remise ENUM('pourcentage','montant_fixe') NOT NULL,
  valeur_remise DECIMAL(12,2) NOT NULL,
  motif VARCHAR(255) NOT NULL,
  id_utilisateur_validation INT UNSIGNED NOT NULL,
  CONSTRAINT fk_remise_utilisateur_validation FOREIGN KEY (id_utilisateur_validation) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Décision #13';
