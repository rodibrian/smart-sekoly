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
    $pdo->exec("DELETE FROM echeance WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_PAYE_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_PAYE_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'TEST_PAYE_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 777778");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'PAYE_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test Écheancier Paiement (Imputation + Débordement) ===\n";

try {
    // Setup utilisateur
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('PAYE_USER', 'User', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='PAYE_USER' AND prenom='User' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_user = $personne['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (777778, $id_personne_user, 'paye_user', SHA2('test', 256), 'actif')");
    $id_user = 777778;

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
    $stmt_p->execute([':nom' => 'PAYE_Établissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'PAYE', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);

    // Élève
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('PAYE', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='PAYE' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_eleve = $personne['id_personne'];
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne_eleve, ':id_eleve' => 777778, ':matricule' => 'PAYE-777778', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);

    // Type de frais
    $typeFraisDAO = new TypeFraisDAO();
    $id_type = $typeFraisDAO->creer(['libelle' => 'PAYE_Frais', 'montant_defaut' => 300000.00]);

    // Facture
    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(777778, [$id_type], $id_annee, 'TEST_PAYE_000001');
    echo "✓ Facture créée (300k CFA)\n";

    // Créer échéancier
    $echeancierDAO = new EcheancierDAO();
    $echances = [
        ['date' => date('Y-m-d', strtotime('+30 days')), 'montant' => 100000],
        ['date' => date('Y-m-d', strtotime('+60 days')), 'montant' => 100000],
        ['date' => date('Y-m-d', strtotime('+90 days')), 'montant' => 100000],
    ];
    $echeancierDAO->creer($id_facture, $echances, $id_user);
    echo "✓ Échéancier créé (3 × 100k)\n";

    // TEST 1 : Imputation partielle sur 1ère échéance (50k sur 100k)
    echo "\n--- TEST 1 : Paiement partiel (50k sur 1ère échéance) ---\n";
    $success = $echeancierDAO->impurerPaiement($id_facture, 50000);
    if (!$success) {
        throw new RuntimeException("Imputation paiement échouée");
    }
    
    $list = $echeancierDAO->listerParFacture($id_facture);
    $ech1 = $list[0];
    if ((float) $ech1['montant_paye'] != 50000) {
        throw new RuntimeException("Montant payé incorrect : " . $ech1['montant_paye'] . " != 50000");
    }
    if ($ech1['statut'] != 'partielle') {
        throw new RuntimeException("Statut incorrect : " . $ech1['statut'] . " != partielle");
    }
    echo "✓ 50k imputés à 1ère échéance (statut=partielle)\n";

    // TEST 2 : Imputation qui déborde (80k : complète 1ère + déborde 2ère de 30k)
    echo "\n--- TEST 2 : Paiement avec débordement (80k) ---\n";
    $success = $echeancierDAO->impurerPaiement($id_facture, 80000);
    if (!$success) {
        throw new RuntimeException("Imputation débordement échouée");
    }

    $list = $echeancierDAO->listerParFacture($id_facture);
    $ech1 = $list[0];
    $ech2 = $list[1];
    $ech3 = $list[2];

    // 1ère devrait être payée (50k + 50k = 100k)
    if ((float) $ech1['montant_paye'] != 100000) {
        throw new RuntimeException("1ère échéance non complète : " . $ech1['montant_paye'] . " != 100000");
    }
    if ($ech1['statut'] != 'payee') {
        throw new RuntimeException("1ère statut incorrect : " . $ech1['statut'] . " != payee");
    }
    echo "✓ 1ère échéance complète (statut=payee)\n";

    // 2ère devrait avoir 30k (débordement)
    if ((float) $ech2['montant_paye'] != 30000) {
        throw new RuntimeException("2ère échéance incorrecte : " . $ech2['montant_paye'] . " != 30000");
    }
    if ($ech2['statut'] != 'partielle') {
        throw new RuntimeException("2ère statut incorrect : " . $ech2['statut'] . " != partielle");
    }
    echo "✓ 2ère échéance partielle (30k payés, statut=partielle)\n";

    // 3ère à 0
    if ((float) $ech3['montant_paye'] != 0) {
        throw new RuntimeException("3ère échéance incorrecte : " . $ech3['montant_paye'] . " != 0");
    }
    if ($ech3['statut'] != 'a_venir') {
        throw new RuntimeException("3ère statut incorrect : " . $ech3['statut'] . " != a_venir");
    }
    echo "✓ 3ère échéance à venir (0k payés, statut=a_venir)\n";

    // TEST 3 : Vérifier montant non payé
    $non_paye = $echeancierDAO->calculerMontantNonPaye($id_facture);
    // 300k - 100k (ech1) - 30k (ech2) = 170k
    if ($non_paye != 170000) {
        throw new RuntimeException("Montant non payé incorrect : $non_paye != 170000");
    }
    echo "✓ Montant non payé calculé : " . $non_paye . " CFA\n";

    echo "\n=== Tous les tests Paiement/Imputation passent ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
