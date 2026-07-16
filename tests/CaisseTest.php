<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Caisse.class.php';

$caisse = new Caisse([
    'date_caisse' => '2026-10-01',
    'fond_de_caisse' => 150000.00,
]);

if ($caisse->get_date_caisse() !== '2026-10-01') {
    throw new RuntimeException('La date de la caisse est incorrecte.');
}

$caisse->retirer_fond(50000.00);
if ($caisse->get_fond_de_caisse() !== 100000.00) {
    throw new RuntimeException('La caisse n’a pas bien retiré le montant.');
}

$caisse->ajouter_fond(25000.00);
if ($caisse->get_fond_de_caisse() !== 125000.00) {
    throw new RuntimeException('La caisse n’a pas bien ajouté le montant.');
}

echo "Test Caisse : OK\n";
