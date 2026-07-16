-- Migration pour la table caisse
CREATE TABLE IF NOT EXISTS caisse (
  id_caisse INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_caisse DATE NOT NULL UNIQUE,
  fond_de_caisse DECIMAL(12,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
