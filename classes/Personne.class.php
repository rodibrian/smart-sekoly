<?php
/**
 * Personne — entité centrale du module élève et personnel.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Personne
{
    private $id_personne;
    private $nom;
    private $prenom;
    private $date_naissance;
    private $sexe;
    private $telephone;
    private $email;
    private $adresse;
    private $piece_identite;
    private $photo;
    private $date_creation;
    private $date_modification;

    public function __construct(array $donnees = [])
    {
        $this->nom = $donnees['nom'] ?? '';
        $this->prenom = $donnees['prenom'] ?? '';
        $this->date_naissance = $donnees['date_naissance'] ?? null;
        $this->sexe = $donnees['sexe'] ?? null;
        $this->telephone = $donnees['telephone'] ?? null;
        $this->email = $donnees['email'] ?? null;
        $this->adresse = $donnees['adresse'] ?? null;
        $this->piece_identite = $donnees['piece_identite'] ?? null;
        $this->photo = $donnees['photo'] ?? null;
    }

    public function set_nom($nom)
    {
        $this->nom = nettoyer_chaine($nom);
        return $this;
    }

    public function set_prenom($prenom)
    {
        $this->prenom = nettoyer_chaine($prenom);
        return $this;
    }

    public function set_email($email)
    {
        $this->email = nettoyer_chaine($email);
        return $this;
    }

    public function get_nom()
    {
        return $this->nom;
    }

    public function get_prenom()
    {
        return $this->prenom;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_nom_complet()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
