<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';

// Générer un token CSRF valide pour la session de test
$token = generer_token_csrf();

// Simuler une requête POST vers la route de création de facture
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/smart-sekoly/factures';

$_POST = [
    'token_csrf' => $token,
    'eleve' => '1',
    'numero' => 'FAC-TEST-001',
    'date_emission' => '2026-07-16',
    'montant_total' => '12345.00',
];

ob_start();
require_once __DIR__ . '/../index.php';
ob_end_clean();

// Vérifier insertion en session (fallback DAO écrit en session si pas de DB)
if (empty($_SESSION['factures']) || !is_array($_SESSION['factures'])) {
    throw new RuntimeException('La session "factures" est vide après POST.');
}

$dernier = end($_SESSION['factures']);
if (($dernier['numero_sequentiel'] ?? $dernier['numero'] ?? '') !== 'FAC-TEST-001' || (float) ($dernier['montant_total'] ?? 0) !== 12345.0) {
    throw new RuntimeException('La facture enregistrée ne correspond pas aux données envoyées.');
}

echo "Test Facture POST : OK\n";