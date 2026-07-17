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
    $pdo->exec("DELETE FROM echeance WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_RETARD_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_RETARD_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'TEST_RETARD_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 666778");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'RETARD_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test Écheancier Retard (Statut EN_RETARD Calculé) ===\n";

try {
    // Setup utilisateur
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('RETARD_USER', 'User', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='RETARD_USER' AND prenom='User' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_user = $personne['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (666778, $id_personne_user, 'retard_user', SHA2('test', 256), 'actif')");
    $id_user = 666778;

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
    $stmt_p->execute([':nom' => 'RETARD_Établissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'RETARD', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);

    // Élève
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('RETARD', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='RETARD' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_eleve = $personne['id_personne'];
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne_eleve, ':id_eleve' => 666778, ':matricule' => 'RETARD-666778', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);

    // Type de frais
    $typeFraisDAO = new TypeFraisDAO();
    $id_type = $typeFraisDAO->creer(['libelle' => 'RETARD_Frais', 'montant_defaut' => 200000.00]);

    // Facture
    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(666778, [$id_type], $id_annee, 'TEST_RETARD_000001');
    echo "✓ Facture créée (200k CFA)\n";

    // Créer échéancier avec dates dans le PASSÉ
    $echeancierDAO = new EcheancierDAO();
    $echances = [
        ['date' => date('Y-m-d', strtotime('-30 days')), 'montant' => 100000],  // 30 jours dans le passé
        ['date' => date('Y-m-d', strtotime('-5 days')), 'montant' => 100000],   // 5 jours dans le passé
    ];
    $echeancierDAO->creer($id_facture, $echances, $id_user);
    echo "✓ Échéancier créé avec dates dans le passé\n";

    // TEST 1 : Vérifier que les statuts initiaux sont "en_retard" (pas payées, date passée)
    echo "\n--- TEST 1 : Statut en_retard (pas payées, date passée) ---\n";
    $list = $echeancierDAO->listerParFacture($id_facture);
    $ech1 = $list[0];
    $ech2 = $list[1];

    if ((float) $ech1['montant_paye'] != 0) {
        throw new RuntimeException("1ère échéance devrait être impayée");
    }
    
    // Recalculer le statut pour s'assurer qu'il est correct
    $statut1 = $echeancierDAO->recalculerStatut($ech1['id_echeance']);
    if ($statut1 != 'en_retard') {
        throw new RuntimeException("1ère statut incorrect : $statut1 != en_retard");
    }
    echo "✓ 1ère échéance : statut=en_retard (30 jours de retard)\n";

    $statut2 = $echeancierDAO->recalculerStatut($ech2['id_echeance']);
    if ($statut2 != 'en_retard') {
        throw new RuntimeException("2ère statut incorrect : $statut2 != en_retard");
    }
    echo "✓ 2ère échéance : statut=en_retard (5 jours de retard)\n";

    // TEST 2 : Paiement partiel sur 1ère (50k) → statut doit rester en_retard (date passée, pas payée intégralement)
    echo "\n--- TEST 2 : Paiement partiel sur en_retard ---\n";
    $echeancierDAO->impurerPaiement($id_facture, 50000);

    $ech1_updated = $echeancierDAO->trouverParId($ech1['id_echeance']);
    if ((float) $ech1_updated['montant_paye'] != 50000) {
        throw new RuntimeException("Montant payé incorrect");
    }
    if ($ech1_updated['statut'] != 'en_retard') {
        throw new RuntimeException("Statut avec paiement partiel incorrect : " . $ech1_updated['statut'] . " != en_retard");
    }
    echo "✓ Après paiement 50k : statut reste en_retard (date passée, montant insuffisant)\n";

    // TEST 3 : Compléter le paiement de 1ère (50k supplémentaires = 100k total) → statut=payée
    echo "\n--- TEST 3 : Paiement complétant l'échéance ---\n";
    $echeancierDAO->impurerPaiement($id_facture, 50000);

    $ech1_paid = $echeancierDAO->trouverParId($ech1['id_echeance']);
    if ((float) $ech1_paid['montant_paye'] != 100000) {
        throw new RuntimeException("1ère non complètement payée");
    }
    if ($ech1_paid['statut'] != 'payee') {
        throw new RuntimeException("1ère statut incorrect après paiement complet : " . $ech1_paid['statut'] . " != payee");
    }
    echo "✓ Après paiement total : statut=payee (100k payés)\n";

    // Vérifier que la 2ère est toujours en retard
    $ech2_check = $echeancierDAO->trouverParId($ech2['id_echeance']);
    if ($ech2_check['statut'] != 'en_retard') {
        throw new RuntimeException("2ère statut changé incorrectement");
    }
    echo "✓ 2ère reste en_retard (0k payés)\n";

    echo "\n=== Tous les tests Retard passent ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
