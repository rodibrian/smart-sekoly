-- Migration pour la table facture_remise
CREATE TABLE IF NOT EXISTS facture_remise (
  id_facture INT UNSIGNED NOT NULL,
  id_remise INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_facture, id_remise),
  CONSTRAINT fk_facture_remise_facture FOREIGN KEY (id_facture) REFERENCES facture(id_facture) ON DELETE CASCADE,
  CONSTRAINT fk_facture_remise_remise FOREIGN KEY (id_remise) REFERENCES remise(id_remise) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
