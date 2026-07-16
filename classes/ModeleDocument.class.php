<?php

class ModeleDocument
{
    private $nom;
    private $contenu;

    public function __construct(array $donnees = [])
    {
        $this->nom = (string) ($donnees['nom'] ?? 'modele_par_defaut');
        $this->contenu = (string) ($donnees['contenu'] ?? 'Bonjour');
    }

    public function render(array $variables = []): string
    {
        $contenu = $this->contenu;
        foreach ($variables as $cle => $valeur) {
            $contenu = str_replace('{' . $cle . '}', (string) $valeur, $contenu);
        }
        return $contenu;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
}
