<?php
/**
 * Transfert — suivi d’un transfert d’élève conservé dans l’historique.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class Transfert
{
    private $id_transfert;
    private $id_inscription;
    private $motif;
    private $date_transfert;
    private $etablissement_origine_destination;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->id_inscription = $donnees['id_inscription'] ?? null;
        $this->motif = nettoyer_chaine($donnees['motif'] ?? '');
        $this->date_transfert = $donnees['date_transfert'] ?? date('Y-m-d');
        $this->etablissement_origine_destination = nettoyer_chaine($donnees['etablissement_origine_destination'] ?? '');
        $this->statut = $donnees['statut'] ?? 'en_cours';
    }

    public function valider()
    {
        $this->statut = 'valide';
        return $this;
    }

    public function get_statut()
    {
        return $this->statut;
    }
}
