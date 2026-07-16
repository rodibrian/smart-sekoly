-- Migration pour la table mouvement_caisse
CREATE TABLE IF NOT EXISTS mouvement_caisse (
  id_mouvement INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_caisse INT UNSIGNED NOT NULL,
  type_mouvement ENUM('entree','sortie') NOT NULL,
  montant DECIMAL(12,2) NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mouvement_caisse_caisse FOREIGN KEY (id_caisse) REFERENCES caisse(id_caisse) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
