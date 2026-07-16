<?php
require_once __DIR__ . '/../classes/ReceiptPrinter.class.php';

$printer = new ReceiptPrinter();
$data = [
    'numero_recu' => 'TEST-001',
    'date_paiement' => '2026-07-16 12:00:00',
    'montant' => 12345.67,
    'mode_paiement' => 'espece',
];

$esc = $printer->generateEscPos($data);
if (strpos($esc, "SMART-SEKOLY") === false) {
    echo "FAIL: ESC/POS payload missing header\n";
    exit(1);
}

if (empty($esc)) {
    echo "FAIL: ESC/POS payload empty\n";
    exit(1);
}

echo "ReceiptPrinter ESC/POS test: OK\n";
return 0;
