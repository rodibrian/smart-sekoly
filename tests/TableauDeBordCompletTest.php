<?php
/**
 * Test pour les 5 actions restantes du tableau de bord
 * Recherche, Rapports, Prévisions, Vision Directeur, Comparatif
 */

// Charger le point d'entrée
require_once __DIR__ . '/../index.php';

// Test 1: Recherche
ob_start();
$_GET = ['module' => 'tableau-de-bord', 'action' => 'recherche', 'q' => 'test'];
$ctrl = new TableauDeBordController('tableau-de-bord', 'recherche', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Recherche globale') !== false) {
    echo "Test Recherche globale : OK\n";
} else {
    echo "Test Recherche globale : FAIL\n";
}

// Test 2: Rapports
ob_start();
$_GET = [];
$ctrl = new TableauDeBordController('tableau-de-bord', 'rapports', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Rapports automatiques') !== false) {
    echo "Test Rapports : OK\n";
} else {
    echo "Test Rapports : FAIL\n";
}

// Test 3: Prévisions
ob_start();
$_GET = [];
$ctrl = new TableauDeBordController('tableau-de-bord', 'previsions', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Prévisions') !== false) {
    echo "Test Prévisions : OK\n";
} else {
    echo "Test Prévisions : FAIL\n";
}

// Test 4: Vision Directeur
ob_start();
$_GET = [];
$ctrl = new TableauDeBordController('tableau-de-bord', 'visionDirecteur', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Vision Directeur') !== false) {
    echo "Test Vision Directeur : OK\n";
} else {
    echo "Test Vision Directeur : FAIL\n";
}

// Test 5: Comparatif
ob_start();
$_GET = [];
$ctrl = new TableauDeBordController('tableau-de-bord', 'comparatif', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Comparatif inter-annuel') !== false) {
    echo "Test Comparatif : OK\n";
} else {
    echo "Test Comparatif : FAIL\n";
}
