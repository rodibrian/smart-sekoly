<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/RemiseDAO.class.php';

$pdo = get_connexion_base_donnees();

// Cleanup
try {
    $pdo->exec("DELETE FROM facture_remise WHERE id_remise IN (SELECT id_remise FROM remise WHERE motif LIKE 'TEST_%')");
    $pdo->exec("DELETE FROM remise WHERE motif LIKE 'TEST_%'");
} catch (Throwable $e) {
    // Ignore
}

echo "=== Test RemiseDAO ===\n";

try {
    // Créer une personne et utilisateur de test
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('TEST_REMISE', 'User', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='TEST_REMISE' AND prenom='User' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne = $personne['id_personne'];
    
    $pdo->exec("INSERT IGNORE INTO utilisateur (id_utilisateur, id_personne, identifiant, mot_de_passe_hash, statut_compte) VALUES (999998, $id_personne, 'test_remise', SHA2('test', 256), 'actif')");
    $id_createur = 999998;
    $id_validateur = 999998;

    // Test 1 : Créer une remise (pourcentage)
    $remiseDAO = new RemiseDAO();
    $id_remise_1 = $remiseDAO->creer($id_createur, 'pourcentage', 10, 'TEST_Remise_10pct');
    if (!$id_remise_1) {
        throw new RuntimeException("Création remise échouée");
    }
    echo "✓ Remise créée (ID=$id_remise_1, type=pourcentage, valeur=10, statut=attente)\n";

    // Test 2 : Récupérer la remise
    $remise = $remiseDAO->trouverParId($id_remise_1);
    if (!$remise || $remise['statut'] !== 'attente') {
        throw new RuntimeException("Remise non trouvée ou mauvais statut");
    }
    echo "✓ Remise récupérée : statut=" . $remise['statut'] . "\n";

    // Test 3 : Calculer montant après remise (pourcentage)
    $montant_original = 100000;
    $montant_apres = $remiseDAO->calculerMontantApres($montant_original, 'pourcentage', 10);
    if ($montant_apres != 90000) {
        throw new RuntimeException("Calcul pourcentage incorrect : $montant_apres != 90000");
    }
    echo "✓ Calcul pourcentage OK : 100000 - 10% = " . $montant_apres . " CFA\n";

    // Test 4 : Créer une remise (montant fixe)
    $id_remise_2 = $remiseDAO->creer($id_createur, 'montant_fixe', 5000, 'TEST_Remise_5k');
    if (!$id_remise_2) {
        throw new RuntimeException("Création remise montant fixe échouée");
    }
    echo "✓ Remise créée (ID=$id_remise_2, type=montant_fixe, valeur=5000)\n";

    // Test 5 : Calculer montant après remise (montant fixe)
    $montant_apres = $remiseDAO->calculerMontantApres($montant_original, 'montant_fixe', 5000);
    if ($montant_apres != 95000) {
        throw new RuntimeException("Calcul montant fixe incorrect");
    }
    echo "✓ Calcul montant fixe OK : 100000 - 5000 = " . $montant_apres . " CFA\n";

    // Test 6 : Lister remises en attente
    $remises_attente = $remiseDAO->listerEnAttenteValidation();
    if (count($remises_attente) < 2) {
        throw new RuntimeException("Remises en attente non listées");
    }
    echo "✓ " . count($remises_attente) . " remise(s) en attente trouvée(s)\n";

    // Test 7 : Valider une remise
    $success = $remiseDAO->valider($id_remise_1, $id_validateur);
    if (!$success) {
        throw new RuntimeException("Validation échouée");
    }
    $remise_validee = $remiseDAO->trouverParId($id_remise_1);
    if ($remise_validee['statut'] !== 'approuvee') {
        throw new RuntimeException("Statut non mis à jour");
    }
    echo "✓ Remise validée : statut=" . $remise_validee['statut'] . "\n";

    // Test 8 : Rejeter une remise
    $success = $remiseDAO->rejeter($id_remise_2, $id_validateur);
    if (!$success) {
        throw new RuntimeException("Rejet échoué");
    }
    $remise_rejetee = $remiseDAO->trouverParId($id_remise_2);
    if ($remise_rejetee['statut'] !== 'rejetee') {
        throw new RuntimeException("Statut rejet non mis à jour");
    }
    echo "✓ Remise rejetée : statut=" . $remise_rejetee['statut'] . "\n";

    echo "\n=== Tous les tests RemiseDAO passent ! ===\n";

} catch (Throwable $e) {
    echo "✗ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
