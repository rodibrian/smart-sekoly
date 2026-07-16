<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Remise.class.php';

$remisePourcentage = new Remise([
    'type_remise' => 'pourcentage',
    'valeur_remise' => 10.0,
    'motif' => 'Remise promotionnelle',
    'id_utilisateur_validation' => 1,
]);

if (!$remisePourcentage->est_pourcentage()) {
    throw new RuntimeException('La remise doit être de type pourcentage.');
}

if ($remisePourcentage->calcule_montant_remise(200000.00) !== 20000.0) {
    throw new RuntimeException('Le calcul de remise pourcentage est incorrect.');
}

$remiseFixe = new Remise([
    'type_remise' => 'montant_fixe',
    'valeur_remise' => 5000.00,
    'motif' => 'Remise de fidélité',
    'id_utilisateur_validation' => 1,
]);

if (!$remiseFixe->est_montant_fixe()) {
    throw new RuntimeException('La remise doit être de type montant fixe.');
}

if ($remiseFixe->calcule_montant_remise(200000.00) !== 5000.0) {
    throw new RuntimeException('Le calcul de remise fixe est incorrect.');
}

echo "Test Remise : OK\n";
