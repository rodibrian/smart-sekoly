<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/TypeFrais.class.php';

$typeFrais = new TypeFrais([
    'code' => 'FRAIS_INSCRIPTION',
    'libelle' => 'Frais d’inscription',
    'description' => 'Frais fixes de dossier et inscription.',
    'montant' => 150000.00,
]);

if ($typeFrais->get_code() !== 'FRAIS_INSCRIPTION') {
    throw new RuntimeException('Le code du type de frais est incorrect.');
}

if ($typeFrais->get_libelle() !== 'Frais d’inscription') {
    throw new RuntimeException('Le libellé du type de frais est incorrect.');
}

if ($typeFrais->get_montant() !== 150000.00) {
    throw new RuntimeException('Le montant du type de frais est incorrect.');
}

if (!$typeFrais->est_actif()) {
    throw new RuntimeException('Le type de frais doit être actif par défaut.');
}

echo "Test TypeFrais : OK\n";
