<?php
require_once __DIR__ . '/../classes/Conge.class.php';

$conge = new Conge([
    'id_enseignant' => 1,
    'type_conge' => 'maladie',
    'date_debut' => '2026-09-01',
    'date_fin' => '2026-09-10',
    'raison' => 'Repos médical',
]);

if (!$conge->est_demande()) {
    throw new RuntimeException('Le congé doit être en statut demande par défaut.');
}

$conge->accepter();
if ($conge->get_statut() !== 'accepte') {
    throw new RuntimeException('Le congé n’a pas été accepté correctement.');
}

$conge->terminer();
if ($conge->get_statut() !== 'termine') {
    throw new RuntimeException('Le congé n’a pas été terminé correctement.');
}

echo "Test Conge : OK\n";
