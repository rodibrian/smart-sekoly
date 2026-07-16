<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Eleve.controller.php';

$matricule = generer_matricule('EL', 2026);
if (!preg_match('/^EL-2026-[A-Z0-9]{6}$/', $matricule)) {
    throw new RuntimeException('Le matricule généré ne respecte pas le format attendu.');
}

$controleur = new EleveController('eleves', 'inscription');
$resultat = $controleur->traiter_formulaire([
    'nom' => 'A',
    'prenom' => 'B',
    'email' => 'a@example.com',
    'date_naissance' => '2010-01-01',
    'matricule' => '',
]);

if ($resultat['donnees']['matricule'] === '') {
    throw new RuntimeException('Le contrôleur n’a pas généré de matricule.');
}

echo "Test GenerationMatricule : OK\n";
