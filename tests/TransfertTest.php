<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Transfert.class.php';

$transfert = new Transfert([
    'id_inscription' => 5,
    'motif' => 'Déménagement',
    'etablissement_origine_destination' => 'Collège Saint-Pierre',
]);
$transfert->valider();

if ($transfert->get_statut() !== 'valide') {
    throw new RuntimeException('Le transfert n’a pas été validé.');
}

echo "Test Transfert : OK\n";
