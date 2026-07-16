<?php
session_start();
unset($_SESSION['eleves']);

require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/ImportDonnees.class.php';

$import = new ImportDonnees();
$fichier_temp = tempnam(sys_get_temp_dir(), 'smart-sekoly-import-');
file_put_contents($fichier_temp, "nom,prenom,email,date_naissance,matricule\nJean,Durand,jean@example.com,2010-02-01,EL-2026-002\n");

$resultat = $import->importer($fichier_temp);

if ($resultat['lignes_validees'] !== 1 || ($resultat['importes'] ?? 0) !== 1) {
    throw new RuntimeException('L’import n’a pas créé l’élève attendu.');
}

if (!isset($_SESSION['eleves']) || count($_SESSION['eleves']) !== 1) {
    throw new RuntimeException('Les élèves importés ne sont pas enregistrés en session.');
}

unlink($fichier_temp);

echo "Test ImportPersistance : OK\n";
