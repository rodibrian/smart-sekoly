<?php

if (!function_exists('nettoyer_chaine')) {
    require_once __DIR__ . '/../includes/fonctions.php';
}

if (!class_exists('RoleDAO', false)) {
    require_once __DIR__ . '/RoleDAO.class.php';
}

if (!class_exists('RolePermissionDAO', false)) {
    require_once __DIR__ . '/RolePermissionDAO.class.php';
}

class AuthService
{
    private const ACTION_EQUIVALENCES = [
        'read' => 'lire',
        'write' => 'modifier',
        'create' => 'creer',
        'update' => 'modifier',
        'delete' => 'supprimer',
        'validate' => 'valider',
        'export' => 'exporter',
    ];

    public function connecter(array $utilisateur): void
    {
        $_SESSION['auth_utilisateur'] = [
            'id' => (int) ($utilisateur['id'] ?? 0),
            'nom' => (string) ($utilisateur['nom'] ?? ''),
            'email' => (string) ($utilisateur['email'] ?? ''),
            'role' => (string) ($utilisateur['role'] ?? 'admin'),
        ];

        try {
            if (!class_exists('JournalConnexion', false)) {
                require_once __DIR__ . '/JournalConnexion.class.php';
            }
            $journal = new JournalConnexion();
            $journal->enregistrer([
                'id_utilisateur' => (int) ($utilisateur['id'] ?? 0),
                'adresse_ip' => nettoyer_chaine($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
                'navigateur' => nettoyer_chaine($_SERVER['HTTP_USER_AGENT'] ?? ''),
            ]);
        } catch (Throwable $exception) {
            error_log('JournalConnexion logging failed: ' . $exception->getMessage());
        }
    }

    public function deconnecter(): void
    {
        unset($_SESSION['auth_utilisateur']);
    }

    public function estConnecte(): bool
    {
        return !empty($_SESSION['auth_utilisateur']['email']);
    }

    public function getUtilisateurConnecte(): ?array
    {
        return $this->estConnecte() ? $_SESSION['auth_utilisateur'] : null;
    }

    public function aLaPermission(string $permission): bool
    {
        $utilisateur = $this->getUtilisateurConnecte();
        if ($utilisateur === null) {
            return false;
        }

        $role = strtolower($utilisateur['role'] ?? 'admin');
        if ($role === 'admin') {
            return true;
        }

        $permission = $this->normaliserPermission($permission);

        $roleDao = new RoleDAO();
        $roleData = $roleDao->trouverRoleParLibelle($role);
        if ($roleData === null) {
            return false;
        }

        $rolePermissionDao = new RolePermissionDAO();
        $permissions = $rolePermissionDao->listerPermissionsRole((int) $roleData['id_role']);

        foreach ($permissions as $permissionEnregistree) {
            $permissionCode = $permissionEnregistree['module'];
            if (!empty($permissionEnregistree['sous_module'])) {
                $permissionCode .= '.' . $permissionEnregistree['sous_module'];
            }
            $permissionCode .= '.' . $permissionEnregistree['action'];

            if ($permissionCode === $permission) {
                return true;
            }
        }

        return false;
    }

    private function normaliserPermission(string $permission): string
    {
        $permission = strtolower(trim($permission));
        if ($permission === '') {
            return $permission;
        }

        $elements = explode('.', $permission);
        $action = array_pop($elements);
        $action = self::ACTION_EQUIVALENCES[$action] ?? $action;

        $elements[] = $action;
        return implode('.', $elements);
    }
}
