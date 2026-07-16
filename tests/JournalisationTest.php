<?php
session_start();

require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/JournalAudit.class.php';
require_once __DIR__ . '/../classes/JournalConnexion.class.php';

$journalAudit = new JournalAudit();
$journalConnexion = new JournalConnexion();

$_SESSION['journal_audit'] = [];
$_SESSION['journal_connexion'] = [];

$reussiAudit = $journalAudit->enregistrer([
    'id_utilisateur' => 1,
    'type_action' => 'modification',
    'table_concernee' => 'eleve',
    'id_enregistrement_concerne' => 10,
    'ancienne_valeur' => ['nom' => 'Rakoto'],
    'nouvelle_valeur' => ['nom' => 'Rakotosy'],
]);

if (!$reussiAudit) {
    throw new RuntimeException('Échec de l’enregistrement dans le journal d’audit.');
}

$reussiConnexion = $journalConnexion->enregistrer([
    'id_utilisateur' => 1,
    'adresse_ip' => '127.0.0.1',
    'navigateur' => 'Mozilla/5.0',
]);

if (!$reussiConnexion) {
    throw new RuntimeException('Échec de l’enregistrement dans le journal de connexion.');
}

if (count($journalAudit->lister()) !== 1) {
    throw new RuntimeException('Le journal d’audit ne contient pas l’entrée attendue.');
}

if (count($journalConnexion->lister()) !== 1) {
    throw new RuntimeException('Le journal de connexion ne contient pas l’entrée attendue.');
}

echo "Journalisation tests : OK\n";
