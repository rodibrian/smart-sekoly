<?php

class SequenceNumerotation
{
    private $prefixe;
    private $longueur;
    private $valeurActuelle;

    public function __construct(array $donnees = [])
    {
        $this->prefixe = (string) ($donnees['prefixe'] ?? '');
        $this->longueur = max(1, (int) ($donnees['longueur'] ?? 4));
        $this->valeurActuelle = max(1, (int) ($donnees['valeur_actuelle'] ?? 1));
    }

    public function prochain(): string
    {
        $numero = str_pad((string) $this->valeurActuelle, $this->longueur, '0', STR_PAD_LEFT);
        $this->valeurActuelle++;
        return $this->prefixe . $numero;
    }

    public function getPrefixe(): string
    {
        return $this->prefixe;
    }
}
