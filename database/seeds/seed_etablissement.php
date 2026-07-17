<?php

require_once __DIR__ . '/../../config/database.php';

$pdo = get_connexion_base_donnees();
if ($pdo === null) {
    echo "Erreur : impossible de se connecter à la base de données.\n";
    exit(1);
}

$pdo->beginTransaction();

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE sequence_numerotation");
    $pdo->exec("TRUNCATE TABLE modele_document");
    $pdo->exec("TRUNCATE TABLE parametrage_etablissement");
    $pdo->exec("TRUNCATE TABLE document_personne");
    $pdo->exec("TRUNCATE TABLE document_administratif");
    $pdo->exec("TRUNCATE TABLE version_document");
    $pdo->exec("TRUNCATE TABLE planning_surveillant");
    $pdo->exec("TRUNCATE TABLE agenda_evenement");
    $pdo->exec("TRUNCATE TABLE seance_reelle");
    $pdo->exec("TRUNCATE TABLE emploi_du_temps");
    $pdo->exec("TRUNCATE TABLE creneau_horaire");
    $pdo->exec("TRUNCATE TABLE salaire");
    $pdo->exec("TRUNCATE TABLE heure_supplementaire");
    $pdo->exec("TRUNCATE TABLE paiement");
    $pdo->exec("TRUNCATE TABLE mouvement_caisse");
    $pdo->exec("TRUNCATE TABLE echeance");
    $pdo->exec("TRUNCATE TABLE facture_remise");
    $pdo->exec("TRUNCATE TABLE remise");
    $pdo->exec("TRUNCATE TABLE ligne_facture");
    $pdo->exec("TRUNCATE TABLE facture");
    $pdo->exec("TRUNCATE TABLE mouvement_stock");
    $pdo->exec("TRUNCATE TABLE pret_materiel");
    $pdo->exec("TRUNCATE TABLE materiel");
    $pdo->exec("TRUNCATE TABLE billet");
    $pdo->exec("TRUNCATE TABLE evenement_carnet");
    $pdo->exec("TRUNCATE TABLE incident_eleve");
    $pdo->exec("TRUNCATE TABLE incident");
    $pdo->exec("TRUNCATE TABLE sanction");
    $pdo->exec("TRUNCATE TABLE retard");
    $pdo->exec("TRUNCATE TABLE absence");
    $pdo->exec("TRUNCATE TABLE bulletin");
    $pdo->exec("TRUNCATE TABLE note");
    $pdo->exec("TRUNCATE TABLE evaluation");
    $pdo->exec("TRUNCATE TABLE periode");
    $pdo->exec("TRUNCATE TABLE affectation");
    $pdo->exec("TRUNCATE TABLE contrat");
    $pdo->exec("TRUNCATE TABLE inscription");
    $pdo->exec("TRUNCATE TABLE transfert");
    $pdo->exec("TRUNCATE TABLE programme");
    $pdo->exec("TRUNCATE TABLE matiere");
    $pdo->exec("TRUNCATE TABLE salle");
    $pdo->exec("TRUNCATE TABLE classe");
    $pdo->exec("TRUNCATE TABLE serie");
    $pdo->exec("TRUNCATE TABLE niveau");
    $pdo->exec("TRUNCATE TABLE cycle");
    $pdo->exec("TRUNCATE TABLE acces_parent_eleve");
    $pdo->exec("TRUNCATE TABLE role_permission");
    $pdo->exec("TRUNCATE TABLE permission");
    $pdo->exec("TRUNCATE TABLE utilisateur");
    $pdo->exec("TRUNCATE TABLE personnel_administratif");
    $pdo->exec("TRUNCATE TABLE enseignant");
    $pdo->exec("TRUNCATE TABLE eleve");
    $pdo->exec("TRUNCATE TABLE personne_role");
    $pdo->exec("TRUNCATE TABLE personne");
    $pdo->exec("TRUNCATE TABLE journal_audit");
    $pdo->exec("TRUNCATE TABLE journal_connexion");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    $pdo->exec("INSERT INTO parametrage_etablissement (nom_etablissement, logo, monnaie, langue_par_defaut, theme_par_defaut, chemin_stockage_documents) VALUES ('Smart-Sekoly', '', 'MGA', 'fr', 'light', 'documents/')");
    $idParametrage = (int) $pdo->lastInsertId();

    $pdo->exec("INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, format, dernier_numero) VALUES ({$idParametrage}, 'matricule', 1, 'EL-{ANNEE}-{NUMERO_SEQUENTIEL}', 0)");
    $pdo->exec("INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, format, dernier_numero) VALUES ({$idParametrage}, 'facture', 1, 'REC-{ANNEE}-{NUMERO_SEQUENTIEL}', 0)");

    $pdo->exec("INSERT INTO role (libelle) VALUES ('directeur'), ('comptable'), ('enseignant'), ('secretaire'), ('surveillant'), ('drh'), ('caissiere')");
    $pdo->exec("INSERT INTO permission (module, sous_module, action) VALUES
      ('tableau-de-bord', NULL, 'lire'),
      ('finance', NULL, 'lire'),
      ('finance', NULL, 'modifier'),
      ('eleves', NULL, 'lire'),
      ('eleves', NULL, 'modifier'),
      ('communication', NULL, 'lire'),
      ('portails', NULL, 'lire'),
      ('parametrage', NULL, 'lire'),
      ('roles', NULL, 'lire'),
      ('roles', NULL, 'modifier'),
      ('permissions', NULL, 'lire'),
      ('permissions', NULL, 'modifier')");

    $roles = $pdo->query("SELECT id_role, libelle FROM role")->fetchAll(PDO::FETCH_ASSOC);
    $permissions = $pdo->query("SELECT id_permission, module, action FROM permission")->fetchAll(PDO::FETCH_ASSOC);
    $roleMap = [];
    foreach ($roles as $role) {
        $roleMap[$role['libelle']] = (int) $role['id_role'];
    }
    $permissionMap = [];
    foreach ($permissions as $permission) {
        $permissionMap[$permission['module'] . '.' . $permission['action']] = (int) $permission['id_permission'];
    }

    $pdo->exec("INSERT INTO cycle (libelle) VALUES ('Primaire'), ('Collège'), ('Lycée')");
    $pdo->exec("INSERT INTO niveau (id_cycle, libelle) VALUES (1, 'CM2'), (2, '6ème'), (2, '5ème'), (2, '4ème'), (2, '3ème'), (3, '2nde'), (3, '1ère'), (3, 'Tle')");
    $pdo->exec("INSERT INTO serie (libelle) VALUES ('OSE'), ('S1'), ('S2')");
    $pdo->exec("INSERT INTO salle (libelle, capacite) VALUES ('Salle A', 30), ('Salle B', 28), ('Salle C', 25)");
    $pdo->exec("INSERT INTO matiere (libelle) VALUES ('Mathématiques'), ('Français'), ('Anglais'), ('Physique'), ('Histoire'), ('Géographie'), ('SVT'), ('Éducation physique'), ('Informatique')");

    $pdo->exec("INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES ('2026-2027', '2026-09-01', '2027-06-30', 'active'), ('2025-2026', '2025-09-01', '2026-06-30', 'cloturee')");

    $pdo->exec("INSERT INTO creneau_horaire (jour_semaine, heure_debut, heure_fin) VALUES
      ('lundi', '08:00:00', '09:30:00'),
      ('lundi', '09:45:00', '11:15:00'),
      ('mardi', '08:00:00', '09:30:00'),
      ('mardi', '09:45:00', '11:15:00')");

    $pdo->exec("INSERT INTO periode (id_annee, libelle, type_periode) VALUES (1, '1er Trimestre', 'trimestre'), (1, '2ème Trimestre', 'trimestre')");

    $pdo->exec("INSERT INTO utilisateur (id_personne, identifiant, mot_de_passe_hash, statut_compte, doit_changer_mdp) VALUES (1, 'admin', '" . password_hash('admin', PASSWORD_DEFAULT) . "', 'actif', 1)");
    $pdo->exec("INSERT INTO personne (id_personne, nom, prenom, email, telephone, adresse, date_creation, date_modification) VALUES (1, 'Smart', 'Admin', 'admin@smart-sekoly.local', '0000000000', 'Siège social', NOW(), NOW())");
    $pdo->exec("INSERT INTO personne_role (id_personne, id_role) VALUES (1, {$roleMap['directeur']})");

    $pdo->commit();
    echo "Seed établissement créé avec succès.\n";
} catch (Throwable $exception) {
    $pdo->rollBack();
    echo "Erreur seed établissement : " . $exception->getMessage() . "\n";
    exit(1);
}
