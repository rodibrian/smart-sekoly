<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Contrat.class.php';
require_once __DIR__ . '/../classes/Salaire.class.php';
require_once __DIR__ . '/../controllers/Salaire.controller.php';

$contrat = new Contrat(['id_enseignant' => 1, 'type_contrat' => 'permanent', 'salaire' => 500000]);
$salaire = Salaire::calculerPourContrat($contrat, ['heures' => 100, 'periode' => '2026-09']);

if ($salaire->get_montant_brut() !== 500000.0 || $salaire->get_retenues() !== 75000.0 || $salaire->get_montant_net() !== 425000.0) {
    throw new RuntimeException('Le calcul de salaire ne correspond pas au contrat permanent attendu.');
}

$controller = new SalaireController('salaires', 'contrat');
$controller->executer();

echo "Test SalaireFlux : OK\n";
