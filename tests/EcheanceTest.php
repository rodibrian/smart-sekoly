<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Echeance.class.php';

$echeance = new Echeance([
    'id_facture' => 1,
    'date_echeance' => '2026-10-15',
    'montant_prevu' => 75000.00,
]);

if ($echeance->get_id_facture() !== 1) {
    throw new RuntimeException('L’ID de facture de l’échéance est incorrect.');
}

if ($echeance->get_date_echeance() !== '2026-10-15') {
    throw new RuntimeException('La date d’échéance est incorrecte.');
}

if (!$echeance->est_en_retard()) {
    throw new RuntimeException('Le statut par défaut de l’échéance doit être en_retard.');
}

$echeance->marquer_payee();
if (!$echeance->est_payee()) {
    throw new RuntimeException('L’échéance doit être marquée payée.');
}

echo "Test Echeance : OK\n";
