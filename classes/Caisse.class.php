<?php
/**
 * Caisse — entité de caisse journalière.
 */
class Caisse
{
    private $id_caisse;
    private $date_caisse;
    private $fond_de_caisse;

    public function __construct(array $donnees = [])
    {
        $this->id_caisse = $donnees['id_caisse'] ?? null;
        $this->date_caisse = $donnees['date_caisse'] ?? date('Y-m-d');
        $this->fond_de_caisse = isset($donnees['fond_de_caisse']) ? (float) $donnees['fond_de_caisse'] : 0.0;
    }

    public function get_id_caisse()
    {
        return $this->id_caisse;
    }

    public function get_date_caisse()
    {
        return $this->date_caisse;
    }

    public function get_fond_de_caisse()
    {
        return $this->fond_de_caisse;
    }

    public function ajouter_fond(float $montant): self
    {
        $this->fond_de_caisse += $montant;
        return $this;
    }

    public function retirer_fond(float $montant): self
    {
        $this->fond_de_caisse -= $montant;
        return $this;
    }
}
