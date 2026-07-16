<?php
/**
 * Enseignant — profil spécialisé de la personne.
 */
class Enseignant extends Personne
{
    private $id_enseignant;
    private $matricule;
    private $date_embauche;
    private $statut_enseignant;

    public function __construct(array $donnees = [])
    {
        parent::__construct($donnees);
        $this->matricule = $donnees['matricule'] ?? '';
        $this->date_embauche = $donnees['date_embauche'] ?? date('Y-m-d');
        $this->statut_enseignant = $donnees['statut_enseignant'] ?? 'actif';
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

    public function generer_matricule($prefixe = 'ENS', $annee = null)
    {
        $annee_utilisee = $annee !== null ? (string) $annee : date('Y');
        $this->matricule = strtoupper($prefixe) . '-' . $annee_utilisee . '-' . uniqid('', false);
        return $this->matricule;
    }
}
