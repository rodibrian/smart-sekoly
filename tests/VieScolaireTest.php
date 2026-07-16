<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Absence.class.php';
require_once __DIR__ . '/../classes/Sanction.class.php';
require_once __DIR__ . '/../controllers/VieScolaire.controller.php';

$controller = new VieScolaireController('vie-scolaire', 'sanctions');
$controller->executer();

echo "Test VieScolaire : OK\n";
