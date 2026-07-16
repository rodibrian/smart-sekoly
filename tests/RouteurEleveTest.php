<?php
$_SERVER['REQUEST_URI'] = '/smart-sekoly/eleves/inscription';
ob_start();
require_once __DIR__ . '/../index.php';
$contenu = ob_get_clean();

if (strpos($contenu, 'Formulaire d’inscription élève') === false) {
    throw new RuntimeException('La route /eleves/inscription ne charge pas la vue attendue.');
}

echo "Test RouteurEleve : OK\n";
