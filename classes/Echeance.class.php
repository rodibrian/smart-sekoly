<?php
/**
 * Echeance — entité de paiement échelonné attachée à une facture.
 */
class Echeance
{
    private $id_echeance;
    private $id_facture;
    private $date_echeance;
    private $montant_prevu;
    private $statut_echeance;

    public function __construct(array $donnees = [])
    {
        $this->id_echeance = $donnees['id_echeance'] ?? null;
        $this->id_facture = $donnees['id_facture'] ?? null;
        $this->date_echeance = $donnees['date_echeance'] ?? date('Y-m-d');
        $this->montant_prevu = isset($donnees['montant_prevu']) ? (float) $donnees['montant_prevu'] : 0.0;
        $this->statut_echeance = $donnees['statut_echeance'] ?? 'en_retard';
    }

    public function get_id_echeance()
    {
        return $this->id_echeance;
    }

    public function get_id_facture()
    {
        return $this->id_facture;
    }

    public function get_date_echeance()
    {
        return $this->date_echeance;
    }

    public function get_montant_prevu()
    {
        return $this->montant_prevu;
    }

    public function get_statut_echeance()
    {
        return $this->statut_echeance;
    }

    public function est_payee(): bool
    {
        return $this->statut_echeance === 'payee';
    }

    public function est_partielle(): bool
    {
        return $this->statut_echeance === 'partielle';
    }

    public function est_en_retard(): bool
    {
        return $this->statut_echeance === 'en_retard';
    }

    public function marquer_payee(): self
    {
        $this->statut_echeance = 'payee';
        return $this;
    }

    public function marquer_partielle(): self
    {
        $this->statut_echeance = 'partielle';
        return $this;
    }

    public function set_statut(string $statut): self
    {
        $statuts_valides = ['payee', 'partielle', 'en_retard'];
        if (in_array($statut, $statuts_valides, true)) {
            $this->statut_echeance = $statut;
        }
        return $this;
    }
}
