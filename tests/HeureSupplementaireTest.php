<?php
require_once __DIR__ . '/../classes/HeureSupplementaire.class.php';

$heure = new HeureSupplementaire([
    'id_enseignant' => 1,
    'id_classe' => 2,
    'id_matiere' => 3,
    'date_heure' => '2026-09-15',
    'nombre_heures' => 4.5,
    'taux' => 15000,
]);

if (!$heure->est_proposee()) {
    throw new RuntimeException('L’heure supplémentaire doit être proposée par défaut.');
}

if ($heure->get_montant() !== 67500.0) {
    throw new RuntimeException('Le montant n’est pas calculé correctement.');
}

$heure->valider();
if ($heure->get_statut() !== 'validee') {
    throw new RuntimeException('Le statut n’a pas été validé correctement.');
}

$heure->payer();
if ($heure->get_statut() !== 'payee') {
    throw new RuntimeException('Le statut n’a pas été payé correctement.');
}

echo "Test HeureSupplementaire : OK\n";
