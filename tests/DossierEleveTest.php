<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Eleve.controller.php';

$controleur = new EleveController('eleves', 'dossier', 42);
$resultat = $controleur->preparer_donnees_dossier();

if (($resultat['id_eleve'] ?? null) !== 42) {
    throw new RuntimeException('L’identifiant de l’élève est incorrect.');
}

if (($resultat['eleve']['nom'] ?? '') !== 'Andriamihaja') {
    throw new RuntimeException('Le dossier élève ne contient pas les données attendues.');
}

echo "Test DossierEleve : OK\n";
