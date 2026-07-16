<?php
require_once __DIR__ . '/../classes/Facture.class.php';

$facture = new Facture([
    'id_eleve' => 1,
    'numero_sequentiel' => 'FAC-2026-001',
    'date_emission' => '2026-09-01',
    'montant_total' => 225000.00,
]);

if (!$facture->est_active()) {
    throw new RuntimeException('La facture doit être active par défaut.');
}

if ($facture->get_numero_sequentiel() !== 'FAC-2026-001') {
    throw new RuntimeException('Le numéro séquentiel de facture est incorrect.');
}

$facture->annuler(5);
if (!$facture->est_annulee()) {
    throw new RuntimeException('La facture doit être annulée après appel de annuler().');
}

if ($facture->get_id_utilisateur_annulation() !== 5) {
    throw new RuntimeException('L’utilisateur d’annulation n’est pas enregistré correctement.');
}

echo "Test Facture : OK\n";
