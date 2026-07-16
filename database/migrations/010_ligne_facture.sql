-- Migration pour la table ligne_facture
CREATE TABLE IF NOT EXISTS ligne_facture (
  id_ligne_facture INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_facture INT UNSIGNED NOT NULL,
  id_type_frais INT UNSIGNED NOT NULL,
  montant_ligne DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_ligne_facture_facture FOREIGN KEY (id_facture) REFERENCES facture(id_facture) ON DELETE CASCADE,
  CONSTRAINT fk_ligne_facture_type_frais FOREIGN KEY (id_type_frais) REFERENCES type_frais(id_type_frais) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
