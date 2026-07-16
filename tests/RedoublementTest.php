<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Redoublement.class.php';

$redoublement = new Redoublement(['id_eleve' => 42, 'motif' => '']);
$redoublement->proposer('Faible progression');
$redoublement->valider();

if ($redoublement->get_decision() !== 'valide') {
    throw new RuntimeException('La décision de redoublement est incorrecte.');
}

echo "Test Redoublement : OK\n";
