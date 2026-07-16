<?php
require_once __DIR__ . '/../classes/SequenceNumerotation.class.php';
require_once __DIR__ . '/../classes/SeuilAlerte.class.php';
require_once __DIR__ . '/../classes/ModeleDocument.class.php';

$sequence = new SequenceNumerotation(['prefixe' => 'INV-', 'longueur' => 4, 'valeur_actuelle' => 7]);
$numero = $sequence->prochain();
if ($numero !== 'INV-0007') {
    throw new RuntimeException('La séquence de numérotation est incorrecte.');
}

$seuil = new SeuilAlerte(['nom' => 'salaire', 'valeur' => 100000]);
if (!$seuil->estDepasse(100000)) {
    throw new RuntimeException('Le seuil d’alerte ne déclenche pas.');
}

$modele = new ModeleDocument(['nom' => 'courrier', 'contenu' => 'Bonjour {nom}']);
if ($modele->render(['nom' => 'Mira']) !== 'Bonjour Mira') {
    throw new RuntimeException('Le modèle de document ne rend pas correctement les variables.');
}

echo "Parametrage avancé tests: OK\n";
