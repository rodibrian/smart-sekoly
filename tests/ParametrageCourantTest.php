<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Parametrage.controller.php';

$controleur = new ParametrageController('parametrage', 'courant');
$resultat = $controleur->traiter_formulaire([
    'nom_etablissement' => 'Collège d’Excellence',
    'format_matricule' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}',
    'prefixe_matricule' => 'CE',
    'annee_courante' => '2027',
]);

if ($resultat['valide'] !== true) {
    throw new RuntimeException('Le formulaire courant est invalide.');
}

echo "Test ParametrageCourant : OK\n";
