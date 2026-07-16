<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/JournalSuivi.class.php';
require_once __DIR__ . '/../classes/SuiviProjet.class.php';

$journal = new JournalSuivi();
$journal->ajouter('test', 'Entrée de test pour le suivi du projet');
$entries = $journal->lister(5);

if (empty($entries) || !isset($entries[0]['message'])) {
    throw new RuntimeException('Le journal de suivi ne renvoie pas d’entrée valide.');
}

$suivi = new SuiviProjet();
$resume = $suivi->generer_resume();

if (!isset($resume['fait'], $resume['en_cours'], $resume['a_faire'], $resume['bloque'], $resume['annule'])) {
    throw new RuntimeException('Le résumé du backlog n’est pas complet.');
}

echo "Test SuiviProjet : OK\n";
