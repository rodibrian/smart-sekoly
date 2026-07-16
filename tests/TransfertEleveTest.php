<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/TransfertEleve.class.php';

$transfert = new TransfertEleve([
    'id_eleve' => 42,
    'type' => 'depart',
    'destination' => 'Lycée Moderne',
]);
$transfert->valider();

if ($transfert->get_statut() !== 'valide') {
    throw new RuntimeException('Le transfert n’a pas été validé.');
}

echo "Test TransfertEleve : OK\n";
