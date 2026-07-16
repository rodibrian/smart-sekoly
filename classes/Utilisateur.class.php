<?php

class Utilisateur
{
    private $id;
    private $nom;
    private $email;
    private $mot_de_passe_hash;
    private $role;

    public function __construct(array $donnees = [])
    {
        $this->id = (int) ($donnees['id'] ?? 0);
        $this->nom = (string) ($donnees['nom'] ?? '');
        $this->email = (string) ($donnees['email'] ?? '');
        $this->mot_de_passe_hash = (string) ($donnees['mot_de_passe_hash'] ?? '');
        $this->role = (string) ($donnees['role'] ?? 'admin');
    }

    public function verifierMotDePasse(string $motDePasse): bool
    {
        return password_verify($motDePasse, $this->mot_de_passe_hash);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public static function hacherMotDePasse(string $motDePasse): string
    {
        return password_hash($motDePasse, PASSWORD_DEFAULT);
    }
}
