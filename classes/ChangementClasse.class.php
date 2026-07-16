<?php
/**
 * ChangementClasse — suivi du passage d’un élève d’une classe à une autre.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class ChangementClasse
{
    private $id_eleve;
    private $ancienne_classe;
    private $nouvelle_classe;
    private $date_changement;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_eleve = (int) ($donnees['id_eleve'] ?? 0);
        $this->ancienne_classe = nettoyer_chaine($donnees['ancienne_classe'] ?? '');
        $this->nouvelle_classe = nettoyer_chaine($donnees['nouvelle_classe'] ?? '');
        $this->date_changement = nettoyer_chaine($donnees['date_changement'] ?? date('Y-m-d'));
        $this->statut = nettoyer_chaine($donnees['statut'] ?? 'en_attente');
    }

    public function valider(): self
    {
        $this->statut = 'valide';
        return $this;
    }

    public function get_statut(): string
    {
        return $this->statut;
    }

    public function get_ancienne_classe(): string
    {
        return $this->ancienne_classe;
    }

    public function get_nouvelle_classe(): string
    {
        return $this->nouvelle_classe;
    }
}
