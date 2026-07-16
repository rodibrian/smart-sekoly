<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/VieScolaire.controller.php';

$controller = new VieScolaireController('vie-scolaire', 'retards');
$controller->executer();

$controllerIncidents = new VieScolaireController('vie-scolaire', 'incidents');
$controllerIncidents->executer();

echo "Test VieScolaireRetardsIncidents : OK\n";
