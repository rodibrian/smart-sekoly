<?php

class AnnonceEvenementCarnet
{
    private int $id;
    private int $id_eleve;
    private string $titre;
    private string $description;
    private string $type;
    private string $date;

    public function __construct(array $donnees = [])
    {
        $this->id = (int) ($donnees['id'] ?? 0);
        $this->id_eleve = (int) ($donnees['id_eleve'] ?? 0);
        $this->titre = nettoyer_chaine($donnees['titre'] ?? '');
        $this->description = nettoyer_chaine($donnees['description'] ?? '');
        $this->type = nettoyer_chaine($donnees['type'] ?? 'info');
        $this->date = $donnees['date'] ?? date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'id_eleve' => $this->id_eleve,
            'titre' => $this->titre,
            'description' => $this->description,
            'type' => $this->type,
            'date' => $this->date,
        ];
    }
}
