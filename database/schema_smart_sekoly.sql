-- =====================================================================
-- SMART-SEKOLY — SCHÉMA PHYSIQUE MYSQL (MPD)
-- Dérivé du MCD_Smart-Sekoly.md (méthode Merise, 67 entités, 12 domaines)
-- Développeur : Baia Creative Solutions — Juillet 2026
--
-- Conventions retenues :
--   - snake_case, nomenclature française (conforme AGENTS.md / section 2.1 du CDC)
--   - Moteur InnoDB, charset utf8mb4 / collation utf8mb4_unicode_ci
--   - Toute table métier porte date_creation / date_modification
--   - Suppression logique (décision #26) : colonne `statut` incluant une
--     valeur 'annule' + date_annulation + id_utilisateur_annulation sur
--     les tables sensibles (note, paiement, inscription, facture)
--   - Aucune valeur métier codée en dur : les ENUM ci-dessous reprennent
--     les libellés du MCD ; si un cycle de vie doit évoluer sans toucher
--     au code, préférez une table de paramétrage (cf. PARAMETRAGE_ETABLISSEMENT)
--     plutôt que d'étendre un ENUM — à arbitrer module par module.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS smart_sekoly
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_sekoly;

-- =====================================================================
-- DOMAINE A.1 — PERSONNES, RÔLES & SÉCURITÉ
-- =====================================================================
CREATE TABLE personne (
  id_personne       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom               VARCHAR(100)    NOT NULL,
  prenom            VARCHAR(100)    NOT NULL,
  date_naissance    DATE            NULL,
  sexe              ENUM('M','F')   NULL,
  telephone         VARCHAR(30)     NULL,
  email             VARCHAR(255)    NULL COMMENT 'Adresse email de la personne',
  adresse           TEXT            NULL COMMENT 'Adresse postale complète',
  piece_identite    VARCHAR(100)    NULL COMMENT 'Numéro de CIN, passeport, ou autre pièce d\'identité',
  photo             VARCHAR(255)    NULL COMMENT 'Chemin fichier',
  date_creation     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_personne_nom_prenom (nom, prenom),
  INDEX idx_personne_email (email) COMMENT 'Pour la recherche et l\'unicité partielle'
) ENGINE=InnoDB;

CREATE TABLE role (
  id_role   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle   VARCHAR(100) NOT NULL UNIQUE COMMENT 'élève, enseignant, parent, directeur, secrétaire, comptable, surveillant, DRH, caissière…'
) ENGINE=InnoDB;

-- Association porteuse EST_UN (cumul de rôles) : PERSONNE (0,n) <-> ROLE (0,n)
CREATE TABLE personne_role (
  id_personne INT UNSIGNED NOT NULL,
  id_role     INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_personne, id_role),
  CONSTRAINT fk_personne_role_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE CASCADE,
  CONSTRAINT fk_personne_role_role     FOREIGN KEY (id_role)     REFERENCES role(id_role)         ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Spécialisation exclusive PERSONNE -> ELEVE (0,1)
CREATE TABLE eleve (
  id_eleve          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne       INT UNSIGNED NOT NULL UNIQUE,
  matricule         VARCHAR(50)  NOT NULL UNIQUE COMMENT 'Format paramétrable, décision #16',
  date_entree       DATE         NOT NULL,
  statut_scolaire   ENUM('actif','ancien','transfere','diplome') NOT NULL DEFAULT 'actif',
  date_creation     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_eleve_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Spécialisation exclusive PERSONNE -> ENSEIGNANT (0,1)
CREATE TABLE enseignant (
  id_enseignant     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne       INT UNSIGNED NOT NULL UNIQUE,
  matricule         VARCHAR(50)  NOT NULL UNIQUE,
  date_embauche     DATE         NOT NULL,
  statut_enseignant ENUM('actif','en_conge','sorti') NOT NULL DEFAULT 'actif',
  date_creation     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_enseignant_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Spécialisation exclusive PERSONNE -> PERSONNEL_ADMINISTRATIF (0,1)
CREATE TABLE personnel_administratif (
  id_personnel      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne       INT UNSIGNED NOT NULL UNIQUE,
  fonction          VARCHAR(100) NOT NULL,
  date_creation     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_personnel_admin_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- PERSONNE (0,1) --POSSEDE--> (1,1) UTILISATEUR
CREATE TABLE utilisateur (
  id_utilisateur         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne            INT UNSIGNED NOT NULL UNIQUE,
  identifiant            VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe_hash      VARCHAR(255) NOT NULL,
  date_derniere_connexion DATETIME    NULL,
  statut_compte          ENUM('actif','verrouille') NOT NULL DEFAULT 'actif',
  doit_changer_mdp       TINYINT(1)   NOT NULL DEFAULT 1 COMMENT 'décision #19 : changement obligatoire à la première connexion',
  nombre_essais_echoues  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  date_creation          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_utilisateur_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE permission (
  id_permission INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  module        VARCHAR(100) NOT NULL,
  sous_module   VARCHAR(100) NULL,
  action        ENUM('creer','lire','modifier','supprimer','exporter','valider') NOT NULL,
  UNIQUE KEY uk_permission (module, sous_module, action)
) ENGINE=InnoDB;

-- ROLE (1,n) --ATTRIBUE--> (0,n) PERMISSION
CREATE TABLE role_permission (
  id_role       INT UNSIGNED NOT NULL,
  id_permission INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_role, id_permission),
  CONSTRAINT fk_role_permission_role       FOREIGN KEY (id_role)       REFERENCES role(id_role)             ON DELETE CASCADE,
  CONSTRAINT fk_role_permission_permission FOREIGN KEY (id_permission) REFERENCES permission(id_permission) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE acces_parent_eleve (
  id_acces        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve        INT UNSIGNED NOT NULL,
  code_acces      VARCHAR(50)  NOT NULL UNIQUE COMMENT 'décision #22',
  date_generation DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  statut          ENUM('actif','revoque') NOT NULL DEFAULT 'actif',
  CONSTRAINT fk_acces_parent_eleve_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE journal_audit (
  id_audit               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur         INT UNSIGNED NOT NULL,
  date_action             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type_action             VARCHAR(100) NOT NULL COMMENT 'ex. suppression_logique, modification, validation',
  table_concernee         VARCHAR(100) NOT NULL COMMENT 'table métier concernée par l''action',
  id_enregistrement_concerne INT UNSIGNED NULL COMMENT 'PK de l''enregistrement concerné dans la table ci-dessus',
  ancienne_valeur         JSON         NULL,
  nouvelle_valeur         JSON         NULL,
  CONSTRAINT fk_journal_audit_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT,
  INDEX idx_journal_audit_table (table_concernee, id_enregistrement_concerne)
) ENGINE=InnoDB COMMENT='Journal transverse (décision #26) — pointeur générique volontairement non contraint par FK stricte, car il référence n''importe quelle table métier.';

CREATE TABLE journal_connexion (
  id_connexion    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur  INT UNSIGNED NOT NULL,
  date_connexion  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  adresse_ip      VARCHAR(45)  NOT NULL,
  navigateur      VARCHAR(255) NULL,
  CONSTRAINT fk_journal_connexion_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.2 — CADRE TEMPOREL & STRUCTURE PÉDAGOGIQUE
-- =====================================================================

CREATE TABLE annee_scolaire (
  id_annee      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle       VARCHAR(20)  NOT NULL UNIQUE COMMENT 'ex. 2026-2027',
  date_debut    DATE         NOT NULL,
  date_fin      DATE         NOT NULL,
  etat          ENUM('preparation','active','cloturee','archivee') NOT NULL DEFAULT 'preparation'
) ENGINE=InnoDB;

CREATE TABLE jour_calendrier (
  id_jour_calendrier INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_annee            INT UNSIGNED NOT NULL,
  date_jour            DATE         NOT NULL,
  type_jour            ENUM('ferie','vacances','journee_pedagogique') NOT NULL,
  CONSTRAINT fk_jour_calendrier_annee FOREIGN KEY (id_annee) REFERENCES annee_scolaire(id_annee) ON DELETE CASCADE,
  UNIQUE KEY uk_jour_calendrier (id_annee, date_jour)
) ENGINE=InnoDB;

CREATE TABLE cycle (
  id_cycle INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle  VARCHAR(50) NOT NULL UNIQUE COMMENT 'Primaire, Collège, Lycée'
) ENGINE=InnoDB;

CREATE TABLE niveau (
  id_niveau INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_cycle  INT UNSIGNED NOT NULL,
  libelle   VARCHAR(50)  NOT NULL COMMENT 'ex. CM2, 6ème, Terminale',
  CONSTRAINT fk_niveau_cycle FOREIGN KEY (id_cycle) REFERENCES cycle(id_cycle) ON DELETE RESTRICT,
  UNIQUE KEY uk_niveau (id_cycle, libelle)
) ENGINE=InnoDB;

CREATE TABLE serie (
  id_serie INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle  VARCHAR(50) NOT NULL UNIQUE COMMENT 'ex. OSE (facultatif, lycée)'
) ENGINE=InnoDB;

CREATE TABLE classe (
  id_classe     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_niveau     INT UNSIGNED NOT NULL,
  id_serie      INT UNSIGNED NULL,
  libelle       VARCHAR(50)  NOT NULL COMMENT 'jamais un texte libre côté saisie élève — section 7',
  effectif_max  SMALLINT UNSIGNED NOT NULL,
  CONSTRAINT fk_classe_niveau FOREIGN KEY (id_niveau) REFERENCES niveau(id_niveau) ON DELETE RESTRICT,
  CONSTRAINT fk_classe_serie  FOREIGN KEY (id_serie)  REFERENCES serie(id_serie)   ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE salle (
  id_salle  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle   VARCHAR(50) NOT NULL UNIQUE,
  capacite  SMALLINT UNSIGNED NOT NULL COMMENT 'décision #17'
) ENGINE=InnoDB;

CREATE TABLE matiere (
  id_matiere INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle    VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE programme (
  id_programme     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_classe        INT UNSIGNED NOT NULL,
  id_matiere       INT UNSIGNED NOT NULL,
  id_annee         INT UNSIGNED NOT NULL COMMENT 'section 7.1 : matière rattachée à une classe pour une année',
  coefficient      DECIMAL(4,2) NOT NULL,
  volume_horaire   SMALLINT UNSIGNED NOT NULL,
  est_obligatoire  TINYINT(1)   NOT NULL DEFAULT 1,
  CONSTRAINT fk_programme_classe  FOREIGN KEY (id_classe)  REFERENCES classe(id_classe)          ON DELETE CASCADE,
  CONSTRAINT fk_programme_matiere FOREIGN KEY (id_matiere) REFERENCES matiere(id_matiere)         ON DELETE RESTRICT,
  CONSTRAINT fk_programme_annee   FOREIGN KEY (id_annee)   REFERENCES annee_scolaire(id_annee)    ON DELETE RESTRICT,
  UNIQUE KEY uk_programme (id_classe, id_matiere, id_annee)
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.3 — SCOLARITÉ DE L'ÉLÈVE
-- =====================================================================

CREATE TABLE inscription (
  id_inscription        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve              INT UNSIGNED NOT NULL,
  id_classe             INT UNSIGNED NOT NULL,
  id_annee              INT UNSIGNED NOT NULL,
  date_inscription      DATE         NOT NULL,
  statut_inscription    ENUM('actif','redoublant','transfere','diplome','annule') NOT NULL DEFAULT 'actif',
  date_annulation        DATETIME     NULL COMMENT 'suppression logique — décision #26',
  id_utilisateur_annulation INT UNSIGNED NULL,
  date_creation          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_inscription_eleve  FOREIGN KEY (id_eleve)  REFERENCES eleve(id_eleve)          ON DELETE RESTRICT,
  CONSTRAINT fk_inscription_classe FOREIGN KEY (id_classe) REFERENCES classe(id_classe)        ON DELETE RESTRICT,
  CONSTRAINT fk_inscription_annee  FOREIGN KEY (id_annee)  REFERENCES annee_scolaire(id_annee)  ON DELETE RESTRICT,
  CONSTRAINT fk_inscription_utilisateur_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL,
  UNIQUE KEY uk_inscription (id_eleve, id_annee)
) ENGINE=InnoDB;

CREATE TABLE transfert (
  id_transfert                       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_inscription                     INT UNSIGNED NOT NULL UNIQUE,
  motif                              VARCHAR(255) NOT NULL,
  date_transfert                     DATE         NOT NULL,
  etablissement_origine_destination  VARCHAR(255) NULL COMMENT 'texte libre — décision #25',
  CONSTRAINT fk_transfert_inscription FOREIGN KEY (id_inscription) REFERENCES inscription(id_inscription) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.4 — ENSEIGNANTS & RESSOURCES HUMAINES
-- =====================================================================

CREATE TABLE contrat (
  id_contrat              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant            INT UNSIGNED NOT NULL,
  type_contrat             ENUM('permanent','forfaitaire','horaire','vacataire','stagiaire','benevole') NOT NULL,
  date_debut               DATE         NOT NULL,
  date_fin                 DATE         NULL,
  montant_ou_taux_horaire  DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_contrat_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE affectation (
  id_affectation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant  INT UNSIGNED NOT NULL,
  id_matiere     INT UNSIGNED NOT NULL,
  id_classe      INT UNSIGNED NOT NULL,
  id_annee       INT UNSIGNED NOT NULL,
  date_debut     DATE NOT NULL,
  date_fin       DATE NULL,
  CONSTRAINT fk_affectation_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant)   ON DELETE RESTRICT,
  CONSTRAINT fk_affectation_matiere    FOREIGN KEY (id_matiere)    REFERENCES matiere(id_matiere)         ON DELETE RESTRICT,
  CONSTRAINT fk_affectation_classe     FOREIGN KEY (id_classe)     REFERENCES classe(id_classe)           ON DELETE RESTRICT,
  CONSTRAINT fk_affectation_annee      FOREIGN KEY (id_annee)      REFERENCES annee_scolaire(id_annee)    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- CONGE rattaché à PERSONNE (validé : enseignant + personnel administratif)
CREATE TABLE conge (
  id_conge     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne  INT UNSIGNED NOT NULL,
  type_conge   ENUM('paye','maladie','formation') NOT NULL,
  date_debut   DATE NOT NULL,
  date_fin     DATE NOT NULL,
  statut       ENUM('demande','valide','refuse') NOT NULL DEFAULT 'demande',
  CONSTRAINT fk_conge_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE heure_supplementaire (
  id_heure_supp  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant  INT UNSIGNED NOT NULL,
  date_heure_supp DATE NOT NULL,
  nombre_heures  DECIMAL(4,2) NOT NULL,
  statut         ENUM('proposee','validee','payee') NOT NULL DEFAULT 'proposee',
  CONSTRAINT fk_heure_supp_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE salaire (
  id_salaire       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_enseignant    INT UNSIGNED NOT NULL,
  periode          VARCHAR(20)  NOT NULL COMMENT 'ex. 2026-07',
  montant_calcule  DECIMAL(12,2) NOT NULL,
  date_paiement    DATE NULL,
  CONSTRAINT fk_salaire_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant) ON DELETE RESTRICT,
  UNIQUE KEY uk_salaire (id_enseignant, periode)
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.5 — EMPLOI DU TEMPS
-- =====================================================================

CREATE TABLE creneau_horaire (
  id_creneau   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  jour_semaine ENUM('lundi','mardi','mercredi','jeudi','vendredi','samedi') NOT NULL,
  heure_debut  TIME NOT NULL,
  heure_fin    TIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE emploi_du_temps (
  id_emploi_du_temps INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_classe          INT UNSIGNED NOT NULL,
  id_enseignant      INT UNSIGNED NOT NULL,
  id_matiere         INT UNSIGNED NOT NULL,
  id_salle            INT UNSIGNED NOT NULL,
  id_creneau          INT UNSIGNED NOT NULL,
  CONSTRAINT fk_edt_classe     FOREIGN KEY (id_classe)     REFERENCES classe(id_classe)             ON DELETE RESTRICT,
  CONSTRAINT fk_edt_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant)     ON DELETE RESTRICT,
  CONSTRAINT fk_edt_matiere    FOREIGN KEY (id_matiere)    REFERENCES matiere(id_matiere)           ON DELETE RESTRICT,
  CONSTRAINT fk_edt_salle      FOREIGN KEY (id_salle)      REFERENCES salle(id_salle)               ON DELETE RESTRICT,
  CONSTRAINT fk_edt_creneau    FOREIGN KEY (id_creneau)    REFERENCES creneau_horaire(id_creneau)   ON DELETE RESTRICT,
  UNIQUE KEY uk_edt_salle_creneau (id_salle, id_creneau) COMMENT 'évite les doubles réservations — décision #17'
) ENGINE=InnoDB;

CREATE TABLE seance_reelle (
  id_seance                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_emploi_du_temps       INT UNSIGNED NOT NULL,
  date_seance               DATE NOT NULL,
  statut                    ENUM('prevu','realise','annule','reporte','remplace') NOT NULL DEFAULT 'prevu',
  id_enseignant_remplacant  INT UNSIGNED NULL COMMENT 'décision #18 — remplacement ponctuel',
  CONSTRAINT fk_seance_edt          FOREIGN KEY (id_emploi_du_temps)      REFERENCES emploi_du_temps(id_emploi_du_temps) ON DELETE RESTRICT,
  CONSTRAINT fk_seance_remplacant   FOREIGN KEY (id_enseignant_remplacant) REFERENCES enseignant(id_enseignant)          ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE agenda_evenement (
  id_evenement_agenda INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre               VARCHAR(150) NOT NULL,
  date_heure          DATETIME     NOT NULL,
  lieu                VARCHAR(150) NULL,
  public_concerne     VARCHAR(150) NULL,
  id_classe           INT UNSIGNED NULL COMMENT 'décision #31',
  CONSTRAINT fk_agenda_classe FOREIGN KEY (id_classe) REFERENCES classe(id_classe) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE planning_surveillant (
  id_planning_surveillant INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur           INT UNSIGNED NOT NULL,
  date_heure               DATETIME     NOT NULL,
  type_surveillance        ENUM('recreation','etude','permanence') NOT NULL,
  CONSTRAINT fk_planning_surveillant_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='décision #50';

-- =====================================================================
-- DOMAINE A.6 — ÉVALUATIONS & RÉSULTATS
-- =====================================================================

CREATE TABLE periode (
  id_periode    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_annee      INT UNSIGNED NOT NULL,
  libelle       VARCHAR(50)  NOT NULL,
  type_periode  ENUM('trimestre','semestre','bimestre') NOT NULL COMMENT 'section 10.2 — paramétrable',
  CONSTRAINT fk_periode_annee FOREIGN KEY (id_annee) REFERENCES annee_scolaire(id_annee) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE evaluation (
  id_evaluation  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_matiere     INT UNSIGNED NOT NULL,
  id_classe      INT UNSIGNED NOT NULL,
  id_periode     INT UNSIGNED NOT NULL,
  id_enseignant  INT UNSIGNED NOT NULL,
  date_evaluation DATE NOT NULL,
  coefficient    DECIMAL(4,2) NOT NULL,
  CONSTRAINT fk_evaluation_matiere    FOREIGN KEY (id_matiere)    REFERENCES matiere(id_matiere)         ON DELETE RESTRICT,
  CONSTRAINT fk_evaluation_classe     FOREIGN KEY (id_classe)     REFERENCES classe(id_classe)           ON DELETE RESTRICT,
  CONSTRAINT fk_evaluation_periode    FOREIGN KEY (id_periode)    REFERENCES periode(id_periode)         ON DELETE RESTRICT,
  CONSTRAINT fk_evaluation_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant)   ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE note (
  id_note                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve                   INT UNSIGNED NOT NULL,
  id_evaluation               INT UNSIGNED NOT NULL,
  valeur                      DECIMAL(5,2) NULL COMMENT 'nullable : le primaire peut noter par appréciation qualitative — décision #1',
  appreciation                VARCHAR(255) NULL,
  statut                      ENUM('actif','annule') NOT NULL DEFAULT 'actif' COMMENT 'suppression logique — décision #26',
  date_annulation              DATETIME NULL,
  id_utilisateur_annulation   INT UNSIGNED NULL,
  date_creation                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_modification            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_note_eleve      FOREIGN KEY (id_eleve)      REFERENCES eleve(id_eleve)           ON DELETE RESTRICT,
  CONSTRAINT fk_note_evaluation FOREIGN KEY (id_evaluation) REFERENCES evaluation(id_evaluation) ON DELETE RESTRICT,
  CONSTRAINT fk_note_utilisateur_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL,
  UNIQUE KEY uk_note (id_eleve, id_evaluation)
) ENGINE=InnoDB;

CREATE TABLE bulletin (
  id_bulletin        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve           INT UNSIGNED NOT NULL,
  id_periode         INT UNSIGNED NOT NULL,
  id_inscription     INT UNSIGNED NOT NULL,
  moyenne_generale   DECIMAL(5,2) NULL,
  rang               SMALLINT UNSIGNED NULL,
  decision            ENUM('admis','redoublement','transfert') NULL,
  date_creation       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bulletin_eleve       FOREIGN KEY (id_eleve)       REFERENCES eleve(id_eleve)             ON DELETE RESTRICT,
  CONSTRAINT fk_bulletin_periode     FOREIGN KEY (id_periode)     REFERENCES periode(id_periode)         ON DELETE RESTRICT,
  CONSTRAINT fk_bulletin_inscription FOREIGN KEY (id_inscription) REFERENCES inscription(id_inscription) ON DELETE RESTRICT,
  UNIQUE KEY uk_bulletin (id_eleve, id_periode)
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.7 — VIE SCOLAIRE & DISCIPLINE
-- =====================================================================

CREATE TABLE absence (
  id_absence  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve    INT UNSIGNED NOT NULL,
  date_absence DATE NOT NULL,
  justifiee   TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_absence_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE retard (
  id_retard      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve       INT UNSIGNED NOT NULL,
  date_retard    DATE NOT NULL,
  duree_minutes  SMALLINT UNSIGNED NOT NULL,
  CONSTRAINT fk_retard_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE sanction (
  id_sanction              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve                 INT UNSIGNED NOT NULL,
  id_utilisateur_validation INT UNSIGNED NOT NULL,
  type_sanction             VARCHAR(100) NOT NULL COMMENT 'paramétrable — décision #12 : avertissement, retenue, exclusion temporaire, exclusion définitive',
  niveau_gravite            TINYINT UNSIGNED NOT NULL,
  motif                     VARCHAR(255) NOT NULL,
  statut                    ENUM('proposee','validee') NOT NULL DEFAULT 'proposee',
  date_creation              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sanction_eleve      FOREIGN KEY (id_eleve)                 REFERENCES eleve(id_eleve)             ON DELETE RESTRICT,
  CONSTRAINT fk_sanction_validation FOREIGN KEY (id_utilisateur_validation) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE incident (
  id_incident  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_incident DATE NOT NULL,
  description   TEXT NULL,
  photos        VARCHAR(255) NULL COMMENT 'chemin fichier(s)',
  temoins       VARCHAR(255) NULL
) ENGINE=InnoDB;

-- INCIDENT <-> ELEVE : association plusieurs-à-plusieurs (un incident peut impliquer plusieurs élèves)
CREATE TABLE incident_eleve (
  id_incident INT UNSIGNED NOT NULL,
  id_eleve    INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_incident, id_eleve),
  CONSTRAINT fk_incident_eleve_incident FOREIGN KEY (id_incident) REFERENCES incident(id_incident) ON DELETE CASCADE,
  CONSTRAINT fk_incident_eleve_eleve    FOREIGN KEY (id_eleve)    REFERENCES eleve(id_eleve)       ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE carnet_suivi (
  id_carnet INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve  INT UNSIGNED NOT NULL UNIQUE,
  CONSTRAINT fk_carnet_suivi_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Table séparée (validé) — isole le journal chronologique du profil élève';

CREATE TABLE billet (
  id_billet    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve     INT UNSIGNED NOT NULL,
  type_billet  ENUM('entree','sortie','autorisation') NOT NULL,
  date_heure   DATETIME NOT NULL,
  motif        VARCHAR(255) NULL,
  CONSTRAINT fk_billet_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- EVENEMENT_CARNET : FK dédiées nullables (validé) plutôt qu'une clé polymorphe.
-- Contrainte d'intégrité applicative à faire respecter côté PHP (couche modèle) :
-- exactement UNE des trois colonnes id_billet / id_sanction / id_incident doit être renseignée,
-- selon la valeur de type_evenement.
CREATE TABLE evenement_carnet (
  id_evenement_carnet INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_carnet            INT UNSIGNED NOT NULL,
  date_evenement        DATETIME NOT NULL,
  type_evenement        ENUM('billet','sanction','incident','annonce','autre') NOT NULL,
  description           TEXT NULL,
  id_billet             INT UNSIGNED NULL,
  id_sanction           INT UNSIGNED NULL,
  id_incident           INT UNSIGNED NULL,
  CONSTRAINT fk_evenement_carnet_carnet   FOREIGN KEY (id_carnet)  REFERENCES carnet_suivi(id_carnet) ON DELETE CASCADE,
  CONSTRAINT fk_evenement_carnet_billet   FOREIGN KEY (id_billet)   REFERENCES billet(id_billet)       ON DELETE SET NULL,
  CONSTRAINT fk_evenement_carnet_sanction FOREIGN KEY (id_sanction) REFERENCES sanction(id_sanction)   ON DELETE SET NULL,
  CONSTRAINT fk_evenement_carnet_incident FOREIGN KEY (id_incident) REFERENCES incident(id_incident)   ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.8 — FINANCE
-- =====================================================================

CREATE TABLE type_frais (
  id_type_frais   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle          VARCHAR(100) NOT NULL UNIQUE,
  montant_defaut   DECIMAL(12,2) NOT NULL COMMENT 'paramétrable — section 11.2'
) ENGINE=InnoDB;

CREATE TABLE facture (
  id_facture           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve              INT UNSIGNED NOT NULL,
  numero_sequentiel     VARCHAR(50)  NOT NULL UNIQUE COMMENT 'décision #14 — non réutilisable, non modifiable après émission',
  date_emission         DATE NOT NULL,
  montant_total         DECIMAL(12,2) NOT NULL,
  statut                ENUM('active','annulee') NOT NULL DEFAULT 'active',
  date_annulation        DATETIME NULL,
  id_utilisateur_annulation INT UNSIGNED NULL,
  CONSTRAINT fk_facture_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE RESTRICT,
  CONSTRAINT fk_facture_utilisateur_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE ligne_facture (
  id_ligne_facture INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_facture        INT UNSIGNED NOT NULL,
  id_type_frais     INT UNSIGNED NOT NULL,
  montant_ligne     DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_ligne_facture_facture    FOREIGN KEY (id_facture)    REFERENCES facture(id_facture)       ON DELETE CASCADE,
  CONSTRAINT fk_ligne_facture_type_frais FOREIGN KEY (id_type_frais) REFERENCES type_frais(id_type_frais) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE remise (
  id_remise                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type_remise               ENUM('pourcentage','montant_fixe') NOT NULL,
  valeur_remise             DECIMAL(12,2) NOT NULL,
  motif                     VARCHAR(255) NOT NULL,
  id_utilisateur_validation INT UNSIGNED NOT NULL,
  CONSTRAINT fk_remise_utilisateur_validation FOREIGN KEY (id_utilisateur_validation) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='décision #13';

-- FACTURE <-> REMISE : modélisé (0,n)-(0,n) par prudence (cf. point D.3 du MCD, à confirmer si besoin réel de remise multi-factures)
CREATE TABLE facture_remise (
  id_facture INT UNSIGNED NOT NULL,
  id_remise  INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_facture, id_remise),
  CONSTRAINT fk_facture_remise_facture FOREIGN KEY (id_facture) REFERENCES facture(id_facture) ON DELETE CASCADE,
  CONSTRAINT fk_facture_remise_remise  FOREIGN KEY (id_remise)  REFERENCES remise(id_remise)   ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE echeance (
  id_echeance     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_facture       INT UNSIGNED NOT NULL,
  date_echeance    DATE NOT NULL,
  montant_prevu    DECIMAL(12,2) NOT NULL,
  statut_echeance  ENUM('payee','partielle','en_retard') NOT NULL DEFAULT 'en_retard',
  CONSTRAINT fk_echeance_facture FOREIGN KEY (id_facture) REFERENCES facture(id_facture) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='décision #23';

CREATE TABLE caisse (
  id_caisse      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_caisse     DATE NOT NULL UNIQUE,
  fond_de_caisse  DECIMAL(12,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE paiement (
  id_paiement                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_echeance                   INT UNSIGNED NOT NULL,
  numero_recu                   VARCHAR(50)  NOT NULL UNIQUE COMMENT 'décision #14',
  date_paiement                 DATETIME NOT NULL,
  montant                       DECIMAL(12,2) NOT NULL,
  mode_paiement                 ENUM('espece','banque','mobile_money') NOT NULL COMMENT 'décision #5',
  id_utilisateur_enregistrement INT UNSIGNED NOT NULL,
  id_caisse                     INT UNSIGNED NOT NULL,
  statut                        ENUM('actif','annule') NOT NULL DEFAULT 'actif' COMMENT 'suppression logique — décision #26',
  date_annulation                DATETIME NULL,
  id_utilisateur_annulation     INT UNSIGNED NULL,
  CONSTRAINT fk_paiement_echeance       FOREIGN KEY (id_echeance)                   REFERENCES echeance(id_echeance)       ON DELETE RESTRICT,
  CONSTRAINT fk_paiement_enregistrement FOREIGN KEY (id_utilisateur_enregistrement) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT,
  CONSTRAINT fk_paiement_caisse         FOREIGN KEY (id_caisse)                     REFERENCES caisse(id_caisse)           ON DELETE RESTRICT,
  CONSTRAINT fk_paiement_utilisateur_annulation FOREIGN KEY (id_utilisateur_annulation) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL,
  INDEX idx_paiement_doublon (id_echeance, montant, date_paiement) COMMENT 'accélère la détection de doublon — décision #24'
) ENGINE=InnoDB;

CREATE TABLE mouvement_caisse (
  id_mouvement    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_caisse        INT UNSIGNED NOT NULL,
  type_mouvement   ENUM('entree','sortie') NOT NULL,
  montant          DECIMAL(12,2) NOT NULL,
  date_creation     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mouvement_caisse_caisse FOREIGN KEY (id_caisse) REFERENCES caisse(id_caisse) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.9 — INVENTAIRE & PRÊTS
-- =====================================================================

CREATE TABLE materiel (
  id_materiel     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle          VARCHAR(150) NOT NULL,
  quantite_stock   INT UNSIGNED NOT NULL DEFAULT 0,
  seuil_alerte     INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE mouvement_stock (
  id_mouvement_stock INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_materiel         INT UNSIGNED NOT NULL,
  type_mouvement       ENUM('entree','sortie') NOT NULL,
  quantite             INT UNSIGNED NOT NULL,
  date_mouvement       DATE NOT NULL,
  CONSTRAINT fk_mouvement_stock_materiel FOREIGN KEY (id_materiel) REFERENCES materiel(id_materiel) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pret_materiel (
  id_pret               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_materiel            INT UNSIGNED NOT NULL,
  id_personne            INT UNSIGNED NOT NULL COMMENT 'élève ou enseignant',
  date_sortie             DATE NOT NULL,
  date_retour_prevue      DATE NULL,
  date_retour_effective   DATE NULL,
  CONSTRAINT fk_pret_materiel_materiel FOREIGN KEY (id_materiel) REFERENCES materiel(id_materiel) ON DELETE RESTRICT,
  CONSTRAINT fk_pret_materiel_personne FOREIGN KEY (id_personne) REFERENCES personne(id_personne) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.10 — DOCUMENTS
-- =====================================================================

CREATE TABLE type_document_obligatoire (
  id_type_doc_obligatoire INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle                  VARCHAR(150) NOT NULL,
  profil_concerne          ENUM('eleve','enseignant') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE document_personne (
  id_document_personne     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_personne               INT UNSIGNED NOT NULL,
  id_type_doc_obligatoire   INT UNSIGNED NULL,
  type_document             VARCHAR(100) NOT NULL,
  chemin_fichier            VARCHAR(255) NOT NULL,
  date_ajout                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_document_personne_personne FOREIGN KEY (id_personne)             REFERENCES personne(id_personne)                             ON DELETE CASCADE,
  CONSTRAINT fk_document_personne_type      FOREIGN KEY (id_type_doc_obligatoire) REFERENCES type_document_obligatoire(id_type_doc_obligatoire) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE document_administratif (
  id_doc_admin  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre          VARCHAR(150) NOT NULL,
  categorie      VARCHAR(100) NULL,
  public_vise    VARCHAR(100) NULL,
  date_creation   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB COMMENT='décision #30 — bibliothèque documentaire interne';

-- VERSION_DOCUMENT partagé par DOCUMENT_PERSONNE et DOCUMENT_ADMINISTRATIF :
-- FK dédiées nullables (cohérent avec le choix retenu pour EVENEMENT_CARNET).
CREATE TABLE version_document (
  id_version            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_document_personne   INT UNSIGNED NULL,
  id_doc_admin           INT UNSIGNED NULL,
  date_version            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  auteur                  VARCHAR(150) NOT NULL,
  commentaire             VARCHAR(255) NULL,
  CONSTRAINT fk_version_document_personne FOREIGN KEY (id_document_personne) REFERENCES document_personne(id_document_personne)     ON DELETE CASCADE,
  CONSTRAINT fk_version_document_admin    FOREIGN KEY (id_doc_admin)         REFERENCES document_administratif(id_doc_admin)         ON DELETE CASCADE,
  CONSTRAINT chk_version_document_source CHECK (
    (id_document_personne IS NOT NULL AND id_doc_admin IS NULL) OR
    (id_document_personne IS NULL AND id_doc_admin IS NOT NULL)
  )
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.11 — PARAMÉTRAGE & CONFIGURATION
-- (créé avant MODELE_DOCUMENT car référencé par celui-ci)
-- =====================================================================

CREATE TABLE parametrage_etablissement (
  id_parametrage             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom_etablissement           VARCHAR(150) NOT NULL,
  logo                        VARCHAR(255) NULL,
  monnaie                     VARCHAR(10)  NOT NULL DEFAULT 'MGA',
  langue_par_defaut           VARCHAR(10)  NOT NULL DEFAULT 'fr',
  theme_par_defaut            VARCHAR(50)  NULL,
  chemin_stockage_documents   VARCHAR(255) NOT NULL,
  format_matricule            VARCHAR(100) NOT NULL DEFAULT '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}',
  prefixe_matricule           VARCHAR(10)  NOT NULL DEFAULT 'ELV',
  annee_courante              VARCHAR(20)  NULL DEFAULT '2025-2026'
) ENGINE=InnoDB COMMENT='décision #2 — table unique dans cette version, prête pour évolution multi-établissement';

CREATE TABLE modele_document (
  id_modele              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_parametrage          INT UNSIGNED NOT NULL,
  type_modele              ENUM('bulletin','recu','attestation','billet') NOT NULL,
  contenu_parametrable     JSON NOT NULL,
  CONSTRAINT fk_modele_document_parametrage FOREIGN KEY (id_parametrage) REFERENCES parametrage_etablissement(id_parametrage) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sequence_numerotation (
  id_sequence      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_parametrage    INT UNSIGNED NOT NULL,
  type_document      VARCHAR(50) NOT NULL COMMENT 'ex. facture, recu, matricule',
  id_annee           INT UNSIGNED NOT NULL,
  dernier_numero     INT UNSIGNED NOT NULL DEFAULT 0,
  format             VARCHAR(100) NOT NULL COMMENT 'ex. {PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL} — décisions #14, #16',
  CONSTRAINT fk_sequence_numerotation_parametrage FOREIGN KEY (id_parametrage) REFERENCES parametrage_etablissement(id_parametrage) ON DELETE CASCADE,
  CONSTRAINT fk_sequence_numerotation_annee       FOREIGN KEY (id_annee)       REFERENCES annee_scolaire(id_annee)                 ON DELETE CASCADE,
  UNIQUE KEY uk_sequence_numerotation (type_document, id_annee)
) ENGINE=InnoDB;

CREATE TABLE seuil_alerte (
  id_seuil        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_parametrage   INT UNSIGNED NOT NULL,
  type_seuil        VARCHAR(50) NOT NULL COMMENT 'ex. seuil_redoublement, seuil_absences',
  valeur_seuil      DECIMAL(8,2) NOT NULL,
  CONSTRAINT fk_seuil_alerte_parametrage FOREIGN KEY (id_parametrage) REFERENCES parametrage_etablissement(id_parametrage) ON DELETE CASCADE,
  UNIQUE KEY uk_seuil_alerte (id_parametrage, type_seuil)
) ENGINE=InnoDB;

-- =====================================================================
-- DOMAINE A.12 — COMMUNICATION & SERVICES ANNEXES
-- =====================================================================

-- Hypothèse (à valider) : le MCD ne précise pas de destinataire pour MESSAGE.
-- id_utilisateur_destinataire ajouté par déduction fonctionnelle.
CREATE TABLE message (
  id_message                     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur_expediteur       INT UNSIGNED NOT NULL,
  id_utilisateur_destinataire     INT UNSIGNED NOT NULL COMMENT 'hypothèse à valider — non explicite dans le MCD',
  date_envoi                      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  contenu                         TEXT NOT NULL,
  lu                               TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_message_expediteur   FOREIGN KEY (id_utilisateur_expediteur)   REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT,
  CONSTRAINT fk_message_destinataire FOREIGN KEY (id_utilisateur_destinataire) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE annonce (
  id_annonce             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur_publication INT UNSIGNED NOT NULL,
  titre                   VARCHAR(150) NOT NULL,
  contenu                 TEXT NOT NULL,
  date_publication        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_annonce_utilisateur FOREIGN KEY (id_utilisateur_publication) REFERENCES utilisateur(id_utilisateur) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ANNONCE <-> EVENEMENT_CARNET : plusieurs-à-plusieurs (une annonce peut toucher plusieurs carnets)
CREATE TABLE annonce_evenement_carnet (
  id_annonce           INT UNSIGNED NOT NULL,
  id_evenement_carnet   INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_annonce, id_evenement_carnet),
  CONSTRAINT fk_annonce_evt_carnet_annonce FOREIGN KEY (id_annonce)         REFERENCES annonce(id_annonce)                 ON DELETE CASCADE,
  CONSTRAINT fk_annonce_evt_carnet_evt     FOREIGN KEY (id_evenement_carnet) REFERENCES evenement_carnet(id_evenement_carnet) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE repas (
  id_repas    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_repas   DATE NOT NULL,
  menu         VARCHAR(255) NOT NULL
) ENGINE=InnoDB COMMENT='décision #45 — module cantine';

-- Association porteuse RESERVATION_REPAS (ELEVE <-> REPAS)
CREATE TABLE reservation_repas (
  id_reservation      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve             INT UNSIGNED NOT NULL,
  id_repas             INT UNSIGNED NOT NULL,
  statut_reservation   ENUM('en_attente','confirmee','annulee') NOT NULL DEFAULT 'en_attente',
  CONSTRAINT fk_reservation_repas_eleve FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve) ON DELETE CASCADE,
  CONSTRAINT fk_reservation_repas_repas FOREIGN KEY (id_repas) REFERENCES repas(id_repas) ON DELETE CASCADE,
  UNIQUE KEY uk_reservation_repas (id_eleve, id_repas)
) ENGINE=InnoDB;

CREATE TABLE examen_blanc (
  id_examen_blanc INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle          VARCHAR(150) NOT NULL,
  date_examen      DATE NOT NULL
) ENGINE=InnoDB COMMENT='décision #47';

-- Association porteuse RESULTAT_EXAMEN_BLANC (ELEVE <-> EXAMEN_BLANC)
CREATE TABLE resultat_examen_blanc (
  id_resultat_examen INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_eleve            INT UNSIGNED NOT NULL,
  id_examen_blanc      INT UNSIGNED NOT NULL,
  note                 DECIMAL(5,2) NULL,
  CONSTRAINT fk_resultat_examen_eleve  FOREIGN KEY (id_eleve)        REFERENCES eleve(id_eleve)               ON DELETE CASCADE,
  CONSTRAINT fk_resultat_examen_examen FOREIGN KEY (id_examen_blanc) REFERENCES examen_blanc(id_examen_blanc) ON DELETE CASCADE,
  UNIQUE KEY uk_resultat_examen_blanc (id_eleve, id_examen_blanc)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- FIN DU SCRIPT — 67 entités du MCD + 6 tables de jonction pures
-- (personne_role, role_permission, incident_eleve, facture_remise,
--  annonce_evenement_carnet, plus les associations porteuses déjà
--  comptées comme entités : reservation_repas, resultat_examen_blanc)
-- =====================================================================
