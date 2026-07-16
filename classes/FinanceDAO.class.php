<?php

class FinanceDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    private function generer_id_session(string $key, string $cle_id)
    {
        if (!isset($_SESSION[$key]) || !is_array($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }

        return generer_identifiant($_SESSION[$key], $cle_id);
    }

    public function insertFacture(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO factures (numero, id_eleve, date_emission, montant_total, statut) VALUES (:numero, :id_eleve, :date_emission, :montant_total, :statut)');
                $stmt->execute([
                    ':numero' => $data['numero'],
                    ':id_eleve' => $data['id_eleve'] ?? null,
                    ':date_emission' => $data['date_emission'],
                    ':montant_total' => $data['montant_total'] ?? 0.00,
                    ':statut' => $data['statut'] ?? 'brouillon',
                ]);

                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $e) {
                error_log('FinanceDAO insertFacture PDO failed, falling back to session: ' . $e->getMessage());
                // fall through to session fallback
            }
        }

        if (!isset($_SESSION['factures']) || !is_array($_SESSION['factures'])) {
            $_SESSION['factures'] = [];
        }

        $id = $this->generer_id_session('factures', 'id_facture');
        $_SESSION['factures'][] = array_merge(['id_facture' => $id], $data);

        return $id;
    }

    public function getFacture(int $id): ?array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('SELECT * FROM factures WHERE id_facture = :id');
                $stmt->execute([':id' => $id]);
                $row = $stmt->fetch();

                return $row ?: null;
            } catch (Throwable $e) {
                error_log('FinanceDAO getFacture PDO failed, falling back to session: ' . $e->getMessage());
                // fall through to session fallback
            }
        }

        if (!empty($_SESSION['factures']) && is_array($_SESSION['factures'])) {
            foreach ($_SESSION['factures'] as $f) {
                if ((int) ($f['id_facture'] ?? 0) === $id) {
                    return $f;
                }
            }
        }

        return null;
    }

    public function insertPaiement(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO paiements (id_echeance, numero_recu, date_paiement, montant, mode_paiement, statut) VALUES (:id_echeance, :numero_recu, :date_paiement, :montant, :mode_paiement, :statut)');
                $stmt->execute([
                    ':id_echeance' => $data['id_echeance'] ?? null,
                    ':numero_recu' => $data['numero_recu'],
                    ':date_paiement' => $data['date_paiement'],
                    ':montant' => $data['montant'],
                    ':mode_paiement' => $data['mode_paiement'] ?? 'espece',
                    ':statut' => $data['statut'] ?? 'actif',
                ]);

                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $e) {
                error_log('FinanceDAO insertPaiement PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['paiements']) || !is_array($_SESSION['paiements'])) {
            $_SESSION['paiements'] = [];
        }

        $id = $this->generer_id_session('paiements', 'id_paiement');
        $_SESSION['paiements'][] = array_merge(['id_paiement' => $id], $data);

        return $id;
    }

    public function insertCaisse(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO caisses (date_caisse, fond_de_caisse) VALUES (:date_caisse, :fond_de_caisse)');
                $stmt->execute([
                    ':date_caisse' => $data['date_caisse'],
                    ':fond_de_caisse' => $data['fond_de_caisse'],
                ]);

                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $e) {
                error_log('FinanceDAO insertCaisse PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['caisses']) || !is_array($_SESSION['caisses'])) {
            $_SESSION['caisses'] = [];
        }

        $id = $this->generer_id_session('caisses', 'id_caisse');
        $_SESSION['caisses'][] = array_merge(['id_caisse' => $id], $data);

        return $id;
    }

    public function insertRemise(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO remises (type_remise, valeur_remise, motif, id_utilisateur_validation) VALUES (:type_remise, :valeur_remise, :motif, :id_utilisateur_validation)');
                $stmt->execute([
                    ':type_remise' => $data['type_remise'],
                    ':valeur_remise' => $data['valeur_remise'] ?? 0,
                    ':motif' => $data['motif'] ?? '',
                    ':id_utilisateur_validation' => $data['id_utilisateur_validation'] ?? null,
                ]);

                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $e) {
                error_log('FinanceDAO insertRemise PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['remises']) || !is_array($_SESSION['remises'])) {
            $_SESSION['remises'] = [];
        }

        $id = $this->generer_id_session('remises', 'id_remise');
        $_SESSION['remises'][] = array_merge(['id_remise' => $id], $data);

        return $id;
    }

    public function insertEcheance(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO echeances (id_facture, date_echeance, montant_prevu, statut_echeance) VALUES (:id_facture, :date_echeance, :montant_prevu, :statut_echeance)');
                $stmt->execute([
                    ':id_facture' => $data['id_facture'] ?? null,
                    ':date_echeance' => $data['date_echeance'],
                    ':montant_prevu' => $data['montant_prevu'] ?? 0,
                    ':statut_echeance' => $data['statut_echeance'] ?? 'payee',
                ]);

                return (int) $this->pdo->lastInsertId();
            } catch (Throwable $e) {
                error_log('FinanceDAO insertEcheance PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['echeances']) || !is_array($_SESSION['echeances'])) {
            $_SESSION['echeances'] = [];
        }

        $id = $this->generer_id_session('echeances', 'id_echeance');
        $_SESSION['echeances'][] = array_merge(['id_echeance' => $id], $data);

        return $id;
    }

    public function all(string $table): array
    {
        if ($this->pdo instanceof PDO) {
            try {
                $allowed = ['factures', 'paiements', 'caisses', 'echeances', 'remises'];
                if (!in_array($table, $allowed, true)) {
                    return [];
                }

                $stmt = $this->pdo->query('SELECT * FROM ' . $table);
                return $stmt->fetchAll();
            } catch (Throwable $e) {
                error_log('FinanceDAO all() PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        return $_SESSION[$table] ?? [];
    }
}
