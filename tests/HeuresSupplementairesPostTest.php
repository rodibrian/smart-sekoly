<?php
session_start();
$_SESSION['heures_supplementaires'] = [];

require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/HeureSupplementaire.controller.php';
require_once __DIR__ . '/../classes/HeureSupplementaire.class.php';
require_once __DIR__ . '/../classes/JournalSuivi.class.php';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'token_csrf' => 'test',
    'enseignant' => '1',
    'classe' => '6e A',
    'matiere' => 'Mathématiques',
    'date_heure' => '2026-09-15',
    'nombre_heures' => '4.5',
    'taux' => '15000',
];

$controller = new HeureSupplementaireController('heures-supplementaires', 'nouvelle');
$controller->executer();

if (empty($_SESSION['heures_supplementaires'])) {
    throw new RuntimeException('La demande d’heures supplémentaires n’a pas été enregistrée.');
}

echo "Test HeuresSupplementairesPost : OK\n";
