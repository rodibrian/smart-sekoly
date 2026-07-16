<?php
$_SERVER['REQUEST_URI'] = '/smart-sekoly/factures/nouvelle';
ob_start();
require_once __DIR__ . '/../index.php';
$contenu = ob_get_clean();

if (strpos($contenu, 'Nouvelle facture') === false) {
    throw new RuntimeException('La route /factures/nouvelle ne charge pas la vue attendue.');
}

echo "Test RouteurFinance : OK\n";
