<?php

require_once __DIR__ . '/../../config/database.php';

$pdo = get_connexion_base_donnees();
if ($pdo === null) {
    echo "Erreur : impossible de se connecter à la base de données.\n";
    exit(1);
}

function executerInsert(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $pdo->lastInsertId();
}

function genererMatriculeEleve(int $index): string
{
    return sprintf('EL-%s-%04d', date('Y'), $index);
}

function genererCodeParent(int $index): string
{
    return sprintf('P-%04d-%s', $index, substr(md5((string) $index), 0, 6));
}

\$pdo->beginTransaction();\necho "BEGIN TRANSACTION\n";\n

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tables = [
        'sequence_numerotation', 'modele_document', 'parametrage_etablissement', 'document_personne', 'document_administratif',
        'version_document', 'planning_surveillant', 'agenda_evenement', 'seance_reelle', 'emploi_du_temps', 'creneau_horaire',
        'salaire', 'heure_supplementaire', 'paiement', 'mouvement_caisse', 'echeance', 'facture_remise', 'remise', 'ligne_facture',
        'facture', 'mouvement_stock', 'pret_materiel', 'materiel', 'billet', 'evenement_carnet', 'incident_eleve', 'incident',
        'sanction', 'retard', 'absence', 'bulletin', 'note', 'evaluation', 'periode', 'affectation', 'contrat', 'inscription',
        'transfert', 'programme', 'matiere', 'salle', 'classe', 'serie', 'niveau', 'cycle', 'acces_parent_eleve', 'role_permission',
        'permission', 'utilisateur', 'personnel_administratif', 'enseignant', 'eleve', 'personne_role', 'personne', 'journal_audit',
        'journal_connexion'
    ];

    foreach ($tables as $table) {
        $pdo->exec("TRUNCATE TABLE {$table}");
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    $idParametrage = executerInsert($pdo,
        "INSERT INTO parametrage_etablissement (nom_etablissement, logo, monnaie, langue_par_defaut, theme_par_defaut, chemin_stockage_documents) VALUES (?, ?, ?, ?, ?, ?)",
        ['Smart-Sekoly', '', 'MGA', 'fr', 'light', 'documents/']
    );

    $annee2026Id = executerInsert($pdo,
        "INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (?, ?, ?, ?)",
        ['2026-2027', '2026-09-01', '2027-06-30', 'active']
    );
    executerInsert($pdo,
        "INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (?, ?, ?, ?)",
        ['2025-2026', '2025-09-01', '2026-06-30', 'cloturee']
    );

    executerInsert($pdo,
        "INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, format, dernier_numero) VALUES (?, ?, ?, ?, ?)",
        [$idParametrage, 'matricule', $annee2026Id, 'EL-{ANNEE}-{NUMERO_SEQUENTIEL}', 0]
    );
    executerInsert($pdo,
        "INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, format, dernier_numero) VALUES (?, ?, ?, ?, ?)",
        [$idParametrage, 'facture', $annee2026Id, 'REC-{ANNEE}-{NUMERO_SEQUENTIEL}', 0]
    );

    $roles = ['directeur', 'comptable', 'enseignant', 'secretaire', 'surveillant', 'drh', 'caissiere', 'parent'];
    $roleMap = [];
    $stmtRole = $pdo->prepare('INSERT INTO role (libelle) VALUES (?)');
    foreach ($roles as $role) {
        $stmtRole->execute([$role]);
        $roleMap[$role] = (int) $pdo->lastInsertId();
    }

    $permissions = [
        ['tableau-de-bord', null, 'lire'],
        ['finance', null, 'lire'],
        ['finance', null, 'modifier'],
        ['eleves', null, 'lire'],
        ['eleves', null, 'modifier'],
        ['communication', null, 'lire'],
        ['portails', null, 'lire'],
        ['parametrage', null, 'lire'],
        ['roles', null, 'lire'],
        ['roles', null, 'modifier'],
        ['permissions', null, 'lire'],
        ['permissions', null, 'modifier'],
        ['enseignants', null, 'lire'],
        ['enseignants', null, 'modifier'],
        ['salaires', null, 'lire'],
        ['absence', null, 'lire']
    ];

    $permissionMap = [];
    $stmtPermission = $pdo->prepare('INSERT INTO permission (module, sous_module, action) VALUES (?, ?, ?)');
    foreach ($permissions as $permission) {
        $stmtPermission->execute($permission);
        $permissionMap[$permission[0] . '.' . $permission[2]] = (int) $pdo->lastInsertId();
    }

    $stmtRolePermission = $pdo->prepare('INSERT INTO role_permission (id_role, id_permission) VALUES (?, ?)');
    foreach ($roleMap as $roleLabel => $idRole) {
        foreach (['tableau-de-bord.lire', 'eleves.lire', 'communication.lire', 'portails.lire'] as $permKey) {
            if (isset($permissionMap[$permKey])) {
                $stmtRolePermission->execute([$idRole, $permissionMap[$permKey]]);
            }
        }
    }

    $stmtRolePermission->execute([$roleMap['directeur'], $permissionMap['finance.modifier']]);
    $stmtRolePermission->execute([$roleMap['directeur'], $permissionMap['roles.modifier']]);
    $stmtRolePermission->execute([$roleMap['directeur'], $permissionMap['permissions.modifier']]);

    $cycles = ['Primaire', 'Collège', 'Lycée'];
    $stmtCycle = $pdo->prepare('INSERT INTO cycle (libelle) VALUES (?)');
    foreach ($cycles as $cycle) {
        $stmtCycle->execute([$cycle]);
    }

    $niveauData = [
        [1, 'CM2'], [2, '6ème'], [2, '5ème'], [2, '4ème'], [2, '3ème'], [3, '2nde'], [3, '1ère'], [3, 'Tle']
    ];
    $stmtNiveau = $pdo->prepare('INSERT INTO niveau (id_cycle, libelle) VALUES (?, ?)');
    foreach ($niveauData as $niveauRow) {
        $stmtNiveau->execute($niveauRow);
    }

    $series = ['OSE', 'S1', 'S2'];
    $stmtSerie = $pdo->prepare('INSERT INTO serie (libelle) VALUES (?)');
    foreach ($series as $serie) {
        $stmtSerie->execute([$serie]);
    }

    $salles = [
        ['Salle A', 30], ['Salle B', 28], ['Salle C', 25], ['Salle D', 26], ['Salle E', 24]
    ];
    $stmtSalle = $pdo->prepare('INSERT INTO salle (libelle, capacite) VALUES (?, ?)');
    foreach ($salles as $salle) {
        $stmtSalle->execute($salle);
    }

    $matieres = ['Mathématiques', 'Français', 'Anglais', 'Physique', 'Histoire', 'Géographie', 'SVT', 'Éducation physique', 'Informatique'];
    $stmtMatiere = $pdo->prepare('INSERT INTO matiere (libelle) VALUES (?)');
    foreach ($matieres as $matiere) {
        $stmtMatiere->execute([$matiere]);
    }

    $stmtAnnee = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (?, ?, ?, ?)');
    $stmtAnnee->execute(['2026-2027', '2026-09-01', '2027-06-30', 'active']);
    $stmtAnnee->execute(['2025-2026', '2025-09-01', '2026-06-30', 'cloturee']);
    $anneeActiveId = (int) $pdo->lastInsertId() - 1;

    $stmtCreneau = $pdo->prepare('INSERT INTO creneau_horaire (jour_semaine, heure_debut, heure_fin) VALUES (?, ?, ?)');
    $creneaux = [
      ['lundi', '08:00:00', '09:30:00'], ['lundi', '09:45:00', '11:15:00'],
      ['mardi', '08:00:00', '09:30:00'], ['mardi', '09:45:00', '11:15:00'],
      ['mercredi', '08:00:00', '09:30:00'], ['jeudi', '09:45:00', '11:15:00'],
      ['vendredi', '08:00:00', '09:30:00']
    ];
    foreach ($creneaux as $creneau) {
        $stmtCreneau->execute($creneau);
    }

    $stmtPeriode = $pdo->prepare('INSERT INTO periode (id_annee, libelle, type_periode) VALUES (?, ?, ?)');
    $stmtPeriode->execute([$anneeActiveId, '1er Trimestre', 'trimestre']);
    $stmtPeriode->execute([$anneeActiveId, '2ème Trimestre', 'trimestre']);
    $periodeIds = array_column($pdo->query("SELECT id_periode FROM periode WHERE id_annee = {$anneeActiveId}")->fetchAll(PDO::FETCH_ASSOC), 'id_periode');

    $stmtClasse = $pdo->prepare('INSERT INTO classe (id_niveau, id_serie, libelle, effectif_max) VALUES (?, ?, ?, ?)');
    $classes = [
        [1, null, 'CM2 A', 28], [1, null, 'CM2 B', 27],
        [2, null, '6ème A', 30], [2, null, '6ème B', 30],
        [3, null, '5ème A', 29], [4, null, '4ème A', 28], [5, null, '3ème A', 28],
        [6, 2, '2nde S1', 28], [6, 3, '2nde S2', 28], [7, 2, '1ère S1', 26], [8, 2, 'Tle S1', 24]
    ];
    foreach ($classes as $classe) {
        $stmtClasse->execute($classe);
    }
    $classeIds = array_column($pdo->query('SELECT id_classe FROM classe')->fetchAll(PDO::FETCH_ASSOC), 'id_classe');

    $stmtProgramme = $pdo->prepare('INSERT INTO programme (id_classe, id_matiere, id_annee, coefficient, volume_horaire, est_obligatoire) VALUES (?, ?, ?, ?, ?, ?)');
    $matiereIds = array_column($pdo->query('SELECT id_matiere FROM matiere')->fetchAll(PDO::FETCH_ASSOC), 'id_matiere');
    foreach ($classeIds as $idClasse) {
        foreach ($matiereIds as $idMatiere) {
            $stmtProgramme->execute([$idClasse, $idMatiere, $anneeActiveId, 1.0, 54, 1]);
        }
    }

    $staffAccounts = [
        ['Admin', 'Directeur', 'directeur', 'directeur', 'directeur@smart-sekoly.local'],
        ['Compta', 'Comptable', 'comptable', 'comptable', 'comptable@smart-sekoly.local'],
        ['Secretaire', 'Secrétaire', 'secretaire', 'secretaire', 'secretaire@smart-sekoly.local'],
        ['Surveillant', 'Surveillant', 'surveillant', 'surveillant', 'surveillant@smart-sekoly.local'],
        ['DRH', 'DRH', 'drh', 'drh', 'drh@smart-sekoly.local'],
        ['Caissiere', 'Caissière', 'caissiere', 'caissiere', 'caissiere@smart-sekoly.local']
    ];

    $stmtPersonne = $pdo->prepare('INSERT INTO personne (nom, prenom, email, telephone, adresse, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmtUtilisateur = $pdo->prepare('INSERT INTO utilisateur (id_personne, identifiant, mot_de_passe_hash, statut_compte, doit_changer_mdp) VALUES (?, ?, ?, ?, ?)');
    $stmtPersonneRole = $pdo->prepare('INSERT INTO personne_role (id_personne, id_role) VALUES (?, ?)');

    $personneIdCounter = 1;
    $stmtPersonne->execute(['Smart', 'Admin', 'admin@smart-sekoly.local', '0000000000', 'Siège social']);
    $personneIdCounter = (int) $pdo->lastInsertId();
    $stmtUtilisateur->execute([$personneIdCounter, 'admin', password_hash('admin', PASSWORD_DEFAULT), 'actif', 1]);
    $stmtPersonneRole->execute([$personneIdCounter, $roleMap['directeur']]);

    foreach ($staffAccounts as $item) {
        [$nom, $prenom, $identifiant, $roleKey, $email] = $item;
        if ($identifiant === 'admin') {
            continue;
        }
        $stmtPersonne->execute([$nom, $prenom, $email, '0000000000', 'Siège social']);
        $idPersonne = (int) $pdo->lastInsertId();
        $stmtUtilisateur->execute([$idPersonne, $identifiant, password_hash($identifiant, PASSWORD_DEFAULT), 'actif', 0]);
        $stmtPersonneRole->execute([$idPersonne, $roleMap[$roleKey]]);
    }

    $teacherNames = [
        ['Lala', 'Rabe'], ['Aina', 'Rakoto'], ['Miora', 'Rasolo'], ['Hery', 'Rakotoniaina'], ['Tiana', 'Randriatsalama'],
        ['Faneva', 'Rajaonary'], ['Nirina', 'Rasoloharima'], ['Voahirana', 'Razanajatovo'], ['Tahina', 'Randrianarisoa'], ['Solofo', 'Rasolofo'],
        ['Nomena', 'Ratsimbazafy'], ['Mamy', 'Rakotonirina'], ['Soa', 'Rasoanaivo'], ['Faniry', 'Rajoelina'], ['Haja', 'Rakotobe']
    ];

    $stmtEnseignant = $pdo->prepare('INSERT INTO enseignant (id_personne, matricule, date_embauche, statut_enseignant, date_creation, date_modification) VALUES (?, ?, ?, ?, NOW(), NOW())');
    $stmtContrat = $pdo->prepare('INSERT INTO contrat (id_enseignant, type_contrat, date_debut, date_fin, montant_ou_taux_horaire) VALUES (?, ?, ?, ?, ?)');
    $stmtPersonneRole = $pdo->prepare('INSERT INTO personne_role (id_personne, id_role) VALUES (?, ?)');

    $teacherIds = [];
    foreach ($teacherNames as $index => $teacherName) {
        [$prenom, $nom] = $teacherName;
        $email = strtolower($prenom . '.' . $nom . '@smart-sekoly.local');
        $stmtPersonne->execute([$nom, $prenom, $email, '034000000' . ($index + 1), 'Lycée Smart']);
        $idPersonne = (int) $pdo->lastInsertId();
        $stmtEnseignant->execute([$idPersonne, 'EN' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT), '2024-09-01', 'actif']);
        $idEnseignant = (int) $pdo->lastInsertId();
        $teacherIds[] = $idEnseignant;
        $stmtContrat->execute([$idEnseignant, $index < 5 ? 'permanent' : 'horaire', '2024-09-01', null, $index < 5 ? 1200000.00 : 25000.00]);
        if ($index < 3) {
            $login = strtolower($prenom . '.' . $nom);
            $stmtUtilisateur->execute([$idPersonne, $login, password_hash($login, PASSWORD_DEFAULT), 'actif', 0]);
            $stmtPersonneRole->execute([$idPersonne, $roleMap['enseignant']]);
        }
    }

    $eleveTemplate = ['Randria', 'Rabe', 'Rasoa', 'Rakotonirina', 'Raharinirina', 'Rasolo', 'Rakotozafy', 'Ravao', 'Rasoanantenaina', 'Ramamonjisoa'];
    $stmtElevePersonne = $pdo->prepare('INSERT INTO personne (nom, prenom, email, telephone, adresse, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmtEleve = $pdo->prepare('INSERT INTO eleve (id_personne, matricule, date_entree, statut_scolaire, date_creation, date_modification) VALUES (?, ?, ?, ?, NOW(), NOW())');
    $stmtInscription = $pdo->prepare('INSERT INTO inscription (id_eleve, id_classe, id_annee, date_inscription, statut_inscription, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');

    $eleveIds = [];
    for ($i = 1; $i <= 52; $i++) {
        $nom = $eleveTemplate[$i % count($eleveTemplate)];
        $prenom = 'Eleve' . $i;
        $email = sprintf('eleve%s@smart-sekoly.local', $i);
        $telephone = '0342000' . str_pad((string) $i, 3, '0', STR_PAD_LEFT);

        $stmtElevePersonne->execute([$nom, $prenom, $email, $telephone, 'Madagascar']);
        $idPersonne = (int) $pdo->lastInsertId();
        $matricule = genererMatriculeEleve($i);
        $stmtEleve->execute([$idPersonne, $matricule, '2026-09-01', 'actif']);
        $idEleve = (int) $pdo->lastInsertId();
        $eleveIds[] = $idEleve;

        $classeId = $classeIds[$i % count($classeIds)];
        $stmtInscription->execute([$idEleve, $classeId, $anneeActiveId, '2026-09-01', 'actif']);
    }

    $stmtParentAccess = $pdo->prepare('INSERT INTO acces_parent_eleve (id_eleve, code_acces, statut) VALUES (?, ?, ?)');
    foreach (array_slice($eleveIds, 0, 30) as $index => $idEleve) {
        $stmtParentAccess->execute([$idEleve, genererCodeParent($index + 1), 'actif']);
    }

    $stmtAffectation = $pdo->prepare('INSERT INTO affectation (id_enseignant, id_matiere, id_classe, id_annee, date_debut, date_fin) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($classeIds as $classeIndex => $classeId) {
        $assignedTeachers = array_slice($teacherIds, $classeIndex % count($teacherIds), 4);
        if (count($assignedTeachers) < 4) {
            $assignedTeachers = array_merge($assignedTeachers, array_slice($teacherIds, 0, 4 - count($assignedTeachers)));
        }
        foreach ($assignedTeachers as $teacherId) {
            $matiereId = $matiereIds[($classeIndex + $teacherId) % count($matiereIds)];
            $stmtAffectation->execute([$teacherId, $matiereId, $classeId, $anneeActiveId, '2026-09-01', null]);
        }
    }

    $stmtEvaluation = $pdo->prepare('INSERT INTO evaluation (id_matiere, id_classe, id_periode, id_enseignant, date_evaluation, coefficient) VALUES (?, ?, ?, ?, ?, ?)');
    $stmtNote = $pdo->prepare('INSERT INTO note (id_eleve, id_evaluation, valeur, appreciation, statut, date_creation, date_modification) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');

    $evaluations = [];
    foreach ($periodeIds as $periodeId) {
        foreach ($classeIds as $classeId) {
            $matiereSubset = array_slice($matiereIds, 0, 4);
            foreach ($matiereSubset as $matiereId) {
                $enseignantId = $teacherIds[array_rand($teacherIds)];
                $evaluationId = executerInsert($pdo,
                    'INSERT INTO evaluation (id_matiere, id_classe, id_periode, id_enseignant, date_evaluation, coefficient) VALUES (?, ?, ?, ?, ?, ?)',
                    [$matiereId, $classeId, $periodeId, $enseignantId, '2026-11-15', 1.0]
                );
                $evaluations[] = $evaluationId;
                $elevesDansClasse = array_filter($eleveIds, function ($id) use ($pdo, $classeId, $anneeActiveId) {
                    $stmt = $pdo->prepare('SELECT 1 FROM inscription WHERE id_eleve = ? AND id_classe = ? AND id_annee = ?');
                    $stmt->execute([$id, $classeId, $anneeActiveId]);
                    return (bool) $stmt->fetchColumn();
                });
                $elevesDansClasse = array_values($elevesDansClasse);
                $sampleEleves = array_slice($elevesDansClasse, 0, 5);
                foreach ($sampleEleves as $idEleve) {
                    $valeur = rand(50, 95) / 10;
                    $appreciation = $valeur >= 14 ? 'Très bien' : ($valeur >= 10 ? 'Bien' : 'À améliorer');
                    $stmtNote->execute([$idEleve, $evaluationId, $valeur, $appreciation, 'actif']);
                }
            }
        }
    }

    $pdo->commit();
    echo "Seed établissement créé avec succès avec données pédagogiques de base.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erreur seed établissement : " . $exception->getMessage() . "\n";
    exit(1);
}
