<?php
/**
 * TransfertEleve — suivi d’un transfert d’élève entre établissements ou classes.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class TransfertEleve
{
    private $id_eleve;
    private $type;
    private $destination;
    private $date_transfert;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_eleve = (int) ($donnees['id_eleve'] ?? 0);
        $this->type = nettoyer_chaine($donnees['type'] ?? 'depart');
        $this->destination = nettoyer_chaine($donnees['destination'] ?? '');
        $this->date_transfert = nettoyer_chaine($donnees['date_transfert'] ?? date('Y-m-d'));
        $this->statut = nettoyer_chaine($donnees['statut'] ?? 'en_attente');
    }

    public function valider(): self
    {
        $this->statut = 'valide';
        return $this;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function get_destination(): string
    {
        return $this->destination;
    }

    public function get_statut(): string
    {
        return $this->statut;
    }
}
