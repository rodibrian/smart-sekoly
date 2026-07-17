<?php
/**
 * Journal d'audit pour les actions sensibles du système.
 */
class JournalAudit
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    public function enregistrer(array $donnees): bool
    {
        $idUtilisateur = isset($donnees['id_utilisateur']) && (int) $donnees['id_utilisateur'] > 0 ? (int) $donnees['id_utilisateur'] : null;
        $payload = [
            'id_utilisateur' => $idUtilisateur,
            'type_action' => nettoyer_chaine($donnees['type_action'] ?? ''),
            'table_concernee' => nettoyer_chaine($donnees['table_concernee'] ?? ''),
            'id_enregistrement_concerne' => isset($donnees['id_enregistrement_concerne']) ? (int) $donnees['id_enregistrement_concerne'] : null,
            'ancienne_valeur' => isset($donnees['ancienne_valeur']) ? json_encode($donnees['ancienne_valeur'], JSON_UNESCAPED_UNICODE) : null,
            'nouvelle_valeur' => isset($donnees['nouvelle_valeur']) ? json_encode($donnees['nouvelle_valeur'], JSON_UNESCAPED_UNICODE) : null,
        ];

        if ($this->pdo instanceof PDO) {
            try {
                // ensure we have a valid utilisateur id (FK constraint)
                if (empty($payload['id_utilisateur']) || !is_int($payload['id_utilisateur'])) {
                    $stmtUser = $this->pdo->query('SELECT id_utilisateur FROM utilisateur LIMIT 1');
                    $urow = $stmtUser->fetch(PDO::FETCH_ASSOC);
                    if ($urow !== false && !empty($urow['id_utilisateur'])) {
                        $payload['id_utilisateur'] = (int) $urow['id_utilisateur'];
                    } else {
                        // create a system user minimally
                        $insP = $this->pdo->prepare('INSERT INTO personne (nom, prenom, email, date_creation) VALUES (:nom, :prenom, :email, NOW())');
                        $insP->execute([':nom' => 'System', ':prenom' => 'Daemon', ':email' => 'system@localhost']);
                        $idp = (int) $this->pdo->lastInsertId();
                        $hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                        $insU = $this->pdo->prepare('INSERT INTO utilisateur (id_personne, identifiant, mot_de_passe_hash, statut_compte, doit_changer_mdp, nombre_essais_echoues, date_creation) VALUES (:idp, :ident, :hash, :statut, :doit, 0, NOW())');
                        $insU->execute([':idp' => $idp, ':ident' => 'system', ':hash' => $hash, ':statut' => 'actif', ':doit' => 0]);
                        $payload['id_utilisateur'] = (int) $this->pdo->lastInsertId();
                    }
                }

                $stmt = $this->pdo->prepare(
                    'INSERT INTO journal_audit (id_utilisateur, type_action, table_concernee, id_enregistrement_concerne, ancienne_valeur, nouvelle_valeur) VALUES (:id_utilisateur, :type_action, :table_concernee, :id_enregistrement_concerne, :ancienne_valeur, :nouvelle_valeur)'
                );
                return $stmt->execute($payload);
            } catch (Throwable $exception) {
                error_log('JournalAudit enregistrer failed: ' . $exception->getMessage());
                return false;
            }
        }

        $_SESSION['journal_audit'][] = array_merge(['date_action' => date('Y-m-d H:i:s')], $payload);
        return true;
    }

    public function lister(int $limit = 50): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT * FROM journal_audit ORDER BY date_action DESC LIMIT :limit');
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } catch (Throwable $exception) {
                error_log('JournalAudit lister failed: ' . $exception->getMessage());
            }
        }

        return array_slice(array_reverse($_SESSION['journal_audit'] ?? []), 0, $limit);
    }
}
