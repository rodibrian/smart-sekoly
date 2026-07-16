<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';

// Générer un token CSRF valide pour la session de test
$token = generer_token_csrf();

// Simuler une requête POST vers la route de création de caisse
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/smart-sekoly/caisses';

$_POST = [
    'token_csrf' => $token,
    'date_caisse' => '2026-07-16',
    'fond_de_caisse' => '1000.00',
];

ob_start();
require_once __DIR__ . '/../index.php';
ob_end_clean();

if (empty($_SESSION['caisses']) || !is_array($_SESSION['caisses'])) {
    throw new RuntimeException('La session "caisses" est vide après POST.');
}

$dernier = end($_SESSION['caisses']);
if ($dernier['date_caisse'] !== '2026-07-16' || (float) $dernier['fond_de_caisse'] !== 1000.0) {
    throw new RuntimeException('La caisse enregistrée ne correspond pas aux données envoyées.');
}

echo "Test Caisse POST : OK\n";
