<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/Impayes.class.php';
require_once __DIR__ . '/../classes/Relance.class.php';
require_once __DIR__ . '/../classes/RapportFinance.class.php';

$echeances = [
    ['id_echeance' => 1, 'id_facture' => 10, 'date_echeance' => '2026-07-01', 'montant_prevu' => 50000.00, 'statut_echeance' => 'en_retard'],
    ['id_echeance' => 2, 'id_facture' => 11, 'date_echeance' => '2026-07-20', 'montant_prevu' => 150000.00, 'statut_echeance' => 'partielle'],
];
$paiements = [
    ['id_echeance' => 2, 'montant' => 100000.00],
];

$impaies = new Impayes();
$detected = $impaies->detecter($echeances, $paiements);
if (count($detected) !== 2) {
    throw new RuntimeException('Le détecteur d’impayés doit retourner 2 éléments.');
}
if ($detected[0]['id_echeance'] !== 1 || $detected[0]['statut'] !== 'impaye') {
    throw new RuntimeException('La première échéance doit être marquée impayée.');
}
if ($detected[1]['id_echeance'] !== 2 || $detected[1]['statut'] !== 'partiellement_reglee') {
    throw new RuntimeException('La deuxième échéance doit être marquée partiellement réglée.');
}

$relance = new Relance();
$message = $relance->genererMessage($detected[0]);
if (strpos($message, 'impayée') === false) {
    throw new RuntimeException('Le message de relance doit mentionner l’échéance impayée.');
}

$rapport = new RapportFinance();
$summary = $rapport->generer($echeances, $paiements);
if ((float) $summary['montant_total_prevu'] !== 200000.0) {
    throw new RuntimeException('Le montant total prévu est incorrect.');
}
if ((int) $summary['nombre_impayes'] !== 1) {
    throw new RuntimeException('Le nombre d’impayés est incorrect.');
}

echo "Finance prototype tests: OK\n";
