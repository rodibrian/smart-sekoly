<?php

class SeuilAlerte
{
    private $nom;
    private $valeur;

    public function __construct(array $donnees = [])
    {
        $this->nom = (string) ($donnees['nom'] ?? 'default');
        $this->valeur = (float) ($donnees['valeur'] ?? 0.0);
    }

    public function estDepasse(float $montant): bool
    {
        return $montant >= $this->valeur;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
}
