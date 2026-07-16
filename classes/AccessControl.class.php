<?php

class AccessControl
{
    public static function estRoutePublique(string $module, string $action): bool
    {
        $module = strtolower(trim($module));
        $action = strtolower(trim($action));

        if ($module === 'auth' && in_array($action, ['login', 'connexion'], true)) {
            return true;
        }

        if ($module === 'installation') {
            return true;
        }

        return false;
    }

    public static function verifierAcces(string $module, string $action): bool
    {
        if (self::estRoutePublique($module, $action)) {
            return true;
        }

        $authService = new AuthService();
        if (!$authService->estConnecte()) {
            return false;
        }

        $permission = self::permissionRequise($module, $action);
        if ($permission === null) {
            return true;
        }

        return $authService->aLaPermission($permission);
    }

    public static function permissionRequise(string $module, string $action): ?string
    {
        $module = strtolower(trim($module));
        $action = strtolower(trim($action));

        if ($module === 'auth') {
            return null;
        }

        if ($module === 'tableau-de-bord') {
            return 'tableau-de-bord.lire';
        }

        if ($module === 'finance') {
            return self::isEcriture($action) ? 'finance.modifier' : 'finance.lire';
        }

        if ($module === 'eleves') {
            return self::isEcriture($action) ? 'eleves.modifier' : 'eleves.lire';
        }

        if ($module === 'roles') {
            return self::isEcriture($action) ? 'roles.modifier' : 'roles.lire';
        }

        if ($module === 'permissions') {
            return self::isEcriture($action) ? 'permissions.modifier' : 'permissions.lire';
        }

        if ($module === 'communication') {
            return 'communication.lire';
        }

        if ($module === 'bibliotheque') {
            return 'bibliotheque.lire';
        }

        if ($module === 'portails') {
            return 'portails.lire';
        }

        if ($module === 'parametrage') {
            return 'parametrage.lire';
        }

        if ($module === 'rapports') {
            return 'rapports.lire';
        }

        if ($module === 'vie-scolaire') {
            return 'vie-scolaire.lire';
        }

        return $module . '.lire';
    }

    private static function isEcriture(string $action): bool
    {
        return (bool) preg_match('/(creer|editer|enregistrer|modifier|supprimer|valider|ajouter|update|delete)/', $action);
    }
}
