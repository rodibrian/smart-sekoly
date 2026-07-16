<?php
/**
 * PersonnelAdministratif — profil du personnel administratif.
 */
class PersonnelAdministratif extends Personne
{
    private $id_personnel;
    private $fonction;

    public function __construct(array $donnees = [])
    {
        parent::__construct($donnees);
        $this->fonction = $donnees['fonction'] ?? '';
    }

    public function set_fonction($fonction)
    {
        $this->fonction = nettoyer_chaine($fonction);
        return $this;
    }

    public function get_fonction()
    {
        return $this->fonction;
    }
}
