<?php

/**
 * Classe de message interne.
 */
class Message
{
    private int $id;
    private string $destinataire;
    private string $contenu;
    private string $date;

    public function __construct(array $donnees)
    {
        $this->id = isset($donnees['id']) ? (int) $donnees['id'] : 0;
        $this->destinataire = nettoyer_chaine($donnees['destinataire'] ?? '');
        $this->contenu = nettoyer_chaine($donnees['contenu'] ?? '');
        $this->date = $donnees['date'] ?? date('d/m/Y H:i');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'destinataire' => $this->destinataire,
            'contenu' => $this->contenu,
            'date' => $this->date,
        ];
    }
}
