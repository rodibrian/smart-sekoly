<?php
/**
 * Redoublement — proposition et validation pour un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Redoublement
{
    private $id_eleve;
    private $motif;
    private $decision;

    public function __construct(array $donnees = [])
    {
        $this->id_eleve = (int) ($donnees['id_eleve'] ?? 0);
        $this->motif = nettoyer_chaine($donnees['motif'] ?? '');
        $this->decision = nettoyer_chaine($donnees['decision'] ?? 'en_attente');
    }

    public function proposer(string $motif): self
    {
        $this->motif = nettoyer_chaine($motif);
        $this->decision = 'propose';
        return $this;
    }

    public function valider(): self
    {
        $this->decision = 'valide';
        return $this;
    }

    public function get_decision(): string
    {
        return $this->decision;
    }

    public function get_motif(): string
    {
        return $this->motif;
    }
}
