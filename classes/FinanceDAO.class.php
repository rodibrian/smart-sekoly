<?php

class FinanceDAO
{
    private $pdo;
    private $tableExistenceCache = [];

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

    private function getTableName(string $alias): ?string
    {
        $map = [
            'factures' => 'facture',
            'facture' => 'facture',
            'paiements' => 'paiement',
            'paiement' => 'paiement',
            'caisses' => 'caisse',
            'caisse' => 'caisse',
            'echeances' => 'echeance',
            'echeance' => 'echeance',
            'remises' => 'remise',
            'remise' => 'remise',
        ];

        return $map[$alias] ?? null;
    }

    private function getSessionKey(string $alias): string
    {
        $map = [
            'factures' => 'factures',
            'facture' => 'factures',
            'paiements' => 'paiements',
            'paiement' => 'paiements',
            'caisses' => 'caisses',
            'caisse' => 'caisses',
            'echeances' => 'echeances',
            'echeance' => 'echeances',
            'remises' => 'remises',
            'remise' => 'remises',
        ];

        return $map[$alias] ?? $alias;
    }

    private function tableExists(string $table): bool
    {
        if (!$this->pdo instanceof PDO) {
            return false;
        }

        if (array_key_exists($table, $this->tableExistenceCache)) {
            return $this->tableExistenceCache[$table];
        }

        try {
            $stmt = $this->pdo->query('SHOW TABLES LIKE ' . $this->pdo->quote($table));
            $exists = (bool) $stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log('FinanceDAO::tableExists() failed for ' . $table . ' : ' . $e->getMessage());
            $exists = false;
        }

        $this->tableExistenceCache[$table] = $exists;
        return $exists;
    }

    private function resolveTableName(string $alias): ?string
    {
        $table = $this->getTableName($alias);
        if ($table === null) {
            return null;
        }

        if (!$this->pdo instanceof PDO) {
            return $table;
        }

        if ($this->tableExists($table)) {
            return $table;
        }

        $plural = $table . 's';
        if ($this->tableExists($plural)) {
            return $plural;
        }

        return $table;
    }

