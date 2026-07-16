<?php
/**
 * Test: Vérifier que le routeur accepte les paramètres GET
 */

// Test 1: Tableau de bord (index)
ob_start();
$_GET = ['module' => 'tableau-de-bord', 'action' => 'index'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/';
$_SERVER['HTTP_HOST'] = 'localhost';
require __DIR__ . '/../index.php';
$output = ob_get_clean();

echo "=== Test 1: Tableau de bord (index) ===\n";
if (strpos($output, 'Tableau de bord') !== false || strpos($output, 'indicateurs') !== false) {
    echo "✓ OK\n";
} else {
    echo "✗ FAIL - Output: " . substr($output, 0, 300) . "\n";
}
