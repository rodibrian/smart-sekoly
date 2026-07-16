<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/TableauDeBordRh.controller.php';

$controller = new TableauDeBordRhController('tableau-de-bord-rh', 'index');
$controller->executer();

echo "Test TableauBordRh : OK\n";
