<?php
/**
 * Tests pour le module Rapports
 * Validation des 5 actions: index, academiques, financiers, personnalises, ministere
 */

require_once __DIR__ . '/../index.php';

$tests_passed = 0;
$tests_total = 0;

// Test 1: Index - Tableau de bord des rapports
$tests_total++;
$_GET['action'] = 'index';
ob_start();
$controller = new RapportsController('rapports', 'index');
$controller->executer();
$output = ob_get_clean();

if (strpos($output, 'Rapports et Statistiques') !== false && strpos($output, 'Rapports Académiques') !== false) {
    echo "✅ Test Rapports Index : OK\n";
    $tests_passed++;
} else {
    echo "❌ Test Rapports Index : FAILED\n";
}

// Test 2: Rapports Académiques
$tests_total++;
$_GET['action'] = 'academiques';
ob_start();
$controller = new RapportsController('rapports', 'academiques');
$controller->executer();
$output = ob_get_clean();

if (strpos($output, 'Rapports Académiques') !== false && strpos($output, 'Classe') !== false) {
    echo "✅ Test Rapports Académiques : OK\n";
    $tests_passed++;
} else {
    echo "❌ Test Rapports Académiques : FAILED\n";
}

// Test 3: Rapports Financiers
$tests_total++;
$_GET['action'] = 'financiers';
ob_start();
$controller = new RapportsController('rapports', 'financiers');
$controller->executer();
$output = ob_get_clean();

if (strpos($output, 'Rapports Financiers') !== false && strpos($output, 'Total Factures') !== false) {
    echo "✅ Test Rapports Financiers : OK\n";
    $tests_passed++;
} else {
    echo "❌ Test Rapports Financiers : FAILED\n";
}

// Test 4: Rapports Personnalisés
$tests_total++;
$_GET['action'] = 'personnalises';
ob_start();
$controller = new RapportsController('rapports', 'personnalises');
$controller->executer();
$output = ob_get_clean();

if (strpos($output, 'Rapports Personnalisés') !== false && strpos($output, 'Générer un Rapport') !== false) {
    echo "✅ Test Rapports Personnalisés : OK\n";
    $tests_passed++;
} else {
    echo "❌ Test Rapports Personnalisés : FAILED\n";
}

// Test 5: Rapports Ministère
$tests_total++;
$_GET['action'] = 'ministere';
ob_start();
$controller = new RapportsController('rapports', 'ministere');
$controller->executer();
$output = ob_get_clean();

if (strpos($output, 'Rapports Officiels') !== false && strpos($output, 'Rapport d') !== false) {
    echo "✅ Test Rapports Ministère : OK\n";
    $tests_passed++;
} else {
    echo "❌ Test Rapports Ministère : FAILED\n";
}

// Test 6: Test POST pour rapport personnalisé
$tests_total++;
$_GET['action'] = 'personnalises';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['type_rapport'] = 'eleves';
$_POST['format_export'] = 'pdf';
$_POST['periode'] = 'mois';

ob_start();
$controller = new RapportsController('rapports', 'personnalises');
$controller->executer();
$output = ob_get_clean();

if (isset($_SESSION['rapports']['personnalises']) && count($_SESSION['rapports']['personnalises']) > 0) {
    echo "✅ Test Rapport Personnalisé Créé : OK\n";
    $tests_passed++;
} else {
    echo "❌ Test Rapport Personnalisé Créé : FAILED\n";
}

// Reset
$_SERVER['REQUEST_METHOD'] = 'GET';
unset($_POST);

// Résumé
echo "\n=== RÉSUMÉ ===\n";
echo "Tests passed: $tests_passed/$tests_total\n";

if ($tests_passed === $tests_total) {
    echo "✅ TOUS LES TESTS SONT PASSÉS!\n";
    exit(0);
} else {
    echo "❌ CERTAINS TESTS ONT ÉCHOUÉ\n";
    exit(1);
}

?>

