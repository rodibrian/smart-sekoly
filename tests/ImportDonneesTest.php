<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/ImportDonnees.class.php';

$import = new ImportDonnees();
$modele = $import->generer_modele();

if (strpos($modele, 'nom,prenom,email,date_naissance,matricule') === false) {
    throw new RuntimeException('Le modèle CSV n’inclut pas les colonnes attendues.');
}

$fichier_temp = tempnam(sys_get_temp_dir(), 'smart-sekoly-import-');
file_put_contents($fichier_temp, "nom,prenom,email,date_naissance,matricule\nJean,Durand,jean@example.com,2010-02-01,EL-2026-001\n");

$resultat = $import->importer($fichier_temp);

if ($resultat['total_lignes'] !== 1 || $resultat['lignes_validees'] !== 1) {
    throw new RuntimeException('L’import CSV ne traite pas correctement la ligne de test.');
}

unlink($fichier_temp);

echo "Test ImportDonnees : OK\n";
