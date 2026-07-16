<?php

class DocumentAdministratif
{
    private int $id;
    private string $titre;
    private string $categorie;
    private string $description;
    private string $date_creation;

    public function __construct(array $donnees = [])
    {
        $this->id = (int) ($donnees['id'] ?? 0);
        $this->titre = (string) ($donnees['titre'] ?? 'Document sans titre');
        $this->categorie = (string) ($donnees['categorie'] ?? 'Général');
        $this->description = (string) ($donnees['description'] ?? '');
        $this->date_creation = (string) ($donnees['date_creation'] ?? date('d/m/Y'));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'categorie' => $this->categorie,
            'description' => $this->description,
            'date_creation' => $this->date_creation,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
