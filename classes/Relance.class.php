<?php

class Relance
{
    public function genererMessage(array $echeance): string
    {
        $id = (int) ($echeance['id_echeance'] ?? 0);
        $montant = number_format((float) ($echeance['montant_prevu'] ?? 0.0), 0, ',', ' ');
        $statut = $echeance['statut'] ?? 'impaye';
        $label = $statut === 'impaye' ? 'impayée' : $statut;

        return "Bonjour, l’échéance #$id est actuellement $label pour un montant de $montant Ar. Merci de procéder au règlement.";
    }
}
