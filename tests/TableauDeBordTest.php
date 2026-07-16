<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/TableauDeBord.controller.php';

ob_start();
$controller = new TableauDeBordController('tableau-de-bord', 'index');
$controller->executer();
$sortieTableau = ob_get_clean();

if (strpos($sortieTableau, 'Tableau de bord') === false) {
    throw new Exception('La vue du tableau de bord n\'a pas été rendue.');
}

if (strpos($sortieTableau, 'Élèves') === false || strpos($sortieTableau, 'Enseignants') === false) {
    throw new Exception('Les indicateurs clés ne sont pas présents.');
}

echo "Test TableauDeBord : OK\n";
