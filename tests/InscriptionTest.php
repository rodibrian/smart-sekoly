<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Inscription.class.php';

$inscription = new Inscription([
    'id_eleve' => 12,
    'id_classe' => 3,
    'id_annee' => 1,
]);
$inscription->annuler(7);

if ($inscription->get_statut_inscription() !== 'annule') {
    throw new RuntimeException('L’inscription n’a pas été annulée correctement.');
}

echo "Test Inscription : OK\n";
