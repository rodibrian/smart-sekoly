<?php

class ReceiptPrinter
{
    public function generateTextReceipt(array $data): string
    {
        $lines = [];
        $lines[] = "SMART-SEKOLY";
        $lines[] = "Reçu de paiement";
        $lines[] = str_repeat('-', 32);
        $lines[] = 'N° reçu : ' . ($data['numero_recu'] ?? ($data['recu'] ?? ''));
        $lines[] = 'Date    : ' . ($data['date_paiement'] ?? ($data['date'] ?? ''));
        $lines[] = 'Montant : ' . ($data['montant'] ?? '0');
        $lines[] = 'Mode    : ' . ($data['mode_paiement'] ?? ($data['mode'] ?? ''));
        $lines[] = str_repeat('-', 32);
        $lines[] = "Merci de votre paiement.";
        $lines[] = "-- Smart-Sekoly --";

        return implode("\n", $lines) . "\n";
    }

    public function generateEscPos(array $data): string
    {
        // Simple ESC/POS-like output (text with basic commands)
        $ESC = "\x1B";
        $newline = "\n";
        $out = '';
        $out .= $ESC . "@"; // init
        $out .= $ESC . "a" . "\x01"; // center
        $out .= "SMART-SEKOLY" . $newline;
        $out .= $newline;
        $out .= $ESC . "a" . "\x00"; // left
        $out .= 'Recu: ' . ($data['numero_recu'] ?? ($data['recu'] ?? '')) . $newline;
        $out .= 'Date: ' . ($data['date_paiement'] ?? ($data['date'] ?? '')) . $newline;
        $out .= 'Montant: ' . ($data['montant'] ?? '0') . $newline;
        $out .= 'Mode: ' . ($data['mode_paiement'] ?? ($data['mode'] ?? '')) . $newline;
        $out .= $newline . $newline;
        $out .= "--- Merci ---" . $newline;
        // Feed a few lines and cut (GS V 0x00)
        $out .= str_repeat($newline, 3);
        $out .= "\x1D\x56\x00";

        return $out;
    }
}
