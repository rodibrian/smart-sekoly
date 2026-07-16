<?php
/**
 * HeureSupplementaire — entité de gestion des heures supplémentaires.
 */
class HeureSupplementaire
{
    private $id_heure_sup;
    private $id_enseignant;
    private $id_classe;
    private $id_matiere;
    private $date_heure;
    private $nombre_heures;
    private $taux;
    private $montant;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_heure_sup = $donnees['id_heure_sup'] ?? null;
        $this->id_enseignant = $donnees['id_enseignant'] ?? null;
        $this->id_classe = $donnees['id_classe'] ?? null;
        $this->id_matiere = $donnees['id_matiere'] ?? null;
        $this->date_heure = $donnees['date_heure'] ?? date('Y-m-d');
        $this->nombre_heures = isset($donnees['nombre_heures']) ? (float) $donnees['nombre_heures'] : 0.0;
        $this->taux = isset($donnees['taux']) ? (float) $donnees['taux'] : 0.0;
        $this->montant = isset($donnees['montant']) ? (float) $donnees['montant'] : $this->nombre_heures * $this->taux;
        $this->statut = $donnees['statut'] ?? 'proposee';
    }

    public function get_id_heure_sup()
    {
        return $this->id_heure_sup;
    }

    public function get_id_enseignant()
    {
        return $this->id_enseignant;
    }

    public function get_id_classe()
    {
        return $this->id_classe;
    }

    public function get_id_matiere()
    {
        return $this->id_matiere;
    }

    public function get_date_heure()
    {
        return $this->date_heure;
    }

    public function get_nombre_heures()
    {
        return $this->nombre_heures;
    }

    public function get_taux()
    {
        return $this->taux;
    }

    public function get_montant()
    {
        return $this->montant;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function valider(): self
    {
        $this->statut = 'validee';
        return $this;
    }

    public function payer(): self
    {
        $this->statut = 'payee';
        return $this;
    }

    public function est_proposee(): bool
    {
        return $this->statut === 'proposee';
    }
}
