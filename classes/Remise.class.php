<?php
/**
 * Remise — entité de remise financière.
 */
class Remise
{
    private $id_remise;
    private $type_remise;
    private $valeur_remise;
    private $motif;
    private $id_utilisateur_validation;

    public function __construct(array $donnees = [])
    {
        $this->id_remise = $donnees['id_remise'] ?? null;
        $this->type_remise = nettoyer_chaine($donnees['type_remise'] ?? '');
        $this->valeur_remise = isset($donnees['valeur_remise']) ? (float) $donnees['valeur_remise'] : 0.0;
        $this->motif = nettoyer_chaine($donnees['motif'] ?? '');
        $this->id_utilisateur_validation = $donnees['id_utilisateur_validation'] ?? null;
    }

    public function get_id_remise()
    {
        return $this->id_remise;
    }

    public function get_type_remise()
    {
        return $this->type_remise;
    }

    public function get_valeur_remise()
    {
        return $this->valeur_remise;
    }

    public function get_motif()
    {
        return $this->motif;
    }

    public function get_id_utilisateur_validation()
    {
        return $this->id_utilisateur_validation;
    }

    public function est_pourcentage(): bool
    {
        return $this->type_remise === 'pourcentage';
    }

    public function est_montant_fixe(): bool
    {
        return $this->type_remise === 'montant_fixe';
    }

    public function calcule_montant_remise(float $montant): float
    {
        if ($this->est_pourcentage()) {
            return $montant * ($this->valeur_remise / 100.0);
        }

        return $this->valeur_remise;
    }
}
