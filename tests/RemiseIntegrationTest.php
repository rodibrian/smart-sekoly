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
    $pdo->exec("DELETE FROM facture_remise WHERE id_remise IN (SELECT id_remise FROM remise WHERE motif LIKE 'INTEG_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'INTEG_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'INTEG_%'");
    $pdo->exec("DELETE FROM remise WHERE motif LIKE 'INTEG_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 666666");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'INTEG_%'");
    $pdo->exec("DELETE FROM parametrage_etablissement WHERE nom_etablissement LIKE 'INTEG_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test Intégration Remise (Chaîne Complète) ===\n";

try {
    // Créer une personne et utilisateur
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('INTEG_USER', 'User', 'F')");
    $stmt_personne_user = $pdo->query("SELECT id_personne FROM personne WHERE nom='INTEG_USER' AND prenom='User' LIMIT 1");
    $personne_user = $stmt_personne_user->fetch(PDO::FETCH_ASSOC);
    $id_personne_user = $personne_user['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (999996, $id_personne_user, 'integ_user', SHA2('test', 256), 'actif')");
    $id_user = 999996;

    // Créer année
    $stmt = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (:libelle, :date_debut, :date_fin, :etat) ON DUPLICATE KEY UPDATE id_annee=id_annee');
    $stmt->execute([':libelle' => '2025-2026', ':date_debut' => '2025-09-15', ':date_fin' => '2026-06-30', ':etat' => 'active']);
    $stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
    $year = $stmt_year->fetch(PDO::FETCH_ASSOC);
    $id_annee = $year['id_annee'];
    echo "✓ Année scolaire créée (ID=$id_annee)\n";

    // Créer paramétrage
    $stmt_p = $pdo->prepare(
        'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante, chemin_stockage_documents) VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante, :chemin)'
    );
    $stmt_p->execute([':nom' => 'INTEG_Établissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'INTEG', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);
    echo "✓ Paramétrage créé\n";

    // Créer élève
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('INTEG', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='INTEG' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne = $personne['id_personne'];
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne, ':id_eleve' => 666666, ':matricule' => 'INTEG-666666', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);
    echo "✓ Élève créé (ID=666666)\n";

    // Créer types de frais
    $typeFraisDAO = new TypeFraisDAO();
    $id_type1 = $typeFraisDAO->creer(['libelle' => 'INTEG_Scolarité', 'montant_defaut' => 80000.00]);
    $id_type2 = $typeFraisDAO->creer(['libelle' => 'INTEG_Cantine', 'montant_defaut' => 20000.00]);
    echo "✓ Types de frais créés (80k + 20k = 100k CFA)\n";

    // Créer facture
    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(666666, [$id_type1, $id_type2], $id_annee, 'INTEG-000001');
    if (!$id_facture) {
        throw new RuntimeException("Création facture échouée");
    }
    $facture = $factureDAO->trouverParId($id_facture);
    $montant_original = $facture['montant_total'];
    echo "✓ Facture créée (ID=$id_facture, montant=" . $montant_original . " CFA)\n";

    // Créer et valider remise
    $remiseDAO = new RemiseDAO();
    $id_remise = $remiseDAO->creer($id_user, 'pourcentage', 10, 'INTEG_Remise_10pct');
    if (!$id_remise) {
        throw new RuntimeException("Création remise échouée");
    }
    echo "✓ Remise créée (ID=$id_remise, 10%)\n";

    $success = $remiseDAO->valider($id_remise, $id_user);
    if (!$success) {
        throw new RuntimeException("Validation remise échouée");
    }
    echo "✓ Remise validée\n";

    // Appliquer remise à facture
    $success = $remiseDAO->appliquerAFacture($id_remise, $id_facture);
    if (!$success) {
        throw new RuntimeException("Application remise échouée");
    }
    echo "✓ Remise appliquée à facture\n";

    // Vérifier le montant après remise
    $remise = $remiseDAO->trouverParId($id_remise);
    $montant_apres = $remiseDAO->calculerMontantApres($montant_original, $remise['type_remise'], $remise['valeur_remise']);
    
    if ($montant_apres != 90000) {
        throw new RuntimeException("Calcul nouveau montant incorrect : $montant_apres != 90000");
    }
    echo "✓ Montant après remise : " . $montant_original . " - 10% = " . $montant_apres . " CFA\n";

    // Vérifier audit trail via JournalAudit
    $stmt_audit = $pdo->query("SELECT COUNT(*) as count FROM journal_audit WHERE table_concernee='remise' AND id_enregistrement_concerne=$id_remise");
    $audit_count = $stmt_audit->fetch(PDO::FETCH_ASSOC)['count'];
    if ($audit_count < 2) {
        throw new RuntimeException("Journal d'audit incomplet : $audit_count événement(s) au lieu d'au moins 2");
    }
    echo "✓ Audit trail vérifié : " . $audit_count . " événement(s) enregistré(s)\n";

    echo "\n=== Test d'Intégration Remise réussi ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
