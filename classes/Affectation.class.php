<?php
/**
 * Affectation — entité d'affectation pédagogique d'un enseignant.
 */
class Affectation
{
    private $id_affectation;
    private $id_enseignant;
    private $id_matiere;
    private $id_classe;
    private $id_annee;
    private $date_affectation;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_affectation = $donnees['id_affectation'] ?? null;
        $this->id_enseignant = $donnees['id_enseignant'] ?? null;
        $this->id_matiere = $donnees['id_matiere'] ?? null;
        $this->id_classe = $donnees['id_classe'] ?? null;
        $this->id_annee = $donnees['id_annee'] ?? null;
        $this->date_affectation = $donnees['date_affectation'] ?? date('Y-m-d');
        $this->statut = $donnees['statut'] ?? 'active';
    }

    public function get_id_affectation()
    {
        return $this->id_affectation;
    }

    public function get_id_enseignant()
    {
        return $this->id_enseignant;
    }

    public function get_id_matiere()
    {
        return $this->id_matiere;
    }

    public function get_id_classe()
    {
        return $this->id_classe;
    }

    public function get_id_annee()
    {
        return $this->id_annee;
    }

    public function get_date_affectation()
    {
        return $this->date_affectation;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function terminer(): self
    {
        $this->statut = 'terminee';
        return $this;
    }

    public function reaffecter(): self
    {
        $this->statut = 'reaffectee';
        return $this;
    }

    public function est_active(): bool
    {
        return $this->statut === 'active';
    }
}
