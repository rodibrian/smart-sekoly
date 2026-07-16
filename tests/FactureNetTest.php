<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Facture.class.php';
require_once __DIR__ . '/../classes/LigneFacture.class.php';
require_once __DIR__ . '/../classes/Remise.class.php';

$facture = new Facture([
    'id_facture' => 1,
    'id_eleve' => 1,
    'numero_sequentiel' => 'FAC-2026-001',
    'date_emission' => '2026-09-01',
]);

$facture->ajouter_ligne(new LigneFacture([
    'id_ligne_facture' => 1,
    'id_facture' => 1,
    'id_type_frais' => 1,
    'montant_ligne' => 120000.00,
]));

$facture->ajouter_ligne(new LigneFacture([
    'id_ligne_facture' => 2,
    'id_facture' => 1,
    'id_type_frais' => 2,
    'montant_ligne' => 90000.00,
]));

$facture->ajouter_remise(new Remise([
    'type_remise' => 'pourcentage',
    'valeur_remise' => 10.0,
    'motif' => 'Bourse sociale',
    'id_utilisateur_validation' => 1,
]));

if ($facture->get_montant_total() !== 210000.00) {
    throw new RuntimeException('Le montant total de la facture doit être la somme des lignes.');
}

if ($facture->calculer_montant_net() !== 189000.00) {
    throw new RuntimeException('Le montant net de la facture après remise est incorrect.');
}

echo "Test Facture Net : OK\n";
