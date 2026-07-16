<?php
/**
 * DocumentObligatoire — suivi des pièces à fournir pour un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class DocumentObligatoire
{
    private $id_document;
    private $nom;
    private $statut;

    public function __construct(array $donnees = [])
    {
        $this->nom = nettoyer_chaine($donnees['nom'] ?? '');
        $this->statut = $donnees['statut'] ?? 'manquant';
    }

    public function marquer_recu(): self
    {
        $this->statut = 'recu';
        return $this;
    }

    public function marquer_manquant(): self
    {
        $this->statut = 'manquant';
        return $this;
    }

    public function get_statut(): string
    {
        return $this->statut;
    }

    public function get_nom(): string
    {
        return $this->nom;
    }
}
