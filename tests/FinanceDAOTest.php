<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/FinanceDAO.class.php';
require_once __DIR__ . '/../classes/EcheancierDAO.class.php';
require_once __DIR__ . '/../classes/FactureDAO.class.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';

$pdo = get_connexion_base_donnees();
$dao = new FinanceDAO();

echo "=== Test FinanceDAO (insertFacture + insertPaiement persistance réelle) ===\n";

$unique = substr(md5(uniqid('', true)), 0, 12);
$numeroFacture = 'TEST_FIN_' . $unique;
$numeroRecu = 'TEST_FIN_REC_' . $unique;

// Cleanup unique entities
try {
    $pdo->exec("DELETE FROM paiement WHERE numero_recu LIKE 'TEST_FIN_REC_%'");
    $pdo->exec("DELETE FROM echeance WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_FIN_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_FIN_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'TEST_FIN_%'");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'TEST_FIN_Frais_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 999999");
    $pdo->exec("DELETE FROM utilisateur WHERE id_utilisateur = 999999");
    $pdo->exec("DELETE FROM personne WHERE nom = 'TEST_FIN_USER' AND prenom = 'User'");
    $pdo->exec("DELETE FROM personne WHERE nom = 'TEST_FIN' AND prenom = 'Eleve'");
} catch (Throwable $e) {
    // Ignore cleanup errors
}

unset($_SESSION['factures'], $_SESSION['paiements'], $_SESSION['caisses']);

try {
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('TEST_FIN_USER', 'User', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='TEST_FIN_USER' AND prenom='User' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne = $personne['id_personne'];
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (999999, $id_personne, 'test_fin_user', SHA2('test', 256), 'actif')");
    $id_user = 999999;

    $stmt = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (:libelle, :date_debut, :date_fin, :etat) ON DUPLICATE KEY UPDATE id_annee=id_annee');
    $stmt->execute([':libelle' => '2025-2026', ':date_debut' => '2025-09-15', ':date_fin' => '2026-06-30', ':etat' => 'active']);
    $stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
    $year = $stmt_year->fetch(PDO::FETCH_ASSOC);
    $id_annee = $year['id_annee'];

    $stmt_p = $pdo->prepare(
        'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante, chemin_stockage_documents) VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante, :chemin)'
    );
    $stmt_p->execute([':nom' => 'TEST_FIN_Etablissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'TEST_FIN', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);

    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('TEST_FIN', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='TEST_FIN' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_eleve = $personne['id_personne'];
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne_eleve, ':id_eleve' => 999999, ':matricule' => 'TEST_FIN-999999', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);

    $typeFraisDAO = new TypeFraisDAO();
    $id_type = $typeFraisDAO->creer(['libelle' => 'TEST_FIN_Frais_' . $unique, 'montant_defaut' => 2500.00]);
    if (!$id_type) {
        throw new RuntimeException('La création du type de frais a échoué.');
    }

    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(999999, [$id_type], $id_annee, $numeroFacture);
    if (!$id_facture) {
        throw new RuntimeException('La création de facture a échoué.');
    }
    echo "✓ Facture créée (ID=$id_facture)\n";

    $echeancierDAO = new EcheancierDAO();
    $echances = [[
        'date' => date('Y-m-d', strtotime('+7 days')),
        'montant' => 2500.00,
    ]];
    $success = $echeancierDAO->creer($id_facture, $echances, $id_user);
    if (!$success) {
        throw new RuntimeException('La création de l\'échéancier a échoué.');
    }

    $list = $echeancierDAO->listerParFacture($id_facture);
    if (count($list) !== 1 || empty($list[0]['id_echeance'])) {
        throw new RuntimeException('Impossible de récupérer l\'échéance créée.');
    }
    $id_echeance = (int) $list[0]['id_echeance'];
    echo "✓ Échéance créée (ID=$id_echeance)\n";

    $id_caisse = $dao->insertCaisse([
        'date_caisse' => date('Y-m-d'),
        'fond_de_caisse' => 1500.00,
    ]);
    if (!is_int($id_caisse) || $id_caisse <= 0) {
        throw new RuntimeException('insertCaisse did not return a valid id.');
    }
    echo "✓ Caisse créée (ID=$id_caisse)\n";

    $idP = $dao->insertPaiement([
        'id_echeance' => $id_echeance,
        'numero_recu' => $numeroRecu,
        'date_paiement' => date('Y-m-d H:i:s'),
        'montant' => 2500.00,
        'mode_paiement' => 'espece',
        'statut' => 'actif',
        'id_utilisateur_enregistrement' => $id_user,
        'id_caisse' => $id_caisse,
    ]);

    if (!is_int($idP) || $idP <= 0) {
        throw new RuntimeException('insertPaiement did not return a valid id.');
    }

    $stmt = $pdo->prepare('SELECT * FROM paiement WHERE id_paiement = :id');
    $stmt->execute([':id' => $idP]);
    $paiement = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$paiement) {
        throw new RuntimeException('Le paiement inséré n\'a pas été trouvé en base.');
    }
    if ((int) $paiement['id_echeance'] !== $id_echeance || (int) $paiement['id_utilisateur_enregistrement'] !== $id_user || (int) $paiement['id_caisse'] !== $id_caisse) {
        throw new RuntimeException('Le paiement en base n\'a pas les bons identifiants de référence.');
    }

    echo "✓ Paiement inséré réellement en base (ID=$idP)\n";
    echo "FinanceDAO tests: OK\n";
} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}