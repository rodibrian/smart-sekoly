<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Sanction.class.php';

$sanction = new Sanction([
    'id_eleve' => 42,
    'type' => 'avertissement',
    'description' => 'Retard répété',
]);
$sanction->valider();

if ($sanction->get_statut() !== 'validee') {
    throw new RuntimeException('La sanction n’a pas été validée.');
}

echo "Test Sanction : OK\n";
