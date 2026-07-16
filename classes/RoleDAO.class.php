<?php

class RoleDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function listerRoles(): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->query('SELECT id_role, libelle FROM role ORDER BY libelle');
                $roles = $stmt->fetchAll();

                if (empty($roles)) {
                    $this->initialiserRolesParDefaut();
                    $stmt = $this->pdo->query('SELECT id_role, libelle FROM role ORDER BY libelle');
                    $roles = $stmt->fetchAll();
                }

                return $roles;
            } catch (Throwable $exception) {
                error_log('RoleDAO listerRoles failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['roles'])) {
            $this->initialiserRolesParDefautSession();
        }

        return array_values($_SESSION['roles']);
    }

    public function trouverRoleParId(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT id_role, libelle FROM role WHERE id_role = :id_role');
                $stmt->execute([':id_role' => $id]);
                $role = $stmt->fetch();
                return $role ?: null;
            } catch (Throwable $exception) {
                error_log('RoleDAO trouverRoleParId failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['roles'] ?? [] as $role) {
            if (($role['id_role'] ?? 0) === $id) {
                return $role;
            }
        }

        return null;
    }

    public function trouverRoleParLibelle(string $libelle): ?array
    {
        $libelle = nettoyer_chaine($libelle);
        if ($libelle === '') {
            return null;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT id_role, libelle FROM role WHERE libelle = :libelle');
                $stmt->execute([':libelle' => $libelle]);
                $role = $stmt->fetch();
                return $role ?: null;
            } catch (Throwable $exception) {
                error_log('RoleDAO trouverRoleParLibelle failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['roles'])) {
            $this->initialiserRolesParDefautSession();
        }

        foreach ($_SESSION['roles'] ?? [] as $role) {
            if (strcasecmp($role['libelle'] ?? '', $libelle) === 0) {
                return $role;
            }
        }

        return null;
    }

    public function creerRole(string $libelle): int
    {
        $libelle = nettoyer_chaine($libelle);
        if ($libelle === '') {
            return 0;
        }

        $existant = $this->trouverRoleParLibelle($libelle);
        if ($existant !== null) {
            return (int) $existant['id_role'];
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO role (libelle) VALUES (:libelle)');
                $stmt->execute([':libelle' => $libelle]);
                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $exception) {
                error_log('RoleDAO creerRole failed: ' . $exception->getMessage());
            }
        }

        if (empty($_SESSION['roles'])) {
            $this->initialiserRolesParDefautSession();
        }

        $roles = $_SESSION['roles'];
        $id = generer_identifiant($roles, 'id_role');
        $roles[$id] = ['id_role' => $id, 'libelle' => $libelle];
        $_SESSION['roles'] = $roles;

        return $id;
    }

    public function assignerRoleAPersonne(int $idPersonne, int $idRole): bool
    {
        if ($idPersonne <= 0 || $idRole <= 0) {
            return false;
        }

        if ($this->pdo instanceof PDO) {
            try {
                $this->pdo->beginTransaction();
                $stmtDelete = $this->pdo->prepare('DELETE FROM personne_role WHERE id_personne = :id_personne');
                $stmtDelete->execute([':id_personne' => $idPersonne]);

                $stmtInsert = $this->pdo->prepare('INSERT INTO personne_role (id_personne, id_role) VALUES (:id_personne, :id_role)');
                $stmtInsert->execute([':id_personne' => $idPersonne, ':id_role' => $idRole]);
                $this->pdo->commit();

                return true;
            } catch (Throwable $exception) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                error_log('RoleDAO assignerRoleAPersonne failed: ' . $exception->getMessage());
            }
        }

        $_SESSION['personne_roles'][$idPersonne] = [$idRole];
        return true;
    }

    public function listerRolesPersonne(int $idPersonne): array
    {
        if ($idPersonne <= 0) {
            return [];
        }

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare(
                    'SELECT r.id_role, r.libelle FROM role r JOIN personne_role pr ON pr.id_role = r.id_role WHERE pr.id_personne = :id_personne ORDER BY r.libelle'
                );
                $stmt->execute([':id_personne' => $idPersonne]);
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('RoleDAO listerRolesPersonne failed: ' . $exception->getMessage());
            }
        }

        $result = [];
        foreach ($_SESSION['personne_roles'][$idPersonne] ?? [] as $idRole) {
            $role = $this->trouverRoleParId($idRole);
            if ($role !== null) {
                $result[] = $role;
            }
        }

        return $result;
    }

    public function trouverPremierRolePersonne(int $idPersonne): ?array
    {
        $roles = $this->listerRolesPersonne($idPersonne);
        return $roles[0] ?? null;
    }

    private function initialiserRolesParDefaut(): void
    {
        $rolesParDefaut = [
            'élève',
            'enseignant',
            'parent',
            'directeur',
            'secrétaire',
            'comptable',
            'surveillant',
            'DRH',
            'caissière',
        ];

        try {
            $stmt = $this->pdo->prepare('INSERT IGNORE INTO role (libelle) VALUES (:libelle)');
            foreach ($rolesParDefaut as $libelle) {
                $stmt->execute([':libelle' => $libelle]);
            }
        } catch (Throwable $exception) {
            error_log('RoleDAO initialiserRolesParDefaut failed: ' . $exception->getMessage());
        }
    }

    private function initialiserRolesParDefautSession(): void
    {
        $defaultRoles = [
            ['id_role' => 1, 'libelle' => 'élève'],
            ['id_role' => 2, 'libelle' => 'enseignant'],
            ['id_role' => 3, 'libelle' => 'parent'],
            ['id_role' => 4, 'libelle' => 'directeur'],
            ['id_role' => 5, 'libelle' => 'secrétaire'],
            ['id_role' => 6, 'libelle' => 'comptable'],
            ['id_role' => 7, 'libelle' => 'surveillant'],
            ['id_role' => 8, 'libelle' => 'DRH'],
            ['id_role' => 9, 'libelle' => 'caissière'],
        ];

        $_SESSION['roles'] = [];
        foreach ($defaultRoles as $role) {
            $_SESSION['roles'][$role['id_role']] = $role;
        }
    }
}
