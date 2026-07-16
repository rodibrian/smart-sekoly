<?php
require_once __DIR__ . '/../classes/Salaire.class.php';

$salaire = new Salaire([
    'id_enseignant' => 1,
    'periode' => '2026-09',
    'montant_brut' => 500000.00,
    'retenues' => 75000.00,
]);

if ($salaire->get_montant_net() !== 425000.00) {
    throw new RuntimeException('Le montant net est incorrect.');
}

if ($salaire->get_statut() !== 'en_attente') {
    throw new RuntimeException('Le statut initial doit être en_attente.');
}

$salaire->valider();
if ($salaire->get_statut() !== 'valide') {
    throw new RuntimeException('Le statut de salaire n’a pas été validé.');
}

$salaire->payer('2026-09-30');
if (!$salaire->est_paye() || $salaire->get_date_paiement() !== '2026-09-30') {
    throw new RuntimeException('Le salaire n’a pas été payé correctement.');
}

echo "Test Salaire : OK\n";
