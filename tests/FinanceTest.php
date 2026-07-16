<?php
/**
 * Test du module Finance
 * Vérifie toutes les actions du contrôleur Finance
 */

require_once __DIR__ . '/../index.php';

// Test 1: Index Finance
ob_start();
$_GET = ['module' => 'finance', 'action' => 'index'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=index';
$ctrl = new FinanceController('finance', 'index', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Gestion Financière') !== false || strpos($output, 'Tableau de bord financier') !== false) {
    echo "Test Finance Index : OK\n";
} else {
    echo "Test Finance Index : FAIL\n";
}

// Test 2: Factures
ob_start();
$_GET = ['module' => 'finance', 'action' => 'factures'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=factures';
$ctrl = new FinanceController('finance', 'factures', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Factures') !== false) {
    echo "Test Finance Factures : OK\n";
} else {
    echo "Test Finance Factures : FAIL\n";
}

// Test 3: Paiements
ob_start();
$_GET = ['module' => 'finance', 'action' => 'paiements'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=paiements';
$ctrl = new FinanceController('finance', 'paiements', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Paiements') !== false) {
    echo "Test Finance Paiements : OK\n";
} else {
    echo "Test Finance Paiements : FAIL\n";
}

// Test 4: Caisses
ob_start();
$_GET = ['module' => 'finance', 'action' => 'caisses'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=caisses';
$ctrl = new FinanceController('finance', 'caisses', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Caisses') !== false) {
    echo "Test Finance Caisses : OK\n";
} else {
    echo "Test Finance Caisses : FAIL\n";
}

// Test 5: Remises
ob_start();
$_GET = ['module' => 'finance', 'action' => 'remises'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=remises';
$ctrl = new FinanceController('finance', 'remises', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Remises') !== false) {
    echo "Test Finance Remises : OK\n";
} else {
    echo "Test Finance Remises : FAIL\n";
}

// Test 6: Rapports
ob_start();
$_GET = ['module' => 'finance', 'action' => 'rapports'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=rapports';
$ctrl = new FinanceController('finance', 'rapports', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Rapports Financiers') !== false) {
    echo "Test Finance Rapports : OK\n";
} else {
    echo "Test Finance Rapports : FAIL\n";
}

// Test 7: Impayés
ob_start();
$_GET = ['module' => 'finance', 'action' => 'impayés'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=impayés';
$ctrl = new FinanceController('finance', 'impayés', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Factures Impayées') !== false) {
    echo "Test Finance Impayés : OK\n";
} else {
    echo "Test Finance Impayés : FAIL\n";
}

// Test 8: Créer Facture
ob_start();
$_GET = ['module' => 'finance', 'action' => 'facture-creer'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=facture-creer';
$ctrl = new FinanceController('finance', 'facture-creer', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Créer une Facture') !== false) {
    echo "Test Finance Créer Facture : OK\n";
} else {
    echo "Test Finance Créer Facture : FAIL\n";
}

// Test 9: Enregistrer Paiement
ob_start();
$_GET = ['module' => 'finance', 'action' => 'paiement-enregistrer'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=paiement-enregistrer';
$ctrl = new FinanceController('finance', 'paiement-enregistrer', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Enregistrer un Paiement') !== false) {
    echo "Test Finance Enregistrer Paiement : OK\n";
} else {
    echo "Test Finance Enregistrer Paiement : FAIL\n";
}

// Test 10: Créer Caisse
ob_start();
$_GET = ['module' => 'finance', 'action' => 'caisse-creer'];
$_SERVER['REQUEST_URI'] = '/smart-sekoly/?module=finance&action=caisse-creer';
$ctrl = new FinanceController('finance', 'caisse-creer', null);
$ctrl->executer();
$output = ob_get_clean();
if (strpos($output, 'Créer une Caisse') !== false) {
    echo "Test Finance Créer Caisse : OK\n";
} else {
    echo "Test Finance Créer Caisse : FAIL\n";
}
