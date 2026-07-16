<?php
/**
 * Conge — entité de congé pour un enseignant.
 */
class Conge
{
    private $id_conge;
    private $id_enseignant;
    private $type_conge;
    private $date_debut;
    private $date_fin;
    private $statut;
    private $raison;

    public function __construct(array $donnees = [])
    {
        $this->id_conge = $donnees['id_conge'] ?? null;
        $this->id_enseignant = $donnees['id_enseignant'] ?? null;
        $this->type_conge = $donnees['type_conge'] ?? 'personnel';
        $this->date_debut = $donnees['date_debut'] ?? date('Y-m-d');
        $this->date_fin = $donnees['date_fin'] ?? date('Y-m-d');
        $this->statut = $donnees['statut'] ?? 'demande';
        $this->raison = $donnees['raison'] ?? null;
    }

    public function get_id_conge()
    {
        return $this->id_conge;
    }

    public function get_id_enseignant()
    {
        return $this->id_enseignant;
    }

    public function get_type_conge()
    {
        return $this->type_conge;
    }

    public function get_date_debut()
    {
        return $this->date_debut;
    }

    public function get_date_fin()
    {
        return $this->date_fin;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function get_raison()
    {
        return $this->raison;
    }

    public function accepter(): self
    {
        $this->statut = 'accepte';
        return $this;
    }

    public function refuser(): self
    {
        $this->statut = 'refuse';
        return $this;
    }

    public function terminer(): self
    {
        $this->statut = 'termine';
        return $this;
    }

    public function est_demande(): bool
    {
        return $this->statut === 'demande';
    }
}
