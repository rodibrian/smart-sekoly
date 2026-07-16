<?php
/**
 * Facture — entité de facturation d'un élève.
 */
class Facture
{
    private $id_facture;
    private $id_eleve;
    private $numero_sequentiel;
    private $date_emission;
    private $montant_total;
    private $statut;
    private $date_annulation;
    private $id_utilisateur_annulation;

    public function __construct(array $donnees = [])
    {
        $this->id_facture = $donnees['id_facture'] ?? null;
        $this->id_eleve = $donnees['id_eleve'] ?? null;
        $this->numero_sequentiel = $donnees['numero_sequentiel'] ?? '';
        $this->date_emission = $donnees['date_emission'] ?? date('Y-m-d');
        $this->montant_total = isset($donnees['montant_total']) ? (float) $donnees['montant_total'] : 0.0;
        $this->statut = $donnees['statut'] ?? 'active';
        $this->date_annulation = $donnees['date_annulation'] ?? null;
        $this->id_utilisateur_annulation = $donnees['id_utilisateur_annulation'] ?? null;
    }

    public function get_id_facture()
    {
        return $this->id_facture;
    }

    public function get_id_eleve()
    {
        return $this->id_eleve;
    }

    public function get_numero_sequentiel()
    {
        return $this->numero_sequentiel;
    }

    public function get_date_emission()
    {
        return $this->date_emission;
    }

    public function get_montant_total()
    {
        return $this->montant_total;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function get_date_annulation()
    {
        return $this->date_annulation;
    }

    public function get_id_utilisateur_annulation()
    {
        return $this->id_utilisateur_annulation;
    }

    public function annuler(int $idUtilisateurAnnulation = null): self
    {
        $this->statut = 'annulee';
        $this->date_annulation = date('Y-m-d H:i:s');
        $this->id_utilisateur_annulation = $idUtilisateurAnnulation;
        return $this;
    }

    public function est_active(): bool
    {
        return $this->statut === 'active';
    }

    public function est_annulee(): bool
    {
        return $this->statut === 'annulee';
    }

    /**
     * Calcule le montant net de la facture après application des remises.
     *
     * @param Remise[] $remises
     * @return float
     */
    public function calculer_montant_net(array $remises = []): float
    {
        $montant_net = $this->montant_total;

        foreach ($remises as $remise) {
            if ($remise instanceof Remise) {
                $montant_net -= $remise->calcule_montant_remise($this->montant_total);
            }
        }

        return max(0.0, round($montant_net, 2));
    }
}
