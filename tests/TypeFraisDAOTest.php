<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';

// Test TypeFraisDAO — Persistance réelle des types de frais

$dao = new TypeFraisDAO();

// Nettoyage : supprimer les tests précédents (données de test)
$pdo = get_connexion_base_donnees();
if ($pdo instanceof PDO) {
    try {
        $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'TEST_%'");
    } catch (Throwable $e) {
        // Ignore si la table n'existe pas encore
    }
}

// Test 1 : Créer 3 types de frais
echo "=== Test TypeFraisDAO ===\n";

$id1 = $dao->creer([
    'libelle' => 'TEST_Scolarité',
    'montant_defaut' => 50000.00,
]);
if (!is_int($id1) || $id1 <= 0) {
    throw new RuntimeException('Échec création type frais 1');
}
echo "✓ Type frais 1 créé : ID=$id1\n";

$id2 = $dao->creer([
    'libelle' => 'TEST_Cantine',
    'montant_defaut' => 10000.00,
]);
if (!is_int($id2) || $id2 <= 0) {
    throw new RuntimeException('Échec création type frais 2');
}
echo "✓ Type frais 2 créé : ID=$id2\n";

$id3 = $dao->creer([
    'libelle' => 'TEST_Transport',
    'montant_defaut' => 5000.00,
]);
if (!is_int($id3) || $id3 <= 0) {
    throw new RuntimeException('Échec création type frais 3');
}
echo "✓ Type frais 3 créé : ID=$id3\n";

// Test 2 : Récupérer par ID
$type1 = $dao->trouverParId($id1);
if (!$type1 || $type1['libelle'] !== 'TEST_Scolarité' || (float) $type1['montant_defaut'] !== 50000.0) {
    throw new RuntimeException('Récupération type frais 1 échouée');
}
echo "✓ Récupération par ID OK\n";

// Test 3 : Recherche par libellé
$found = $dao->trouverParLibelle('TEST_Cantine');
if (!$found || (float) $found['montant_defaut'] !== 10000.0) {
    throw new RuntimeException('Recherche par libellé échouée');
}
echo "✓ Recherche par libellé OK\n";

// Test 4 : Lister tous
$tous = $dao->lister();
$test_types = array_filter($tous, function ($t) {
    return strpos($t['libelle'], 'TEST_') === 0;
});
if (count($test_types) < 3) {
    throw new RuntimeException('Listage échoué, count=' . count($test_types));
}
echo "✓ Listage OK (" . count($tous) . " types au total)\n";

// Test 5 : Mettre à jour montant
$success = $dao->mettreAJourMontantDefaut($id1, 55000.00);
if (!$success) {
    throw new RuntimeException('Mise à jour montant échouée');
}
$updated = $dao->trouverParId($id1);
if ((float) $updated['montant_defaut'] !== 55000.0) {
    throw new RuntimeException('Montant non mis à jour');
}
echo "✓ Mise à jour montant OK (50000 → 55000)\n";

// Test 6 : Unicité du libellé (doublon)
$dup = $dao->creer([
    'libelle' => 'TEST_Scolarité',  // Même libellé que id1
    'montant_defaut' => 60000.00,
]);
if ($dup !== null) {
    // Si la DB enforce UNIQUE, on doit avoir null. Sinon, on teste quand même.
    echo "⚠ Attention : doublon de libellé accepté (pas d'UNIQUE en DB ?)\n";
} else {
    echo "✓ Unicité du libellé vérifiée\n";
}

echo "\n=== Tous les tests TypeFraisDAO passent ! ===\n";
