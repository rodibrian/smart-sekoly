<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/RemiseDAO.class.php';
require_once __DIR__ . '/../classes/FactureDAO.class.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';

$pdo = get_connexion_base_donnees();

// Cleanup
try {
    $pdo->exec("DELETE FROM facture_remise WHERE id_remise IN (SELECT id_remise FROM remise WHERE motif LIKE 'TEST_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'VALIDATION_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'VALIDATION_%'");
    $pdo->exec("DELETE FROM remise WHERE motif LIKE 'TEST_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 777777");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'VAL_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test Validation Remise (Blocage + Nominal) ===\n";

try {
    // Créer une personne et utilisateur de test
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('VALIDATION_USER', 'User', 'F')");
    $stmt_personne_user = $pdo->query("SELECT id_personne FROM personne WHERE nom='VALIDATION_USER' AND prenom='User' LIMIT 1");
    $personne_user = $stmt_personne_user->fetch(PDO::FETCH_ASSOC);
    $id_personne_user = $personne_user['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (999997, $id_personne_user, 'val_user', SHA2('test', 256), 'actif')");
    $id_user = 999997;

    // Créer une année
    $stmt = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (:libelle, :date_debut, :date_fin, :etat) ON DUPLICATE KEY UPDATE id_annee=id_annee');
    $stmt->execute([':libelle' => '2025-2026', ':date_debut' => '2025-09-15', ':date_fin' => '2026-06-30', ':etat' => 'active']);
    $stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
    $year = $stmt_year->fetch(PDO::FETCH_ASSOC);
    $id_annee = $year['id_annee'];

    // Créer paramétrage
    $stmt_param = $pdo->query("SELECT id_parametrage FROM parametrage_etablissement LIMIT 1");
    $param_row = $stmt_param->fetch(PDO::FETCH_ASSOC);
    if (!$param_row) {
        $stmt_p = $pdo->prepare(
            'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante, chemin_stockage_documents) VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante, :chemin)'
        );
        $stmt_p->execute([':nom' => 'VAL_Établissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'VALIDATION', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);
    }

    // Créer un élève
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('VAL', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='VAL' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne = $personne['id_personne'];
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne, ':id_eleve' => 777777, ':matricule' => 'VAL-777777', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);
    echo "✓ Élève créé\n";

    // Créer un type de frais
    $typeFraisDAO = new TypeFraisDAO();
    $id_type = $typeFraisDAO->creer(['libelle' => 'VAL_Frais', 'montant_defaut' => 100000.00]);
    echo "✓ Type de frais créé (100k CFA)\n";

    // Créer une facture
    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(777777, [$id_type], $id_annee, 'VALIDATION-000001');
    echo "✓ Facture créée (ID=$id_facture, montant=100k CFA)\n";

    $remiseDAO = new RemiseDAO();

    // ========== TEST NÉGATIF CRUCIAL ==========
    // Créer une remise à l'état 'attente'
    $id_remise_attente = $remiseDAO->creer($id_user, 'pourcentage', 20, 'TEST_20pct_ATTENTE');
    echo "\n✓ Remise créée à l'état 'attente' (ID=$id_remise_attente)\n";

    // TENTATIVE D'APPLICATION SANS VALIDATION → DOIT ÉCHOUER
    echo "\n--- TEST NÉGATIF : Tentative d'appliquer remise non validée ---\n";
    $resultat = $remiseDAO->appliquerAFacture($id_remise_attente, $id_facture);
    
    if ($resultat === true) {
        throw new RuntimeException("ERREUR CRITIQUE : Remise non validée a pu être appliquée ! Blocage défaillant !");
    }
    echo "✓ BLOCAGE EFFECTIF : Remise non validée a été rejetée (retour = false)\n";

    // Vérifier que la remise n'est PAS liée à la facture
    $remises_facture = $remiseDAO->listerParFacture($id_facture);
    if (!empty($remises_facture)) {
        throw new RuntimeException("ERREUR : Remise non validée s'est quand même appliquée !");
    }
    echo "✓ Vérification : aucune remise liée à la facture\n";

    // ========== TEST NOMINAL : Création → Validation → Application ==========
    echo "\n--- TEST NOMINAL : Création → Validation → Application ---\n";

    // Créer une nouvelle remise
    $id_remise_ok = $remiseDAO->creer($id_user, 'montant_fixe', 15000, 'TEST_15k_OK');
    echo "✓ Remise créée (ID=$id_remise_ok, montant fixe=15k)\n";

    // Valider la remise
    $success = $remiseDAO->valider($id_remise_ok, $id_user);
    if (!$success) {
        throw new RuntimeException("Validation échouée");
    }
    echo "✓ Remise validée\n";

    // Appliquer la remise à la facture
    $success = $remiseDAO->appliquerAFacture($id_remise_ok, $id_facture);
    if (!$success) {
        throw new RuntimeException("Application remise validée échouée");
    }
    echo "✓ Remise appliquée à la facture\n";

    // Vérifier l'application
    $remises_facture = $remiseDAO->listerParFacture($id_facture);
    if (empty($remises_facture) || $remises_facture[0]['id_remise'] != $id_remise_ok) {
        throw new RuntimeException("Remise non trouvée sur facture");
    }
    echo "✓ Remise vérifiée sur facture\n";

    // Calculer le nouveau montant
    $remise = $remises_facture[0];
    $montant_original = 100000;
    $montant_apres = $remiseDAO->calculerMontantApres($montant_original, $remise['type_remise'], $remise['valeur_remise']);
    if ($montant_apres != 85000) {
        throw new RuntimeException("Calcul nouveau montant incorrect : $montant_apres != 85000");
    }
    echo "✓ Nouveau montant après remise : " . $montant_original . " - 15000 = " . $montant_apres . " CFA\n";

    echo "\n=== Tous les tests Validation Remise passent ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
