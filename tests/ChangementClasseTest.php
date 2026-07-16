<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/ChangementClasse.class.php';

$changement = new ChangementClasse([
    'id_eleve' => 42,
    'ancienne_classe' => '5e B',
    'nouvelle_classe' => '6e A',
]);
$changement->valider();

if ($changement->get_statut() !== 'valide') {
    throw new RuntimeException('Le changement de classe n’a pas été validé.');
}

echo "Test ChangementClasse : OK\n";
