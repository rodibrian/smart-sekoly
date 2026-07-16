<?php
/**
 * LigneFacture — entité de ligne de facture.
 */
class LigneFacture
{
    private $id_ligne_facture;
    private $id_facture;
    private $id_type_frais;
    private $montant_ligne;

    public function __construct(array $donnees = [])
    {
        $this->id_ligne_facture = $donnees['id_ligne_facture'] ?? null;
        $this->id_facture = $donnees['id_facture'] ?? null;
        $this->id_type_frais = $donnees['id_type_frais'] ?? null;
        $this->montant_ligne = isset($donnees['montant_ligne']) ? (float) $donnees['montant_ligne'] : 0.0;
    }

    public function get_id_ligne_facture()
    {
        return $this->id_ligne_facture;
    }

    public function get_id_facture()
    {
        return $this->id_facture;
    }

    public function get_id_type_frais()
    {
        return $this->id_type_frais;
    }

    public function get_montant_ligne()
    {
        return $this->montant_ligne;
    }
}
