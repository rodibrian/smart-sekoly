-- Migration initiale pour le paramétrage de l'établissement
CREATE TABLE IF NOT EXISTS parametrage_etablissement (
  id_parametrage INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom_etablissement VARCHAR(255) NOT NULL,
  logo VARCHAR(255) NULL,
  monnaie VARCHAR(10) NOT NULL DEFAULT 'MGA',
  langue_par_defaut VARCHAR(10) NOT NULL DEFAULT 'fr',
  theme_par_defaut VARCHAR(20) NOT NULL DEFAULT 'clair',
  chemin_stockage_documents VARCHAR(255) NOT NULL DEFAULT 'documents',
  format_matricule VARCHAR(100) NOT NULL DEFAULT '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}',
  prefixe_matricule VARCHAR(20) NOT NULL DEFAULT 'EL',
  annee_courante YEAR NOT NULL,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
