<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Eleve.controller.php';
require_once __DIR__ . '/../classes/DocumentObligatoire.class.php';

$document = new DocumentObligatoire([
    'nom' => 'CNI',
    'statut' => 'manquant',
]);
$document->marquer_recu();

if ($document->get_statut() !== 'recu') {
    throw new RuntimeException('Le document n’a pas été marqué comme reçu.');
}

$controleur = new EleveController('eleves', 'documents', 42);
$resultat = $controleur->preparer_documents_obligatoires();

if (($resultat['id_eleve'] ?? null) !== 42) {
    throw new RuntimeException('L’identifiant de l’élève est incorrect.');
}

if (count($resultat['documents'] ?? []) < 3) {
    throw new RuntimeException('La liste des documents obligatoires est incomplète.');
}

echo "Test DocumentsObligatoires : OK\n";
