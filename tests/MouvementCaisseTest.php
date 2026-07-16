<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/MouvementCaisse.class.php';

$mouvement = new MouvementCaisse([
    'id_caisse' => 1,
    'type_mouvement' => 'sortie',
    'montant' => 30000.00,
]);

if (!$mouvement->est_sortie()) {
    throw new RuntimeException('Le mouvement doit être de type sortie.');
}

if ($mouvement->get_montant() !== 30000.00) {
    throw new RuntimeException('Le montant du mouvement de caisse est incorrect.');
}

echo "Test MouvementCaisse : OK\n";
