<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Eleve.controller.php';

$controleur = new EleveController('eleves', 'inscription');
$resultat = $controleur->traiter_formulaire([
    'nom' => 'Andriamihaja',
    'prenom' => 'Lova',
    'email' => 'lova@example.com',
    'date_naissance' => '2015-03-05',
    'matricule' => 'EL-2026-001',
]);

if ($resultat['valide'] !== true) {
    throw new RuntimeException('Le formulaire d’inscription est invalide.');
}

echo "Test InscriptionFormulaire : OK\n";
