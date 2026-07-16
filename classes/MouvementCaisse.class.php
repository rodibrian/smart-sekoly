<?php
/**
 * MouvementCaisse — entrée ou sortie de la caisse.
 */
class MouvementCaisse
{
    private $id_mouvement;
    private $id_caisse;
    private $type_mouvement;
    private $montant;
    private $date_creation;

    public function __construct(array $donnees = [])
    {
        $this->id_mouvement = $donnees['id_mouvement'] ?? null;
        $this->id_caisse = $donnees['id_caisse'] ?? null;
        $this->type_mouvement = $donnees['type_mouvement'] ?? 'entree';
        $this->montant = isset($donnees['montant']) ? (float) $donnees['montant'] : 0.0;
        $this->date_creation = $donnees['date_creation'] ?? date('Y-m-d H:i:s');
    }

    public function get_id_mouvement()
    {
        return $this->id_mouvement;
    }

    public function get_id_caisse()
    {
        return $this->id_caisse;
    }

    public function get_type_mouvement()
    {
        return $this->type_mouvement;
    }

    public function get_montant()
    {
        return $this->montant;
    }

    public function get_date_creation()
    {
        return $this->date_creation;
    }

    public function est_entree(): bool
    {
        return $this->type_mouvement === 'entree';
    }

    public function est_sortie(): bool
    {
        return $this->type_mouvement === 'sortie';
    }
}
