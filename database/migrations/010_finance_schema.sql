-- Migration: Schéma minimal pour le module Finance
-- Date: 2026-07-16
-- Compatible MySQL (InnoDB)

CREATE TABLE IF NOT EXISTS `factures` (
  `id_facture` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero` VARCHAR(64) NOT NULL UNIQUE,
  `id_eleve` INT UNSIGNED DEFAULT NULL,
  `date_emission` DATE NOT NULL,
  `montant_total` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `statut` VARCHAR(32) NOT NULL DEFAULT 'brouillon',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_facture`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lignes_facture` (
  `id_ligne` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_facture` INT UNSIGNED NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantite` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `prix_unitaire` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `montant` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_ligne`),
  INDEX (`id_facture`),
  CONSTRAINT `fk_lignes_facture_facture` FOREIGN KEY (`id_facture`) REFERENCES `factures`(`id_facture`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `remises` (
  `id_remise` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_remise` VARCHAR(32) NOT NULL,
  `valeur_remise` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `motif` VARCHAR(255) DEFAULT NULL,
  `id_utilisateur_validation` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_remise`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `echeances` (
  `id_echeance` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_facture` INT UNSIGNED NOT NULL,
  `date_echeance` DATE NOT NULL,
  `montant_prevu` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `statut` VARCHAR(32) NOT NULL DEFAULT 'ouvert',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_echeance`),
  INDEX (`id_facture`),
  CONSTRAINT `fk_echeances_facture` FOREIGN KEY (`id_facture`) REFERENCES `factures`(`id_facture`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_echeance` INT UNSIGNED DEFAULT NULL,
  `numero_recu` VARCHAR(64) NOT NULL,
  `date_paiement` DATETIME NOT NULL,
  `montant` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `mode_paiement` VARCHAR(32) NOT NULL DEFAULT 'espece',
  `statut` VARCHAR(32) NOT NULL DEFAULT 'actif',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_paiement`),
  INDEX (`id_echeance`),
  CONSTRAINT `fk_paiements_echeance` FOREIGN KEY (`id_echeance`) REFERENCES `echeances`(`id_echeance`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `caisses` (
  `id_caisse` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_caisse` DATE NOT NULL,
  `fond_de_caisse` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_caisse`),
  UNIQUE KEY `uk_caisse_date` (`date_caisse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mouvements_caisse` (
  `id_mouvement` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_caisse` INT UNSIGNED NOT NULL,
  `type` ENUM('entree','sortie') NOT NULL,
  `montant` DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  `description` VARCHAR(255) DEFAULT NULL,
  `date_mouvement` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mouvement`),
  INDEX (`id_caisse`),
  CONSTRAINT `fk_mouvements_caisse_caisse` FOREIGN KEY (`id_caisse`) REFERENCES `caisses`(`id_caisse`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fin migration
