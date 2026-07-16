<?php
/**
 * Salaire — entité de gestion de salaire d'un enseignant.
 */
class Salaire
{
    private $id_salaire;
    private $id_enseignant;
    private $periode;
    private $montant_brut;
    private $montant_net;
    private $retenues;
    private $statut;
    private $date_paiement;

    public function __construct(array $donnees = [])
    {
        $this->id_salaire = $donnees['id_salaire'] ?? null;
        $this->id_enseignant = $donnees['id_enseignant'] ?? null;
        $this->periode = $donnees['periode'] ?? date('Y-m');
        $this->montant_brut = isset($donnees['montant_brut']) ? (float) $donnees['montant_brut'] : 0.0;
        $this->retenues = isset($donnees['retenues']) ? (float) $donnees['retenues'] : 0.0;
        $this->montant_net = isset($donnees['montant_net']) ? (float) $donnees['montant_net'] : max(0.0, $this->montant_brut - $this->retenues);
        $this->statut = $donnees['statut'] ?? 'en_attente';
        $this->date_paiement = $donnees['date_paiement'] ?? null;
    }

    public static function calculerPourContrat(Contrat $contrat, array $options = []): self
    {
        $type = $contrat->get_type_contrat();
        $salaire = $contrat->get_salaire();

        switch ($type) {
            case 'horaire':
                $heures = $options['heures'] ?? 100;
                $taux = $salaire > 0 ? $salaire : 15000;
                $montant_brut = $taux * $heures;
                $retenues = $montant_brut * 0.10;
                break;

            case 'CDD':
                $heures = $options['heures'] ?? 90;
                $taux = $salaire > 0 ? $salaire : 18000;
                $montant_brut = $taux * $heures;
                $retenues = $montant_brut * 0.12;
                break;

            case 'permanent':
            default:
                $montant_brut = $salaire > 0 ? $salaire : 500000;
                $retenues = $montant_brut * 0.15;
                break;
        }

        return new self([
            'id_enseignant' => $contrat->get_id_enseignant(),
            'periode' => $options['periode'] ?? date('Y-m'),
            'montant_brut' => $montant_brut,
            'retenues' => $retenues,
            'statut' => $options['statut'] ?? 'en_attente',
        ]);
    }

    public function get_id_salaire()
    {
        return $this->id_salaire;
    }

    public function get_id_enseignant()
    {
        return $this->id_enseignant;
    }

    public function get_periode()
    {
        return $this->periode;
    }

    public function get_montant_brut()
    {
        return $this->montant_brut;
    }

    public function get_montant_net()
    {
        return $this->montant_net;
    }

    public function get_retenues()
    {
        return $this->retenues;
    }

    public function get_statut()
    {
        return $this->statut;
    }

    public function get_date_paiement()
    {
        return $this->date_paiement;
    }

    public function valider(): self
    {
        $this->statut = 'valide';
        return $this;
    }

    public function payer(string $date_paiement = null): self
    {
        $this->statut = 'paye';
        $this->date_paiement = $date_paiement ?? date('Y-m-d');
        return $this;
    }

    public function est_paye(): bool
    {
        return $this->statut === 'paye';
    }
}
