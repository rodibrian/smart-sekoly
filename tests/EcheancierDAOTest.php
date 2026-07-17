<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/EcheancierDAO.class.php';

$pdo = get_connexion_base_donnees();

// Cleanup
try {
    $pdo->exec("DELETE FROM echeance WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_ECH_%')");
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'TEST_ECH_%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'TEST_ECH_%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 888888");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'ECH_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test EcheancierDAO (CRUD + Répartition) ===\n";

try {
    // Créer utilisateur
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('ECH_USER', 'User', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='ECH_USER' AND prenom='User' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne = $personne['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (888888, $id_personne, 'ech_user', SHA2('test', 256), 'actif')");
    $id_user = 888888;

    // Créer une année et paramétrage
    $stmt = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (:libelle, :date_debut, :date_fin, :etat) ON DUPLICATE KEY UPDATE id_annee=id_annee');
    $stmt->execute([':libelle' => '2025-2026', ':date_debut' => '2025-09-15', ':date_fin' => '2026-06-30', ':etat' => 'active']);
    $stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
    $year = $stmt_year->fetch(PDO::FETCH_ASSOC);
    $id_annee = $year['id_annee'];

    // Créer paramétrage
    $stmt_p = $pdo->prepare(
        'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante, chemin_stockage_documents) VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante, :chemin)'
    );
    $stmt_p->execute([':nom' => 'ECH_Établissement', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'ECH', ':annee_courante' => '2025-2026', ':chemin' => '/documents']);

    // Créer élève
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('ECH', 'Eleve', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='ECH' AND prenom='Eleve' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne_eleve = $personne['id_personne'];
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne_eleve, ':id_eleve' => 888888, ':matricule' => 'ECH-888888', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);

    // Créer type de frais
    require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';
    $typeFraisDAO = new TypeFraisDAO();
    $id_type = $typeFraisDAO->creer(['libelle' => 'ECH_Frais', 'montant_defaut' => 300000.00]);

    // Créer facture
    require_once __DIR__ . '/../classes/FactureDAO.class.php';
    $factureDAO = new FactureDAO();
    $id_facture = $factureDAO->creerFacture(888888, [$id_type], $id_annee, 'TEST_ECH_000001');
    echo "✓ Facture créée (ID=$id_facture, montant=300k CFA)\n";

    // Test 1 : Créer un échéancier 3 échéances
    $echeancierDAO = new EcheancierDAO();
    $echances = [
        ['date' => date('Y-m-d', strtotime('+30 days')), 'montant' => 100000],
        ['date' => date('Y-m-d', strtotime('+60 days')), 'montant' => 100000],
        ['date' => date('Y-m-d', strtotime('+90 days')), 'montant' => 100000],
    ];
    
    $success = $echeancierDAO->creer($id_facture, $echances, $id_user);
    if (!$success) {
        throw new RuntimeException("Création échéancier échouée");
    }
    echo "✓ Échéancier créé : 3 échéances de 100k CFA\n";

    // Test 2 : Récupérer les échéances
    $list = $echeancierDAO->listerParFacture($id_facture);
    if (count($list) != 3) {
        throw new RuntimeException("Nombre d'échéances incorrect : " . count($list) . " != 3");
    }
    echo "✓ 3 échéances récupérées\n";

    // Test 3 : Vérifier les montants
    $total = 0;
    foreach ($list as $ech) {
        if ($ech['montant_prevu'] != 100000) {
            throw new RuntimeException("Montant incorrectement réparti");
        }
        if ($ech['statut'] != 'a_venir') {
            throw new RuntimeException("Statut incorrect pour échéance à venir");
        }
        $total += (float) $ech['montant_prevu'];
    }
    if ($total != 300000) {
        throw new RuntimeException("Montant total incorrect : $total != 300000");
    }
    echo "✓ Montants et statuts initiaux vérifiés (tous à_venir)\n";

    // Test 4 : Calculer l'état global
    $etat = $echeancierDAO->calculerEtatGlobal($id_facture);
    if ($etat['a_venir'] != 3 || $etat['payees'] != 0 || $etat['montant_total_prevu'] != 300000) {
        throw new RuntimeException("État global incorrect");
    }
    echo "✓ État global calculé : 3 à_venir, 300k prévu\n";

    echo "\n=== Tous les tests EcheancierDAO passent ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
