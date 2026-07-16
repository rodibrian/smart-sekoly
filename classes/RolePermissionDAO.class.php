<?php

class RolePermissionDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function listerPermissionsRole(int $idRole): array
    {
        if ($idRole <= 0) {
            return [];
        }

        $permissionDao = new PermissionDAO();

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare(
                    'SELECT p.id_permission, p.module, p.sous_module, p.action
                     FROM permission p
                     JOIN role_permission rp ON rp.id_permission = p.id_permission
                     WHERE rp.id_role = :id_role
                     ORDER BY p.module, p.sous_module, p.action'
                );
                $stmt->execute(['id_role' => $idRole]);
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('RolePermissionDAO listerPermissionsRole failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['role_permissions'])) {
            $this->initialiserPermissionsRoleParDefaut();
        }

        $permissions = [];
        foreach ($_SESSION['role_permissions'][$idRole] ?? [] as $idPermission) {
            $permission = $permissionDao->trouverPermissionParId((int) $idPermission);
            if ($permission !== null) {
                $permissions[] = $permission;
            }
        }

        return $permissions;
    }

    public function assignerPermissionsRole(int $idRole, array $permissionIds): bool
    {
        if ($idRole <= 0) {
            return false;
        }

        $permissionIds = array_values(array_filter(array_map('intval', $permissionIds), fn($id) => $id > 0));

        if ($this->pdo instanceof PDO) {
            try {
                $this->pdo->beginTransaction();
                $stmtDelete = $this->pdo->prepare('DELETE FROM role_permission WHERE id_role = :id_role');
                $stmtDelete->execute(['id_role' => $idRole]);

                if (!empty($permissionIds)) {
                    $stmtInsert = $this->pdo->prepare('INSERT INTO role_permission (id_role, id_permission) VALUES (:id_role, :id_permission)');
                    foreach ($permissionIds as $permissionId) {
                        $stmtInsert->execute(['id_role' => $idRole, 'id_permission' => $permissionId]);
                    }
                }

                $this->pdo->commit();
                return true;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                error_log('RolePermissionDAO assignerPermissionsRole failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['role_permissions'])) {
            $this->initialiserPermissionsRoleParDefaut();
        }

        $_SESSION['role_permissions'][$idRole] = $permissionIds;
        return true;
    }

    private function initialiserPermissionsRoleParDefaut(): void
    {
        if (!isset($_SESSION['role_permissions']) || !is_array($_SESSION['role_permissions'])) {
            $_SESSION['role_permissions'] = [];
        }

        $permissionDao = new PermissionDAO();
        $permissions = $permissionDao->listerPermissions();

        $defaultRoleMap = [
            'élève' => ['eleves.lire', 'tableau-de-bord.lire'],
            'enseignant' => ['eleves.lire', 'tableau-de-bord.lire'],
            'parent' => ['tableau-de-bord.lire'],
            'directeur' => ['tableau-de-bord.lire', 'finance.lire', 'permissions.lire', 'roles.lire'],
            'secrétaire' => ['finance.lire', 'eleves.lire'],
            'comptable' => ['finance.lire', 'finance.modifier', 'tableau-de-bord.lire'],
            'surveillant' => ['vie-scolaire.lire', 'tableau-de-bord.lire'],
            'drh' => ['tableau-de-bord.lire', 'permissions.lire'],
            'caissière' => ['finance.lire', 'finance.modifier'],
        ];

        $roleDao = new RoleDAO();
        foreach ($roleDao->listerRoles() as $role) {
            $libelle = strtolower($role['libelle'] ?? '');
            $permissionCodes = $defaultRoleMap[$libelle] ?? ['tableau-de-bord.lire'];
            $permissionIds = [];

            foreach ($permissions as $permission) {
                $code = $permission['module'];
                if (!empty($permission['sous_module'])) {
                    $code .= '.' . $permission['sous_module'];
                }
                $code .= '.' . $permission['action'];

                if (in_array($code, $permissionCodes, true)) {
                    $permissionIds[] = $permission['id_permission'];
                }
            }

            $_SESSION['role_permissions'][$role['id_role']] = $permissionIds;
        }
    }
}