    public function getDerniereCaisseId(): ?int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('caisse');
                if ($table) {
                    $stmt = $this->pdo->query('SELECT id_caisse FROM ' . $table . ' ORDER BY date_caisse DESC LIMIT 1');
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row !== false && !empty($row['id_caisse'])) {
                        return (int) $row['id_caisse'];
                    }
                }
            } catch (Throwable $e) {
                error_log('FinanceDAO::getDerniereCaisseId() : ' . $e->getMessage());
            }
        }

        if (!empty($_SESSION['caisses']) && is_array($_SESSION['caisses'])) {
            $last = end($_SESSION['caisses']);
            if (!empty($last['id_caisse'])) {
                return (int) $last['id_caisse'];
            }
        }

        return null;
    }

    public function getOrCreateCaisseDuJourId(): ?int
    {
        $dateDuJour = date('Y-m-d');

        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('caisse');
                if ($table) {
                    $stmt = $this->pdo->prepare('SELECT id_caisse FROM ' . $table . ' WHERE date_caisse = :date LIMIT 1');
                    $stmt->execute([':date' => $dateDuJour]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row !== false && !empty($row['id_caisse'])) {
                        return (int) $row['id_caisse'];
                    }

                    $stmtInsert = $this->pdo->prepare('INSERT INTO ' . $table . ' (date_caisse, fond_de_caisse) VALUES (:date_caisse, :fond_de_caisse)');
                    $stmtInsert->execute([':date_caisse' => $dateDuJour, ':fond_de_caisse' => 0]);
                    return (int) $this->pdo->lastInsertId();
                }
            } catch (Throwable $e) {
                error_log('FinanceDAO::getOrCreateCaisseDuJourId() : ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['caisses']) || !is_array($_SESSION['caisses'])) {
            $_SESSION['caisses'] = [];
        }

        foreach ($_SESSION['caisses'] as $caisse) {
            if (($caisse['date_caisse'] ?? '') === $dateDuJour && !empty($caisse['id_caisse'])) {
                return (int) $caisse['id_caisse'];
            }
        }

        $id = $this->generer_id_session('caisses', 'id_caisse');
        $caisse = ['id_caisse' => $id, 'date_caisse' => $dateDuJour, 'fond_de_caisse' => 0.0];
        $_SESSION['caisses'][] = $caisse;
        return $id;
    }

    private function synchroniser_session(string $key, array $donnees): void
    {
        if (!isset($_SESSION[$key]) || !is_array($_SESSION[$key])) {
            $_SESSION[$key] = [];
        }

        $_SESSION[$key][] = $donnees;
    }

    public function insertFacture(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('facture');
                $stmt = $this->pdo->prepare('INSERT INTO ' . $table . ' (numero_sequentiel, id_eleve, date_emission, montant_total, statut) VALUES (:numero_sequentiel, :id_eleve, :date_emission, :montant_total, :statut)');
                $status = $data['statut'] ?? 'active';
                $allowedStatuts = ['active', 'annulee'];
                if (!in_array($status, $allowedStatuts, true)) {
                    $status = 'active';
                }

                $stmt->execute([
                    ':numero_sequentiel' => $data['numero'] ?? $data['numero_sequentiel'] ?? '',
                    ':id_eleve' => $data['id_eleve'] ?? null,
                    ':date_emission' => $data['date_emission'],
                    ':montant_total' => $data['montant_total'] ?? 0.00,
                    ':statut' => $status,
                ]);

                $id = (int) $this->pdo->lastInsertId();
                $this->synchroniser_session($this->getSessionKey('facture'), array_merge(['id_facture' => $id], $data));
                return $id;
            } catch (Throwable $e) {
                error_log('FinanceDAO insertFacture PDO failed, falling back to session: ' . $e->getMessage());
                // fall through to session fallback
            }
        }

        if (!isset($_SESSION['factures']) || !is_array($_SESSION['factures'])) {
            $_SESSION['factures'] = [];
        }

        $id = $this->generer_id_session('factures', 'id_facture');
        $dataWithId = array_merge(['id_facture' => $id], $data);
        $_SESSION['factures'][] = $dataWithId;

        return $id;
    }

    public function getFacture(int $id): ?array
    {
        return $this->getById('facture', $id);
    }

    public function getById(string $tableAlias, int $id): ?array
    {
        $idColumns = [
            'factures' => 'id_facture',
            'facture' => 'id_facture',
            'paiements' => 'id_paiement',
            'paiement' => 'id_paiement',
            'caisses' => 'id_caisse',
            'caisse' => 'id_caisse',
            'echeances' => 'id_echeance',
            'echeance' => 'id_echeance',
            'remises' => 'id_remise',
            'remise' => 'id_remise',
        ];

        if (!isset($idColumns[$tableAlias])) {
            return null;
        }

        $idColumn = $idColumns[$tableAlias];

        if ($this->pdo instanceof PDO) {
            try {
                $actualTable = $this->resolveTableName($tableAlias);
                if ($actualTable !== null) {
                    $stmt = $this->pdo->prepare('SELECT * FROM ' . $actualTable . ' WHERE ' . $idColumn . ' = :id LIMIT 1');
                    $stmt->execute([':id' => $id]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    return $row ?: null;
                }
            } catch (Throwable $e) {
                error_log('FinanceDAO getById PDO failed for ' . $tableAlias . ': ' . $e->getMessage());
            }
        }

        $sessionKey = $this->getSessionKey($tableAlias);
        if (!empty($_SESSION[$sessionKey]) && is_array($_SESSION[$sessionKey])) {
            foreach ($_SESSION[$sessionKey] as $item) {
                if ((int) ($item[$idColumn] ?? 0) === $id) {
                    return $item;
                }
            }
        }

        return null;
    }

    public function insertPaiement(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('paiement');

                $data['id_utilisateur_enregistrement'] = $data['id_utilisateur_enregistrement'] ?? 1;
                if (empty($data['id_caisse'])) {
                    $data['id_caisse'] = $this->getOrCreateCaisseDuJourId() ?? $this->getDerniereCaisseId();
                }

                if ($table === 'paiement') {
                    $stmt = $this->pdo->prepare(
                        'INSERT INTO paiement (id_echeance, numero_recu, date_paiement, montant, mode_paiement, id_utilisateur_enregistrement, id_caisse, statut) VALUES (:id_echeance, :numero_recu, :date_paiement, :montant, :mode_paiement, :id_utilisateur_enregistrement, :id_caisse, :statut)'
                    );
                    $stmt->execute([
                        ':id_echeance' => $data['id_echeance'] ?? null,
                        ':numero_recu' => $data['numero_recu'],
                        ':date_paiement' => $data['date_paiement'],
                        ':montant' => $data['montant'],
                        ':mode_paiement' => $data['mode_paiement'] ?? 'espece',
                        ':id_utilisateur_enregistrement' => $data['id_utilisateur_enregistrement'],
                        ':id_caisse' => $data['id_caisse'],
                        ':statut' => $data['statut'] ?? 'actif',
                    ]);
                } else {
                    $stmt = $this->pdo->prepare('INSERT INTO paiements (id_echeance, numero_recu, date_paiement, montant, mode_paiement, statut) VALUES (:id_echeance, :numero_recu, :date_paiement, :montant, :mode_paiement, :statut)');
                    $stmt->execute([
                        ':id_echeance' => $data['id_echeance'] ?? null,
                        ':numero_recu' => $data['numero_recu'],
                        ':date_paiement' => $data['date_paiement'],
                        ':montant' => $data['montant'],
                        ':mode_paiement' => $data['mode_paiement'] ?? 'espece',
                        ':statut' => $data['statut'] ?? 'actif',
                    ]);
                }

                $id = (int) $this->pdo->lastInsertId();
                $this->synchroniser_session($this->getSessionKey('paiement'), array_merge(['id_paiement' => $id], $data));
                return $id;
            } catch (Throwable $e) {
                error_log('FinanceDAO insertPaiement PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['paiements']) || !is_array($_SESSION['paiements'])) {
            $_SESSION['paiements'] = [];
        }

        $id = $this->generer_id_session('paiements', 'id_paiement');
        $dataWithId = array_merge(['id_paiement' => $id], $data);
        $_SESSION['paiements'][] = $dataWithId;

        return $id;
    }

    public function insertCaisse(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('caisse');
                $stmt = $this->pdo->prepare('INSERT INTO ' . $table . ' (date_caisse, fond_de_caisse) VALUES (:date_caisse, :fond_de_caisse)');
                $stmt->execute([
                    ':date_caisse' => $data['date_caisse'],
                    ':fond_de_caisse' => $data['fond_de_caisse'],
                ]);

                $id = (int) $this->pdo->lastInsertId();
                $this->synchroniser_session($this->getSessionKey('caisse'), array_merge(['id_caisse' => $id], $data));
                return $id;
            } catch (Throwable $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    try {
                        $table = $this->resolveTableName('caisse');
                        $stmt = $this->pdo->prepare('SELECT id_caisse FROM ' . $table . ' WHERE date_caisse = :date LIMIT 1');
                        $stmt->execute([':date' => $data['date_caisse']]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row !== false && !empty($row['id_caisse'])) {
                            return (int) $row['id_caisse'];
                        }
                    } catch (Throwable $inner) {
                        error_log('FinanceDAO insertCaisse duplicate key handler failed: ' . $inner->getMessage());
                    }
                }

                error_log('FinanceDAO insertCaisse PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['caisses']) || !is_array($_SESSION['caisses'])) {
            $_SESSION['caisses'] = [];
        }

        $id = $this->generer_id_session('caisses', 'id_caisse');
        $dataWithId = array_merge(['id_caisse' => $id], $data);
        $_SESSION['caisses'][] = $dataWithId;

        return $id;
    }

    public function insertRemise(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('remise');
                $stmt = $this->pdo->prepare('INSERT INTO ' . $table . ' (type_remise, valeur_remise, motif, id_utilisateur_validation) VALUES (:type_remise, :valeur_remise, :motif, :id_utilisateur_validation)');
                $stmt->execute([
                    ':type_remise' => $data['type_remise'],
                    ':valeur_remise' => $data['valeur_remise'] ?? 0,
                    ':motif' => $data['motif'] ?? '',
                    ':id_utilisateur_validation' => $data['id_utilisateur_validation'] ?? null,
                ]);

                $id = (int) $this->pdo->lastInsertId();
                $this->synchroniser_session($this->getSessionKey('remise'), array_merge(['id_remise' => $id], $data));
                return $id;
            } catch (Throwable $e) {
                error_log('FinanceDAO insertRemise PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['remises']) || !is_array($_SESSION['remises'])) {
            $_SESSION['remises'] = [];
        }

        $id = $this->generer_id_session('remises', 'id_remise');
        $dataWithId = array_merge(['id_remise' => $id], $data);
        $_SESSION['remises'][] = $dataWithId;

        return $id;
    }

    public function insertEcheance(array $data): int
    {
        if ($this->pdo instanceof PDO) {
            try {
                $table = $this->resolveTableName('echeance');
                $stmt = $this->pdo->prepare('INSERT INTO ' . $table . ' (id_facture, date_echeance, montant_prevu, statut_echeance) VALUES (:id_facture, :date_echeance, :montant_prevu, :statut_echeance)');
                $stmt->execute([
                    ':id_facture' => $data['id_facture'] ?? null,
                    ':date_echeance' => $data['date_echeance'],
                    ':montant_prevu' => $data['montant_prevu'] ?? 0,
                    ':statut_echeance' => $data['statut_echeance'] ?? 'payee',
                ]);

                $id = (int) $this->pdo->lastInsertId();
                $this->synchroniser_session($this->getSessionKey('echeance'), array_merge(['id_echeance' => $id], $data));
                return $id;
            } catch (Throwable $e) {
                error_log('FinanceDAO insertEcheance PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        if (!isset($_SESSION['echeances']) || !is_array($_SESSION['echeances'])) {
            $_SESSION['echeances'] = [];
        }

        $id = $this->generer_id_session('echeances', 'id_echeance');
        $dataWithId = array_merge(['id_echeance' => $id], $data);
        $_SESSION['echeances'][] = $dataWithId;

        return $id;
    }

    public function all(string $table): array
    {
        $allowed = ['factures', 'facture', 'paiements', 'paiement', 'caisses', 'caisse', 'echeances', 'echeance', 'remises', 'remise'];
        if (!in_array($table, $allowed, true)) {
            return [];
        }

        if ($this->pdo instanceof PDO) {
            try {
                $actualTable = $this->resolveTableName($table);
                if ($actualTable !== null) {
                    $stmt = $this->pdo->query('SELECT * FROM ' . $actualTable);
                    return $stmt->fetchAll();
                }
            } catch (Throwable $e) {
                error_log('FinanceDAO all() PDO failed, falling back to session: ' . $e->getMessage());
            }
        }

        $sessionKey = $this->getSessionKey($table);
        return $_SESSION[$sessionKey] ?? [];
    }
}
