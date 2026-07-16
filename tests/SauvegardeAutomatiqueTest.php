<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Parametrage.controller.php';

$controleur = new ParametrageController('parametrage', 'sauvegardes');
$resultat = $controleur->traiter_sauvegarde_formulaire([
    'frequence' => 'quotidienne',
    'repertoire' => 'backups',
    'retention' => '7',
    'activer' => '1',
]);

if ($resultat['valide'] !== true) {
    throw new RuntimeException('La configuration des sauvegardes est invalide.');
}

echo "Test SauvegardeAutomatique : OK\n";
