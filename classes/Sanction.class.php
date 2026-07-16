<?php
/**
 * Sanction — proposition et validation d’une sanction pour un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Sanction
{
    private $id_eleve;
    private $type;
    private $description;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_eleve = (int) ($donnees['id_eleve'] ?? 0);
        $this->type = nettoyer_chaine($donnees['type'] ?? 'avertissement');
        $this->description = nettoyer_chaine($donnees['description'] ?? '');
        $this->statut = nettoyer_chaine($donnees['statut'] ?? 'proposee');
    }

    public function valider(): self
    {
        $this->statut = 'validee';
        return $this;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function get_description(): string
    {
        return $this->description;
    }

    public function get_statut(): string
    {
        return $this->statut;
    }
}
