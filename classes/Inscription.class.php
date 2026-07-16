<?php
/**
 * Inscription — relation annuelle entre un élève, une classe et une année scolaire.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Inscription
{
    private $id_inscription;
    private $id_eleve;
    private $id_classe;
    private $id_annee;
    private $date_inscription;
    private $statut_inscription;
    private $date_creation;
    private $date_modification;
    private $date_annulation;
    private $id_utilisateur_annulation;

    public function __construct(array $donnees = [])
    {
        $this->id_eleve = $donnees['id_eleve'] ?? null;
        $this->id_classe = $donnees['id_classe'] ?? null;
        $this->id_annee = $donnees['id_annee'] ?? null;
        $this->date_inscription = $donnees['date_inscription'] ?? date('Y-m-d');
        $this->statut_inscription = $donnees['statut_inscription'] ?? 'actif';
    }

    public function set_id_eleve($id_eleve)
    {
        $this->id_eleve = (int) $id_eleve;
        return $this;
    }

    public function set_id_classe($id_classe)
    {
        $this->id_classe = $id_classe !== null ? (int) $id_classe : null;
        return $this;
    }

    public function set_id_annee($id_annee)
    {
        $this->id_annee = $id_annee !== null ? (int) $id_annee : null;
        return $this;
    }

    public function annuler($id_utilisateur_annulation = null)
    {
        $this->statut_inscription = 'annule';
        $this->date_annulation = date('Y-m-d H:i:s');
        $this->id_utilisateur_annulation = $id_utilisateur_annulation !== null ? (int) $id_utilisateur_annulation : null;
        return $this;
    }

    public function get_statut_inscription()
    {
        return $this->statut_inscription;
    }
}
