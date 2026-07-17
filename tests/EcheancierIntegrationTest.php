<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/EcheancierDAO.class.php';
require_once __DIR__ . '/../classes/FactureDAO.class.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';

$pdo = get_connexion_base_donnees();

// Cleanup
try {
    $pdo->exec("DELETE FROM echeance WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'INTEG_ECH_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'INTEG_ECH_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'INTEG_ECH_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 555778");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'INTEG_ECH_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test Intégration Écheancier (Chaîne Complète) ===\n";

try {
    // Setup utilisateur
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('INTEG_ECH_USER', 'User', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='INTEG_ECH_USER' AND prenom='User' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_user = $personne['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (555778, $id_personne_user, 'integ_ech_user', SHA2('test', 256), 'actif')");
    $id_user = 555778;

    // Année
    $stmt = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (:libelle, :date_debut, :date_fin, :etat) ON DUPLICATE KEY UPDATE id_annee=id_annee');
    $stmt->execute([':libelle' => '2025-2026', ':date_debut' => '2025-09-15', ':date_fin' => '2026-06-30', ':etat' => 'active']);
    $stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
    $year = $stmt_year->fetch(PDO::FETCH_ASSOC);
    $id_annee = $year['id_annee'];

    // Paramétrage
    $stmt_p = $pdo->prepare(
        'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante, chemin_stockage_documents) VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante, :chemin)'
    );
    $stmt_p->execute([':nom' => 'INTEG_ECH_Établissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'INTEG_ECH', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);

    // Élève
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('INTEG_ECH', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='INTEG_ECH' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_eleve = $personne['id_personne'];
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne_eleve, ':id_eleve' => 555778, ':matricule' => 'INTEG_ECH-555778', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);

    // Type de frais
    $typeFraisDAO = new TypeFraisDAO();
    $id_type1 = $typeFraisDAO->creer(['libelle' => 'INTEG_ECH_Scolarité', 'montant_defaut' => 120000.00]);
    $id_type2 = $typeFraisDAO->creer(['libelle' => 'INTEG_ECH_Cantine', 'montant_defaut' => 80000.00]);

    // Facture
    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(555778, [$id_type1, $id_type2], $id_annee, 'INTEG_ECH_000001');
    $facture = $factureDAO->trouverParId($id_facture);
    $montant_total = (float) $facture['montant_total'];
    
    if ($montant_total != 200000) {
        throw new RuntimeException("Montant facture incorrect : $montant_total != 200000");
    }
    echo "✓ Facture créée (200k CFA : 120k scolarité + 80k cantine)\n";

    // Créer échéancier 4 mensualités
    $echeancierDAO = new EcheancierDAO();
    $echances = [
        ['date' => date('Y-m-d', strtotime('+15 days')), 'montant' => 50000],
        ['date' => date('Y-m-d', strtotime('+45 days')), 'montant' => 50000],
        ['date' => date('Y-m-d', strtotime('+75 days')), 'montant' => 50000],
        ['date' => date('Y-m-d', strtotime('+105 days')), 'montant' => 50000],
    ];
    $success = $echeancierDAO->creer($id_facture, $echances, $id_user);
    if (!$success) {
        throw new RuntimeException("Création échéancier échouée");
    }
    echo "✓ Échéancier créé : 4 × 50k CFA\n";

    // Vérifier état initial
    $etat = $echeancierDAO->calculerEtatGlobal($id_facture);
    if ($etat['a_venir'] != 4 || $etat['montant_total_prevu'] != 200000) {
        throw new RuntimeException("État initial incorrect");
    }
    echo "✓ État initial : 4 à_venir, 200k prévu, 0 payé\n";

    // Paiement 1 : 50k (complète 1ère)
    echo "\n--- Paiement 1 : 50k ---\n";
    $echeancierDAO->impurerPaiement($id_facture, 50000);
    $etat = $echeancierDAO->calculerEtatGlobal($id_facture);
    if ($etat['payees'] != 1 || $etat['a_venir'] != 3 || $etat['montant_total_paye'] != 50000) {
        throw new RuntimeException("État après paiement 1 incorrect");
    }
    echo "✓ 1ère payée, 3 à_venir, 50k payés\n";

    // Paiement 2 : 100k (complète 2ère + 3ère)
    echo "\n--- Paiement 2 : 100k ---\n";
    $echeancierDAO->impurerPaiement($id_facture, 100000);
    $etat = $echeancierDAO->calculerEtatGlobal($id_facture);
    if ($etat['payees'] != 3 || $etat['partielles'] != 0 || $etat['a_venir'] != 1 || $etat['montant_total_paye'] != 150000) {
        throw new RuntimeException("État après paiement 2 incorrect : payées=" . $etat['payees'] . ", partielles=" . $etat['partielles']);
    }
    echo "✓ 3 payées, 1 à_venir, 150k payés\n";

    // Paiement 3 : 50k (complète 4ère)
    echo "\n--- Paiement 3 : 50k ---\n";
    $echeancierDAO->impurerPaiement($id_facture, 50000);
    $etat = $echeancierDAO->calculerEtatGlobal($id_facture);
    if ($etat['payees'] != 4 || $etat['a_venir'] != 0 || $etat['montant_total_paye'] != 200000) {
        throw new RuntimeException("État après paiement 3 incorrect");
    }
    echo "✓ 4 payées, 0 à_venir, 200k payés\n";

    // Vérifier montant non payé
    $non_paye = $echeancierDAO->calculerMontantNonPaye($id_facture);
    if ($non_paye != 0) {
        throw new RuntimeException("Montant non payé incorrect : $non_paye != 0");
    }
    echo "✓ Montant non payé = 0 (facture complètement payée)\n";

    // Vérifier dernière échéance
    $list = $echeancierDAO->listerParFacture($id_facture);
    $ech_last = $list[3];
    if ($ech_last['statut'] != 'payee' || (float) $ech_last['montant_paye'] != 50000) {
        throw new RuntimeException("Dernière échéance incorrecte : statut=" . $ech_last['statut'] . ", montant_paye=" . $ech_last['montant_paye']);
    }
    echo "✓ Dernière échéance : payee, 50k payé\n";

    echo "\n=== Test d'Intégration Écheancier réussi ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
