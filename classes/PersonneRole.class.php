<?php

class PersonneRole
{
    private int $id_personne;
    private int $id_role;
    private string $date_creation;

    public function __construct(array $donnees = [])
    {
        $this->id_personne = (int) ($donnees['id_personne'] ?? 0);
        $this->id_role = (int) ($donnees['id_role'] ?? 0);
        $this->date_creation = (string) ($donnees['date_creation'] ?? date('Y-m-d H:i:s'));
    }

    public function getIdPersonne(): int
    {
        return $this->id_personne;
    }

    public function getIdRole(): int
    {
        return $this->id_role;
    }

    public function getDateCreation(): string
    {
        return $this->date_creation;
    }

    public function toArray(): array
    {
        return [
            'id_personne' => $this->id_personne,
            'id_role' => $this->id_role,
            'date_creation' => $this->date_creation,
        ];
    }
}
