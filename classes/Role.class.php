<?php

class Role
{
    private string $libelle;
    private array $permissions;

    public function __construct(string $libelle, array $permissions = [])
    {
        $this->libelle = nettoyer_chaine($libelle);
        $this->permissions = $permissions;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function ajouterPermission(string $permission): void
    {
        if (!in_array($permission, $this->permissions, true)) {
            $this->permissions[] = $permission;
        }
    }

    public function aLaPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function toArray(): array
    {
        return [
            'libelle' => $this->libelle,
            'permissions' => $this->permissions,
        ];
    }
}
