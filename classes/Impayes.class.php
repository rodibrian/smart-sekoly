<?php

class Impayes
{
    public function detecter(array $echeances, array $paiements): array
    {
        $paiementsParEcheance = [];
        foreach ($paiements as $paiement) {
            $id = (int) ($paiement['id_echeance'] ?? 0);
            if ($id > 0) {
                $paiementsParEcheance[$id] = ($paiementsParEcheance[$id] ?? 0.0) + (float) ($paiement['montant'] ?? 0.0);
            }
        }

        $resultats = [];
        foreach ($echeances as $echeance) {
            $id = (int) ($echeance['id_echeance'] ?? 0);
            $montantPrevu = (float) ($echeance['montant_prevu'] ?? 0.0);
            $montantRecu = $paiementsParEcheance[$id] ?? 0.0;
            $statut = 'a_venir';

            if ($montantRecu >= $montantPrevu) {
                $statut = 'reglee';
            } elseif ($montantRecu > 0) {
                $statut = 'partiellement_reglee';
            } elseif ((string) ($echeance['statut_echeance'] ?? '') === 'en_retard') {
                $statut = 'impaye';
            }

            $resultats[] = [
                'id_echeance' => $id,
                'id_facture' => (int) ($echeance['id_facture'] ?? 0),
                'montant_prevu' => $montantPrevu,
                'montant_recu' => $montantRecu,
                'statut' => $statut,
            ];
        }

        return $resultats;
    }
}
