<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Personne.class.php';
require_once __DIR__ . '/../classes/Eleve.class.php';

$eleve = new Eleve([
    'nom' => 'Rasoanirina',
    'prenom' => 'Mialy',
    'email' => 'mialy@example.com',
]);
$eleve->set_matricule('EL-2026-001');

if ($eleve->get_nom() !== 'Rasoanirina') {
    throw new RuntimeException('Le nom de l’élève n’a pas été conservé.');
}

if ($eleve->get_matricule() !== 'EL-2026-001') {
    throw new RuntimeException('Le matricule n’a pas été conservé.');
}

echo "Test Eleve : OK\n";
