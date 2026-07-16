<?php
require_once __DIR__ . '/../classes/Affectation.class.php';

$affectation = new Affectation([
    'id_enseignant' => 1,
    'id_matiere' => 2,
    'id_classe' => 3,
    'id_annee' => 4,
    'date_affectation' => '2026-09-01',
]);

if (!$affectation->est_active()) {
    throw new RuntimeException('L’affectation doit être active par défaut.');
}

$affectation->terminer();
if ($affectation->get_statut() !== 'terminee') {
    throw new RuntimeException('Le statut de l’affectation n’a pas été mis à jour.');
}

$affectation->reaffecter();
if ($affectation->get_statut() !== 'reaffectee') {
    throw new RuntimeException('Le statut de l’affectation n’a pas été requalifié en reaffectee.');
}

echo "Test Affectation : OK\n";
