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
            return 'dashboard.read';
        }

        if ($module === 'finance') {
            return self::isEcriture($action) ? 'finance.write' : 'finance.read';
        }

        if ($module === 'eleves') {
            return self::isEcriture($action) ? 'eleves.write' : 'eleves.read';
        }

        if ($module === 'roles') {
            return self::isEcriture($action) ? 'users.write' : 'users.read';
        }

        if ($module === 'communication') {
            return 'communication.read';
        }

        if ($module === 'bibliotheque') {
            return 'bibliotheque.read';
        }

        if ($module === 'portails') {
            return 'portails.read';
        }

        if ($module === 'parametrage') {
            return 'settings.read';
        }

        if ($module === 'rapports') {
            return 'reports.read';
        }

        if ($module === 'vie-scolaire') {
            return 'eleves.read';
        }

        return $module . '.read';
    }

    private static function isEcriture(string $action): bool
    {
        return (bool) preg_match('/(creer|editer|enregistrer|modifier|supprimer|valider|ajouter|update|delete)/', $action);
    }
}
