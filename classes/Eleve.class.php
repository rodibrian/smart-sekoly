<?php
/**
 * Eleve — profil spécialisé de la personne.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Eleve extends Personne
{
    private $id_eleve;
    private $matricule;
    private $date_entree;
    private $statut_scolaire;

    public function __construct(array $donnees = [])
    {
        parent::__construct($donnees);
        $this->matricule = $donnees['matricule'] ?? '';
        $this->date_entree = $donnees['date_entree'] ?? date('Y-m-d');
        $this->statut_scolaire = $donnees['statut_scolaire'] ?? 'actif';
    }

    public function set_matricule($matricule)
    {
        $this->matricule = nettoyer_chaine($matricule);
        return $this;
    }

    public function get_matricule()
    {
        return $this->matricule;
    }

    public function generer_matricule($prefixe = 'EL', $annee = null)
    {
        $annee_utilisee = $annee !== null ? (string) $annee : date('Y');
        $this->matricule = strtoupper($prefixe) . '-' . $annee_utilisee . '-' . uniqid('', false);
        return $this->matricule;
    }
}
