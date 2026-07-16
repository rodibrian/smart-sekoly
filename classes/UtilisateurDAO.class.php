<?php

class UtilisateurDAO
{
    private ?PDO $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
        $this->initialiserUtilisateursSession();
    }

    public function trouverParIdentifiant(string $identifiant): ?array
    {
        $identifiant = trim($identifiant);
        if ($identifiant === '') {
            return null;
        }

        if ($this->pdo !== null) {
            try {
                $stmt = $this->pdo->prepare(
                    'SELECT u.*, r.libelle AS role
                     FROM utilisateur u
                     LEFT JOIN personne_role pr ON pr.id_personne = u.id_personne
                     LEFT JOIN role r ON r.id_role = pr.id_role
                     WHERE u.identifiant = :identifiant
                     LIMIT 1'
                );
                $stmt->execute(['identifiant' => $identifiant]);
                $utilisateur = $stmt->fetch();
                if ($utilisateur !== false) {
                    $utilisateur['role'] = $utilisateur['role'] ?? 'admin';
                    $utilisateur['statut_compte'] = $utilisateur['statut_compte'] ?? 'actif';
                    $utilisateur['nombre_essais_echoues'] = (int) ($utilisateur['nombre_essais_echoues'] ?? 0);
                    return $utilisateur;
                }
            } catch (Throwable $exception) {
                error_log('UtilisateurDAO trouverParIdentifiant failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['utilisateurs'] as $utilisateur) {
            if (strcasecmp($utilisateur['identifiant'] ?? '', $identifiant) === 0) {
                $utilisateur['role'] = $utilisateur['role'] ?? 'admin';
                $utilisateur['statut_compte'] = $utilisateur['statut_compte'] ?? 'actif';
                $utilisateur['nombre_essais_echoues'] = (int) ($utilisateur['nombre_essais_echoues'] ?? 0);
                return $utilisateur;
            }
        }

        return null;
    }

    public function mettreAJourTentatives(int $id_utilisateur, int $tentatives, ?string $statut_compte = null): bool
    {
        if ($this->pdo !== null) {
            try {
                $sql = 'UPDATE utilisateur SET nombre_essais_echoues = :essais';
                if ($statut_compte !== null) {
                    $sql .= ', statut_compte = :statut';
                }
                $sql .= ' WHERE id_utilisateur = :id';
                $stmt = $this->pdo->prepare($sql);
                $params = ['essais' => $tentatives, 'id' => $id_utilisateur];
                if ($statut_compte !== null) {
                    $params['statut'] = $statut_compte;
                }
                return $stmt->execute($params);
            } catch (Throwable $exception) {
                error_log('UtilisateurDAO mettreAJourTentatives failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['utilisateurs'] as &$utilisateur) {
            if ((int) ($utilisateur['id_utilisateur'] ?? 0) === $id_utilisateur) {
                $utilisateur['nombre_essais_echoues'] = $tentatives;
                if ($statut_compte !== null) {
                    $utilisateur['statut_compte'] = $statut_compte;
                }
                return true;
            }
        }

        return false;
    }

    public function mettreAJourDerniereConnexion(int $id_utilisateur): bool
    {
        if ($this->pdo !== null) {
            try {
                $stmt = $this->pdo->prepare('UPDATE utilisateur SET date_derniere_connexion = NOW() WHERE id_utilisateur = :id');
                return $stmt->execute(['id' => $id_utilisateur]);
            } catch (Throwable $exception) {
                error_log('UtilisateurDAO mettreAJourDerniereConnexion failed: ' . $exception->getMessage());
            }
        }

        foreach ($_SESSION['utilisateurs'] as &$utilisateur) {
            if ((int) ($utilisateur['id_utilisateur'] ?? 0) === $id_utilisateur) {
                $utilisateur['date_derniere_connexion'] = date('Y-m-d H:i:s');
                return true;
            }
        }

        return false;
    }

    public function reinitialiserEssais(int $id_utilisateur): bool
    {
        return $this->mettreAJourTentatives($id_utilisateur, 0, 'actif');
    }

    private function initialiserUtilisateursSession(): void
    {
        if (!isset($_SESSION['utilisateurs']) || !is_array($_SESSION['utilisateurs'])) {
            $_SESSION['utilisateurs'] = [
                [
                    'id_utilisateur' => 1,
                    'identifiant' => 'admin',
                    'mot_de_passe_hash' => Utilisateur::hacherMotDePasse('admin'),
                    'statut_compte' => 'actif',
                    'doit_changer_mdp' => 1,
                    'nombre_essais_echoues' => 0,
                    'date_derniere_connexion' => null,
                    'role' => 'admin',
                ],
            ];
        }
    }
}
