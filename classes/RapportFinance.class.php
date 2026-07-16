<?php

class RapportFinance
{
    public function generer(array $echeances, array $paiements): array
    {
        $montantTotalPrevu = 0.0;
        $montantTotalRecu = 0.0;
        $impayes = 0;

        foreach ($echeances as $echeance) {
            $montantTotalPrevu += (float) ($echeance['montant_prevu'] ?? 0.0);
        }

        foreach ($paiements as $paiement) {
            $montantTotalRecu += (float) ($paiement['montant'] ?? 0.0);
        }

        foreach ($echeances as $echeance) {
            if ((string) ($echeance['statut_echeance'] ?? '') === 'en_retard') {
                $impayes++;
            }
        }

        return [
            'montant_total_prevu' => $montantTotalPrevu,
            'montant_total_recu' => $montantTotalRecu,
            'nombre_impayes' => $impayes,
        ];
    }
}
