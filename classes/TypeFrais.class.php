<?php
/**
 * TypeFrais — entité de type de frais de la finance.
 */
class TypeFrais
{
    private $id_type_frais;
    private $code;
    private $libelle;
    private $description;
    private $montant;
    private $actif;

    public function __construct(array $donnees = [])
    {
        $this->id_type_frais = $donnees['id_type_frais'] ?? null;
        $this->code = nettoyer_chaine($donnees['code'] ?? '');
        $this->libelle = nettoyer_chaine($donnees['libelle'] ?? '');
        $this->description = nettoyer_chaine($donnees['description'] ?? '');
        $this->montant = isset($donnees['montant']) ? (float) $donnees['montant'] : 0.0;
        $this->actif = isset($donnees['actif']) ? (bool) $donnees['actif'] : true;
    }

    public function get_id_type_frais()
    {
        return $this->id_type_frais;
    }

    public function get_code()
    {
        return $this->code;
    }

    public function get_libelle()
    {
        return $this->libelle;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_montant()
    {
        return $this->montant;
    }

    public function est_actif(): bool
    {
        return $this->actif;
    }

    public function activer(): self
    {
        $this->actif = true;
        return $this;
    }

    public function desactiver(): self
    {
        $this->actif = false;
        return $this;
    }
}
