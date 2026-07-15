<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Parametrage.controller.php';

$controleur = new ParametrageController('parametrage', 'assistant');
$resultat = $controleur->traiter_formulaire([
    'nom_etablissement' => 'Lycée Moderne',
    'format_matricule' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}',
    'prefixe_matricule' => 'LM',
    'annee_courante' => '2026',
]);

if ($resultat['valide'] !== true) {
    throw new RuntimeException('Le formulaire de paramétrage est invalide.');
}

if ($resultat['donnees']['nom_etablissement'] !== 'Lycée Moderne') {
    throw new RuntimeException('Le nom de l’établissement n’a pas été conservé.');
}

echo "Test ParametrageController : OK\n";
