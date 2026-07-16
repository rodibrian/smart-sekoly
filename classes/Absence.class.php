<?php
/**
 * Absence — suivi des absences d’un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Absence
{
    private $id_eleve;
    private $date_absence;
    private $motif;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_eleve = (int) ($donnees['id_eleve'] ?? 0);
        $this->date_absence = nettoyer_chaine($donnees['date_absence'] ?? date('Y-m-d'));
        $this->motif = nettoyer_chaine($donnees['motif'] ?? '');
        $this->statut = nettoyer_chaine($donnees['statut'] ?? 'en_attente');
    }

    public function valider(): self
    {
        $this->statut = 'valide';
        return $this;
    }

    public function get_date_absence(): string
    {
        return $this->date_absence;
    }

    public function get_motif(): string
    {
        return $this->motif;
    }

    public function get_statut(): string
    {
        return $this->statut;
    }
}
