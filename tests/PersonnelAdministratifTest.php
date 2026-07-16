<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Personne.class.php';
require_once __DIR__ . '/../classes/PersonnelAdministratif.class.php';

$personnel = new PersonnelAdministratif([
    'nom' => 'Rabe',
    'prenom' => 'Mina',
    'email' => 'mina@example.com',
    'fonction' => 'Secrétaire',
]);

if ($personnel->get_nom() !== 'Rabe') {
    throw new RuntimeException('Le nom du personnel administratif est incorrect.');
}

if ($personnel->get_fonction() !== 'Secrétaire') {
    throw new RuntimeException('La fonction du personnel administratif est incorrecte.');
}

echo "Test PersonnelAdministratif : OK\n";
