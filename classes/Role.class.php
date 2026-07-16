<?php

class Role
{
    private $nom;
    private $permissions;

    public function __construct(string $nom, array $permissions = [])
    {
        $this->nom = $nom;
        $this->permissions = $permissions;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function aLaPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
