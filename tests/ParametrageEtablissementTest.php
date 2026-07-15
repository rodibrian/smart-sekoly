<?php
require_once __DIR__ . '/../classes/ParametrageEtablissement.class.php';

$parametrage = new ParametrageEtablissement();
$parametrage->set_format_matricule('{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}');
$matricule = $parametrage->generer_matricule('ELEVE', 12, 2026);

if ($matricule !== 'ELEVE-2026-12') {
    throw new RuntimeException('Le matricule généré est incorrect : ' . $matricule);
}

echo "Test ParametrageEtablissement : OK\n";
