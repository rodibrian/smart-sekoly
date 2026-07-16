<?php
/**
 * Paiement — entité d'encaissement liée à une échéance.
 */
class Paiement
{
    private $id_paiement;
    private $id_echeance;
    private $numero_recu;
    private $date_paiement;
    private $montant;
    private $mode_paiement;
    private $id_utilisateur_enregistrement;
    private $id_caisse;
    private $statut;
    private $date_annulation;
    private $id_utilisateur_annulation;

    public function __construct(array $donnees = [])
    {
        $this->id_paiement = $donnees['id_paiement'] ?? null;
        $this->id_echeance = $donnees['id_echeance'] ?? null;
        $this->numero_recu = nettoyer_chaine($donnees['numero_recu'] ?? '');
        $this->date_paiement = $donnees['date_paiement'] ?? date('Y-m-d H:i:s');
        $this->montant = isset($donnees['montant']) ? (float) $donnees['montant'] : 0.0;
        $this->mode_paiement = $donnees['mode_paiement'] ?? 'espece';
        $this->id_utilisateur_enregistrement = $donnees['id_utilisateur_enregistrement'] ?? null;
        $this->id_caisse = $donnees['id_caisse'] ?? null;
        $this->statut = $donnees['statut'] ?? 'actif';
        $this->date_annulation = $donnees['date_annulation'] ?? null;
        $this->id_utilisateur_annulation = $donnees['id_utilisateur_annulation'] ?? null;
    }

    public function get_id_paiement()
    {
        return $this->id_paiement;
    }

    public function get_id_echeance()
    {
        return $this->id_echeance;
    }

    public function get_numero_recu()
    {
        return $this->numero_recu;
    }

    public function get_date_paiement()
    {
        return $this->date_paiement;
    }

    public function get_montant()
    {
        return $this->montant;
    }

    public function get_mode_paiement()
    {
        return $this->mode_paiement;
    }

    public function get_id_utilisateur_enregistrement()
    {
        return $this->id_utilisateur_enregistrement;
    }

    public function get_id_caisse()
    {
        return $this->id_caisse;
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

    public function est_actif(): bool
    {
        return $this->statut === 'actif';
    }

    public function est_annule(): bool
    {
        return $this->statut === 'annule';
    }

    public function annuler(int $idUtilisateurAnnulation = null): self
    {
        $this->statut = 'annule';
        $this->date_annulation = date('Y-m-d H:i:s');
        $this->id_utilisateur_annulation = $idUtilisateurAnnulation;
        return $this;
    }
}
