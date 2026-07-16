<?php

/**
 * Classe d'annonce scolaire.
 */
class Annonce
{
    private int $id;
    private string $titre;
    private string $contenu;
    private string $date;

    public function __construct(array $donnees)
    {
        $this->id = isset($donnees['id']) ? (int) $donnees['id'] : 0;
        $this->titre = nettoyer_chaine($donnees['titre'] ?? '');
        $this->contenu = nettoyer_chaine($donnees['contenu'] ?? '');
        $this->date = $donnees['date'] ?? date('d/m/Y');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'date' => $this->date,
        ];
    }
}
