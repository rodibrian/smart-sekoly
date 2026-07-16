<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Absence.class.php';

$absence = new Absence([
    'id_eleve' => 42,
    'date_absence' => '2026-07-16',
    'motif' => 'Maladie',
]);
$absence->valider();

if ($absence->get_statut() !== 'valide') {
    throw new RuntimeException('L’absence n’a pas été validée.');
}

echo "Test Absence : OK\n";
