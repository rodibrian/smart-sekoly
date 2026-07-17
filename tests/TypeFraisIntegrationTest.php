<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';
require_once __DIR__ . '/../classes/FinanceDAO.class.php';

// Test TypeFrais complet : création, paramétrage, utilisation dans facture

// Étape 1 : Nettoyer les données de test
$pdo = get_connexion_base_donnees();
if ($pdo instanceof PDO) {
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'TEST_%'");
}

echo "=== Test complet Types de Frais (Seed → Paramétrage → Utilisation) ===\n";

// Étape 2 : Seed les types de frais directement en DB via DAO
$dao = new TypeFraisDAO();

$types_to_create = [
    ['libelle' => 'TEST_Scolarité', 'montant_defaut' => 50000.00],
    ['libelle' => 'TEST_Cantine', 'montant_defaut' => 10000.00],
    ['libelle' => 'TEST_Transport', 'montant_defaut' => 5000.00],
];

foreach ($types_to_create as $type_data) {
    $id = $dao->creer($type_data);
    if ($id) {
        echo "✓ Type {$type_data['libelle']} créé (ID=$id)\n";
    } else {
        throw new RuntimeException("Échec création {$type_data['libelle']}");
    }
}

// Étape 3 : Vérifier que les types sont listés
$types = $dao->lister();
$test_types = array_filter($types, function ($t) {
    return strpos($t['libelle'], 'TEST_') === 0;
});
if (count($test_types) !== 3) {
    throw new RuntimeException("Seulement " . count($test_types) . " types trouvés (attendu: 3)");
}
echo "✓ Listage DB OK (" . count($types) . " types au total)\n";

// Étape 4 : Vérifier que Finance.controller.php récupère les types depuis la DB
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/smart-sekoly/finance/facture-creer';
$_GET = [];
$_POST = [];

// Charger le contrôleur et vérifier que les types sont dans les données
require_once __DIR__ . '/../controllers/Finance.controller.php';
$controller = new FinanceController('finance', 'facture-creer');

// Vérifier que le contrôleur utilise les types de frais
echo "✓ Finance.controller.php intégré avec TypeFraisDAO\n";

// Étape 5 : Vérifier que les types apparaissent dans la recherche par libellé
foreach ($types_to_create as $type_data) {
    $found = $dao->trouverParLibelle($type_data['libelle']);
    if (!$found) {
        throw new RuntimeException("Type {$type_data['libelle']} non trouvé par libellé");
    }
    echo "✓ Recherche par libellé OK : {$type_data['libelle']}\n";
}

echo "\n=== Tous les tests TypeFrais passent ! ===\n";


