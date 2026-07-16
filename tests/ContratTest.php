<?php
require_once __DIR__ . '/../classes/Contrat.class.php';

$contrat = new Contrat([
    'id_enseignant' => 1,
    'type_contrat' => 'horaire',
    'date_debut' => '2026-08-01',
    'salaire' => '1200000.00',
]);

if (!$contrat->est_actif()) {
    throw new RuntimeException('Le contrat doit être actif par défaut.');
}

$contrat->terminer('2027-07-31');
if ($contrat->get_statut() !== 'termine') {
    throw new RuntimeException('Le statut du contrat n’a pas été mis à jour en termine.');
}

if ($contrat->get_date_fin() !== '2027-07-31') {
    throw new RuntimeException('La date de fin du contrat est incorrecte après terminaison.');
}

echo "Test Contrat : OK\n";
