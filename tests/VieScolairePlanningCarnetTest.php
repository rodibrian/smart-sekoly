<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/VieScolaire.controller.php';

ob_start();
$controller = new VieScolaireController('vie-scolaire', 'planning');
$controller->executer();
$sortiePlanning = ob_get_clean();

if (strpos($sortiePlanning, 'Planning des surveillants') === false) {
    throw new Exception('La vue du planning des surveillants n\'a pas été rendue.');
}

ob_start();
$controllerCarnet = new VieScolaireController('vie-scolaire', 'carnet');
$controllerCarnet->executer();
$sortieCarnet = ob_get_clean();

if (strpos($sortieCarnet, 'Carnet de suivi collectif') === false) {
    throw new Exception('La vue du carnet de suivi collectif n\'a pas été rendue.');
}

echo "Test VieScolairePlanningCarnet : OK\n";
