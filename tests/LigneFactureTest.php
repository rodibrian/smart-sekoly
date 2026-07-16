<?php
require_once __DIR__ . '/../classes/LigneFacture.class.php';

$ligne = new LigneFacture([
    'id_ligne_facture' => 1,
    'id_facture' => 1,
    'id_type_frais' => 1,
    'montant_ligne' => 125000.00,
]);

if ($ligne->get_id_facture() !== 1) {
    throw new RuntimeException('L’ID de facture de la ligne est incorrect.');
}

if ($ligne->get_montant_ligne() !== 125000.00) {
    throw new RuntimeException('Le montant de la ligne de facture est incorrect.');
}

echo "Test LigneFacture : OK\n";
