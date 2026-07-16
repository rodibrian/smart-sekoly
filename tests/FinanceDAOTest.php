<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/FinanceDAO.class.php';

$dao = new FinanceDAO();

// Nettoyage session pour test propre
unset($_SESSION['factures'], $_SESSION['paiements'], $_SESSION['caisses']);

// Test insertFacture
$idF = $dao->insertFacture([
    'numero' => 'FAC-DAO-001',
    'id_eleve' => 1,
    'date_emission' => '2026-07-16',
    'montant_total' => 5000.00,
    'statut' => 'brouillon',
]);

if (!is_int($idF) || $idF <= 0) {
    throw new RuntimeException('insertFacture did not return a valid id.');
}

if (est_base_donnees_disponible()) {
    $f = $dao->getFacture($idF);
    if (empty($f)) {
        throw new RuntimeException('getFacture returned empty for id from insertFacture.');
    }
} else {
    if (empty($_SESSION['factures']) || !is_array($_SESSION['factures'])) {
        throw new RuntimeException('Session factures not populated after insertFacture fallback.');
    }
}

// Test insertPaiement
$idP = $dao->insertPaiement([
    'id_echeance' => 1,
    'numero_recu' => 'REC-DAO-001',
    'date_paiement' => '2026-07-16 12:00:00',
    'montant' => 2500.00,
    'mode_paiement' => 'espece',
    'statut' => 'actif',
]);

if (!is_int($idP) || $idP <= 0) {
    throw new RuntimeException('insertPaiement did not return a valid id.');
}

if (!est_base_donnees_disponible() && (empty($_SESSION['paiements']) || !is_array($_SESSION['paiements']))) {
    throw new RuntimeException('Session paiements not populated after insertPaiement fallback.');
}

// Test insertCaisse
$idC = $dao->insertCaisse([
    'date_caisse' => '2026-07-16',
    'fond_de_caisse' => 1500.00,
]);

if (!is_int($idC) || $idC <= 0) {
    throw new RuntimeException('insertCaisse did not return a valid id.');
}

if (!est_base_donnees_disponible() && (empty($_SESSION['caisses']) || !is_array($_SESSION['caisses']))) {
    throw new RuntimeException('Session caisses not populated after insertCaisse fallback.');
}

echo "FinanceDAO tests: OK\n";