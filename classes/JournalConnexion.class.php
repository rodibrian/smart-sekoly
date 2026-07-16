<?php
/**
 * Journal des connexions utilisateurs.
 */
class JournalConnexion
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function enregistrer(array $donnees): bool
    {
        $payload = [
            'id_utilisateur' => (int) ($donnees['id_utilisateur'] ?? 0),
            'adresse_ip' => nettoyer_chaine($donnees['adresse_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? ''),
            'navigateur' => nettoyer_chaine($donnees['navigateur'] ?? $_SERVER['HTTP_USER_AGENT'] ?? ''),
        ];

        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO journal_connexion (id_utilisateur, adresse_ip, navigateur) VALUES (:id_utilisateur, :adresse_ip, :navigateur)'
                );
                return $stmt->execute($payload);
            } catch (Throwable $exception) {
                error_log('JournalConnexion enregistrer failed: ' . $exception->getMessage());
                return false;
            }
        }

        $_SESSION['journal_connexion'][] = array_merge(['date_connexion' => date('Y-m-d H:i:s')], $payload);
        return true;
    }

    public function lister(int $limit = 50): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT * FROM journal_connexion ORDER BY date_connexion DESC LIMIT :limit');
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('JournalConnexion lister failed: ' . $exception->getMessage());
            }
        }

        return array_slice(array_reverse($_SESSION['journal_connexion'] ?? []), 0, $limit);
    }
}
