<?php
session_start();
define('APP_NAME', 'Smart-Sekoly');
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/VieScolaire.controller.php';

ob_start();
$controller = new VieScolaireController('vie-scolaire', 'billets');
$controller->executer();
$sortieBillets = ob_get_clean();

if (strpos($sortieBillets, 'Suivi des billets') === false) {
    throw new Exception('La vue des billets n\'a pas été rendue.');
}

ob_start();
$controllerSorties = new VieScolaireController('vie-scolaire', 'sorties');
$controllerSorties->executer();
$sortieSorties = ob_get_clean();

if (strpos($sortieSorties, 'Autorisation de sortie') === false) {
    throw new Exception('La vue des autorisations de sortie n\'a pas été rendue.');
}

echo "Test VieScolaireBilletsSorties : OK\n";
