<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Paiement.class.php';

$paiement = new Paiement([
    'id_echeance' => 1,
    'numero_recu' => 'REC-2026-001',
    'date_paiement' => '2026-10-01 09:00:00',
    'montant' => 50000.00,
    'mode_paiement' => 'mobile_money',
    'id_utilisateur_enregistrement' => 2,
    'id_caisse' => 1,
]);

if (!$paiement->est_actif()) {
    throw new RuntimeException('Le paiement doit être actif par défaut.');
}

if ($paiement->get_mode_paiement() !== 'mobile_money') {
    throw new RuntimeException('Le mode de paiement est incorrect.');
}

$paiement->annuler(3);
if (!$paiement->est_annule()) {
    throw new RuntimeException('Le paiement doit être annulé après annulation.');
}

if ($paiement->get_id_utilisateur_annulation() !== 3) {
    throw new RuntimeException('L’ID de l’utilisateur d’annulation du paiement est incorrect.');
}

echo "Test Paiement : OK\n";
