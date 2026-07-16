<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/FinanceDAO.class.php';

// Nettoyage session
$_SESSION['remises'] = [];
$_SESSION['echeances'] = [];

$dao = new FinanceDAO();

// Test insertRemise
$idRemise = $dao->insertRemise([
    'type_remise' => 'pourcentage',
    'valeur_remise' => 5.0,
    'motif' => 'Test unitaire'
]);

if (empty($_SESSION['remises'])) {
    echo "FAIL: remises not created in session\n";
    exit(1);
}

$found = false;
foreach ($_SESSION['remises'] as $r) {
    if (($r['id_remise'] ?? null) == $idRemise) { $found = true; break; }
}
if (!$found) {
    echo "FAIL: inserted remise id not found in session\n";
    exit(1);
}

// Test insertEcheance
$idEche = $dao->insertEcheance([
    'id_facture' => 1,
    'date_echeance' => '2026-10-01',
    'montant_prevu' => 1000.0,
    'statut_echeance' => 'payee'
]);

if (empty($_SESSION['echeances'])) {
    echo "FAIL: echeances not created in session\n";
    exit(1);
}

$found2 = false;
foreach ($_SESSION['echeances'] as $e) {
    if (($e['id_echeance'] ?? null) == $idEche) { $found2 = true; break; }
}
if (!$found2) {
    echo "FAIL: inserted echeance id not found in session\n";
    exit(1);
}

echo "FinanceDAO unit tests: OK\n";
return 0;
