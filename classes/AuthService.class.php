<?php

class AuthService
{
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

        $role = $utilisateur['role'] ?? 'admin';
        $rolesPermissions = [
            'admin' => ['users.read', 'users.write', 'eleves.read', 'eleves.write', 'finance.read', 'finance.write'],
            'directeur' => ['eleves.read', 'finance.read', 'reports.read'],
            'enseignant' => ['eleves.read'],
        ];

        return in_array($permission, $rolesPermissions[$role] ?? [], true);
    }
}
