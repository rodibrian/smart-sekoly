<?php
$_SERVER['REQUEST_URI'] = '/smart-sekoly/portails/index';
ob_start();
require __DIR__ . '/../index.php';
$contenu = ob_get_clean();

if (strpos($contenu, 'Portails Élève / Parent') === false && strpos($contenu, 'Portails Élève / Parent') === false) {
    throw new RuntimeException('La route /portails/index ne charge pas la vue attendue.');
}

echo "Test RouteurPortails : OK\n";
