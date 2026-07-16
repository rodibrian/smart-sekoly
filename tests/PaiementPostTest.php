<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';

$token = generer_token_csrf();

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/smart-sekoly/paiements';

$_POST = [
    'token_csrf' => $token,
    'id_echeance' => '1',
    'numero_recu' => 'REC-TEST-001',
    'date_paiement' => '2026-07-16 12:00:00',
    'montant' => '2500.50',
    'mode_paiement' => 'espece',
];

ob_start();
require_once __DIR__ . '/../index.php';
ob_end_clean();

if (empty($_SESSION['paiements']) || !is_array($_SESSION['paiements'])) {
    throw new RuntimeException('La session "paiements" est vide après POST.');
}

$dernier = end($_SESSION['paiements']);
if ((int) $dernier['id_echeance'] !== 1 || $dernier['numero_recu'] !== 'REC-TEST-001' || (float) $dernier['montant'] !== 2500.5) {
    throw new RuntimeException('Le paiement enregistré ne correspond pas aux données envoyées.');
}

echo "Test Paiement POST : OK\n";
