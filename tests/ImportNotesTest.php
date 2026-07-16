<?php
session_start();

require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/ImportDonnees.class.php';

$import = new ImportDonnees();

// Préparer un élève existant en session pour vérifier le lien matricule.
$_SESSION['eleves'] = [
    1 => [
        'id' => 1,
        'nom' => 'Durand',
        'prenom' => 'Jean',
        'email' => 'jean.durand@example.com',
        'date_naissance' => '2010-02-01',
        'matricule' => 'EL-2026-001',
        'statut' => 'Actif',
    ],
];

$fichier_temp = tempnam(sys_get_temp_dir(), 'smart-sekoly-import-notes-');
file_put_contents($fichier_temp, "matricule,id_evaluation,date_evaluation,matiere,periode,valeur,appreciation,coefficient,enseignant\nEL-2026-001,10,2026-06-15,Mathématiques,Semestre 2,15.5,Très bien,2,Mme Dupont\n");

$resultat = $import->importer($fichier_temp, 'notes');

if ($resultat['total_lignes'] !== 1 || $resultat['lignes_validees'] !== 1 || ($resultat['importes'] ?? 0) !== 1) {
    throw new RuntimeException('L’import de notes ne traite pas correctement la ligne de test.');
}

if (!isset($_SESSION['notes']) || count($_SESSION['notes']) !== 1) {
    throw new RuntimeException('Les notes importées ne sont pas enregistrées en session.');
}

$note = reset($_SESSION['notes']);
if ($note['matricule'] !== 'EL-2026-001' || $note['valeur'] !== 15.5 || $note['matiere'] !== 'Mathématiques') {
    throw new RuntimeException('Le contenu de la note importée est incorrect.');
}

unlink($fichier_temp);
echo "Test ImportNotes : OK\n";
