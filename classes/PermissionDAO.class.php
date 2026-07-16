<?php

class PermissionDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function listerPermissions(): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->query('SELECT id_permission, module, sous_module, action FROM permission ORDER BY module, sous_module, action');
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('PermissionDAO listerPermissions failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['permissions'])) {
            $this->initialiserPermissionsParDefaut();
        }

        return array_values($_SESSION['permissions']);
    }

    public function creerPermission(string $module, ?string $sousModule, string $action): int
    {
        $module = nettoyer_chaine($module);
        $sousModule = nettoyer_chaine($sousModule);
        $action = nettoyer_chaine($action);

        if ($module === '' || $action === '') {
            return 0;
        }

        $existant = $this->trouverPermission($module, $sousModule, $action);
        if ($existant !== null) {
            return (int) $existant['id_permission'];
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO permission (module, sous_module, action) VALUES (:module, :sous_module, :action)');
                $stmt->execute([
                    'module' => $module,
                    'sous_module' => $sousModule !== '' ? $sousModule : null,
                    'action' => $action,
                ]);
                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $exception) {
                error_log('PermissionDAO creerPermission failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['permissions'])) {
            $this->initialiserPermissionsParDefaut();
        }

        $permissions = $_SESSION['permissions'];
        $id = generer_identifiant($permissions, 'id_permission');
        $permissions[$id] = [
            'id_permission' => $id,
            'module' => $module,
            'sous_module' => $sousModule,
            'action' => $action,
        ];
        $_SESSION['permissions'] = $permissions;

        return $id;
    }

    public function trouverPermission(string $module, ?string $sousModule, string $action): ?array
    {
        $module = nettoyer_chaine($module);
        $sousModule = nettoyer_chaine($sousModule);
        $action = nettoyer_chaine($action);

        if ($module === '' || $action === '') {
            return null;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT id_permission, module, sous_module, action FROM permission WHERE module = :module AND sous_module <=> :sous_module AND action = :action LIMIT 1');
                $stmt->execute([
                    'module' => $module,
                    'sous_module' => $sousModule !== '' ? $sousModule : null,
                    'action' => $action,
                ]);
                $permission = $stmt->fetch();
                return $permission ?: null;
            } catch (Throwable $exception) {
                error_log('PermissionDAO trouverPermission failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['permissions'] ?? [] as $permission) {
            if (strcasecmp($permission['module'] ?? '', $module) === 0
                && strcasecmp((string) ($permission['sous_module'] ?? ''), $sousModule) === 0
                && strcasecmp($permission['action'] ?? '', $action) === 0) {
                return $permission;
            }
        }

        return null;
    }

    public function trouverPermissionParId(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT id_permission, module, sous_module, action FROM permission WHERE id_permission = :id_permission LIMIT 1');
                $stmt->execute(['id_permission' => $id]);
                $permission = $stmt->fetch();
                return $permission ?: null;
            } catch (Throwable $exception) {
                error_log('PermissionDAO trouverPermissionParId failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['permissions'] ?? [] as $permission) {
            if (($permission['id_permission'] ?? 0) === $id) {
                return $permission;
            }
        }

        return null;
    }

    public function modifierPermission(int $id, string $module, ?string $sousModule, string $action): bool
    {
        if ($id <= 0) {
            return false;
        }

        $module = nettoyer_chaine($module);
        $sousModule = nettoyer_chaine($sousModule);
        $action = nettoyer_chaine($action);

        if ($module === '' || $action === '') {
            return false;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('UPDATE permission SET module = :module, sous_module = :sous_module, action = :action WHERE id_permission = :id_permission');
                return $stmt->execute([
                    'module' => $module,
                    'sous_module' => $sousModule !== '' ? $sousModule : null,
                    'action' => $action,
                    'id_permission' => $id,
                ]);
            } catch (Throwable $exception) {
                error_log('PermissionDAO modifierPermission failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['permissions'] as &$permission) {
            if (($permission['id_permission'] ?? 0) === $id) {
                $permission['module'] = $module;
                $permission['sous_module'] = $sousModule;
                $permission['action'] = $action;
                return true;
            }
        }

        return false;
    }

    public function supprimerPermission(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('DELETE FROM permission WHERE id_permission = :id_permission');
                return $stmt->execute(['id_permission' => $id]);
            } catch (Throwable $exception) {
                error_log('PermissionDAO supprimerPermission failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['permissions'])) {
            return false;
        }

        foreach ($_SESSION['permissions'] as $index => $permission) {
            if (($permission['id_permission'] ?? 0) === $id) {
                unset($_SESSION['permissions'][$index]);
                $_SESSION['permissions'] = array_values($_SESSION['permissions']);
                return true;
            }
        }

        return false;
    }

    public function initialiserPermissionsParDefaut(): void
    {
        $permissions = [
            ['module' => 'finance', 'sous_module' => null, 'action' => 'lire'],
            ['module' => 'finance', 'sous_module' => null, 'action' => 'modifier'],
            ['module' => 'eleves', 'sous_module' => null, 'action' => 'lire'],
            ['module' => 'eleves', 'sous_module' => null, 'action' => 'modifier'],
            ['module' => 'roles', 'sous_module' => null, 'action' => 'lire'],
            ['module' => 'roles', 'sous_module' => null, 'action' => 'modifier'],
        ];

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT IGNORE INTO permission (module, sous_module, action) VALUES (:module, :sous_module, :action)');
                foreach ($permissions as $permission) {
                    $stmt->execute([
                        'module' => $permission['module'],
                        'sous_module' => $permission['sous_module'],
                        'action' => $permission['action'],
                    ]);
                }
            } catch (Throwable $exception) {
                error_log('PermissionDAO initialiserPermissionsParDefaut failed: ' . $exception->getMessage());
            }
        }

        $_SESSION['permissions'] = [];
        foreach ($permissions as $permission) {
            $_SESSION['permissions'][] = [
                'id_permission' => generer_identifiant($_SESSION['permissions'] ?? [], 'id_permission'),
                'module' => $permission['module'],
                'sous_module' => $permission['sous_module'],
                'action' => $permission['action'],
            ];
        }
    }
}
