<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Eleve.controller.php';

$controller = new EleveController('eleves', 'inscription');
$result = $controller->traiter_formulaire([
    'nom' => 'Rabe',
    'prenom' => 'Mina',
    'email' => 'mina@example.com',
    'date_naissance' => '2014-06-01',
    'matricule' => '',
]);

if (!$result['valide']) {
    throw new RuntimeException('La validation d’inscription a échoué.');
}
if ($result['donnees']['matricule'] === '') {
    throw new RuntimeException('Le matricule n’a pas été généré.');
}

echo "Eleve validation test: OK\n";
