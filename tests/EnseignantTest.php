<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Personne.class.php';
require_once __DIR__ . '/../classes/Enseignant.class.php';

$enseignant = new Enseignant([
    'nom' => 'Rakoto',
    'prenom' => 'Jean',
    'email' => 'jean@example.com',
    'matricule' => 'ENS-2026-001',
]);

if ($enseignant->get_nom() !== 'Rakoto') {
    throw new RuntimeException('Le nom de l’enseignant n’a pas été initialisé correctement.');
}

if ($enseignant->get_matricule() !== 'ENS-2026-001') {
    throw new RuntimeException('Le matricule initial de l’enseignant n’est pas conservé.');
}

$enseignant->generer_matricule('ENS', 2026);

if (strpos($enseignant->get_matricule(), 'ENS-2026-') !== 0) {
    throw new RuntimeException('La génération du matricule enseignant n’est pas correcte.');
}

echo "Test Enseignant : OK\n";
