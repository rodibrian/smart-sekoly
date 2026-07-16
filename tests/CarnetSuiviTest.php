<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/CarnetSuivi.class.php';

$carnet = new CarnetSuivi(42);
$carnet->ajouter_evenement('Rappel', 'Documents à fournir', 'info');
$carnet->ajouter_evenement('Absence', 'Absence non justifiée', 'warning');

if (count($carnet->get_evenements()) !== 2) {
    throw new RuntimeException('Le carnet de suivi ne contient pas le bon nombre d’événements.');
}

echo "Test CarnetSuivi : OK\n";
