<?php
/**
 * Contrat — entité de contrat d'un enseignant.
 */
class Contrat
{
    private $id_contrat;
    private $id_enseignant;
    private $type_contrat;
    private $date_debut;
    private $date_fin;
    private $salaire;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_contrat = $donnees['id_contrat'] ?? null;
        $this->id_enseignant = $donnees['id_enseignant'] ?? null;
        $this->type_contrat = $donnees['type_contrat'] ?? 'permanent';
        $this->date_debut = $donnees['date_debut'] ?? date('Y-m-d');
        $this->date_fin = $donnees['date_fin'] ?? null;
        $this->salaire = isset($donnees['salaire']) ? (float) $donnees['salaire'] : 0.0;
        $this->statut = $donnees['statut'] ?? 'actif';
    }

    public function get_id_contrat()
    {
        return $this->id_contrat;
    }

    public function get_id_enseignant()
    {
        return $this->id_enseignant;
    }

    public function get_type_contrat()
    {
        return $this->type_contrat;
    }

    public function get_date_debut()
    {
        return $this->date_debut;
    }

    public function get_date_fin()
    {
        return $this->date_fin;
    }

    public function get_salaire()
    {
        return $this->salaire;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function terminer(string $date_fin = null): self
    {
        $this->statut = 'termine';
        $this->date_fin = $date_fin ?? date('Y-m-d');
        return $this;
    }

    public function annuler(): self
    {
        $this->statut = 'annule';
        return $this;
    }

    public function est_actif(): bool
    {
        return $this->statut === 'actif';
    }
}
