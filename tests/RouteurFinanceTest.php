<?php
$routes = [
    '/smart-sekoly/factures/nouvelle' => 'Nouvelle facture',
    '/smart-sekoly/remises/nouvelle' => 'Nouvelle remise',
    '/smart-sekoly/paiements/nouveau' => 'Nouveau paiement',
    '/smart-sekoly/caisses/nouvelle' => 'Nouvelle caisse',
    '/smart-sekoly/echeances/nouvelle' => 'Nouvelle échéance',
];

foreach ($routes as $uri => $attendu) {
    $_SERVER['REQUEST_URI'] = $uri;
    ob_start();
    require_once __DIR__ . '/../index.php';
    $contenu = ob_get_clean();

    if (strpos($contenu, $attendu) === false) {
        throw new RuntimeException(sprintf('La route %s ne charge pas la vue attendue.', $uri));
    }
}

echo "Test RouteurFinance : OK\n";
