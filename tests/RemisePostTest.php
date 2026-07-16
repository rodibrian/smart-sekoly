<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';

$token = generer_token_csrf();

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/smart-sekoly/remises';

$_POST = [
    'token_csrf' => $token,
    'type_remise' => 'pourcentage',
    'valeur_remise' => '5',
    'motif' => 'Test automatique',
];

ob_start();
require_once __DIR__ . '/../index.php';
ob_end_clean();

if (empty($_SESSION['remises']) || !is_array($_SESSION['remises'])) {
    throw new RuntimeException('La session "remises" est vide après POST.');
}

$dernier = end($_SESSION['remises']);
if ($dernier['type_remise'] !== 'pourcentage' || (float) $dernier['valeur_remise'] !== 5.0 || $dernier['motif'] !== 'Test automatique') {
    throw new RuntimeException('La remise enregistrée ne correspond pas aux données envoyées.');
}

echo "Test Remise POST : OK\n";
