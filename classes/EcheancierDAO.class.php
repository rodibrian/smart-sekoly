<?php
/**
 * EcheancierDAO — Data Access Object pour gestion des échéances.
 * Échéances avec statuts CALCULÉS (pas saisie manuelle).
 * Statuts (enum): payee, partielle, en_retard — recalculés automatiquement à partir des paiements.
 * NOTE: Utilise schéma existant (statut_echeance, date_echeance)
 */
require_once __DIR__ . '/JournalAudit.class.php';

class EcheancierDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    /**
     * Crée un échéancier pour une facture en répartissant le montant sur N échéances.
     * 
     * @param int $id_facture : facture à fractionner
     * @param array $echances : [['date' => 'YYYY-MM-DD', 'montant' => 50000], ...]
     * @param int $id_createur : utilisateur qui crée l'échéancier
     * @return bool
     */
    public function creer(int $id_facture, array $echances, int $id_createur): bool
    {
        if (!$this->pdo instanceof PDO || empty($echances)) {
            error_log('EcheancierDAO::creer() : PDO ou échéances vides');
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            // Vérifier que la facture existe
            $stmt_check = $this->pdo->prepare('SELECT id_facture FROM facture WHERE id_facture = :id LIMIT 1');
            $stmt_check->execute([':id' => $id_facture]);
            if (!$stmt_check->fetch()) {
                $this->pdo->rollBack();
                error_log('EcheancierDAO::creer() : facture non trouvée');
                return false;
            }

            // Supprimer les échéances existantes pour cette facture (remise à zéro)
            $stmt_del = $this->pdo->prepare('DELETE FROM echeance WHERE id_facture = :id_facture');
            $stmt_del->execute([':id_facture' => $id_facture]);

            // Insérer les nouvelles échéances
            $stmt_insert = $this->pdo->prepare(
                'INSERT INTO echeance (id_facture, numero_ordre, date_echeance, montant_prevu, statut_echeance, montant_paye) 
                 VALUES (:id_facture, :numero, :date, :montant, :statut, 0)'
            );

            $numero = 1;
            foreach ($echances as $ech) {
                $stmt_insert->execute([
                    ':id_facture' => $id_facture,
                    ':numero' => $numero,
                    ':date' => $ech['date'],
                    ':montant' => $ech['montant'],
                    ':statut' => 'a_venir',  // Default statut for future unpaid
                ]);
                $numero++;
            }

            $this->pdo->commit();

            // Log création
            $audit = new JournalAudit();
            $audit->enregistrer([
                'id_utilisateur' => $id_createur,
                'type_action' => 'creation',
                'table_concernee' => 'echeance',
                'id_enregistrement_concerne' => $id_facture,
                'nouvelle_valeur' => ['nb_echances' => count($echances)],
            ]);

            return true;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            error_log('EcheancierDAO::creer() : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les échéances d'une facture, ordonnées par numéro.
     */
    public function listerParFacture(int $id_facture): array
    {
        if (!$this->pdo instanceof PDO) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id_echeance, id_facture, numero_ordre, date_echeance, montant_prevu, montant_paye, statut_echeance as statut FROM echeance WHERE id_facture = :id_facture ORDER BY numero_ordre ASC'
            );
            $stmt->execute([':id_facture' => $id_facture]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('EcheancierDAO::listerParFacture() : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère une échéance par ID.
     */
    public function trouverParId(int $id_echeance): ?array
    {
        if (!$this->pdo instanceof PDO) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare('SELECT id_echeance, id_facture, numero_ordre, date_echeance, montant_prevu, montant_paye, statut_echeance as statut FROM echeance WHERE id_echeance = :id');
            $stmt->execute([':id' => $id_echeance]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Throwable $e) {
            error_log('EcheancierDAO::trouverParId() : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcule et met à jour le statut d'une échéance en fonction de son montant payé vs prévu.
     * Statuts (enum): a_venir, payee, partielle, en_retard
     * 
     * @param int $id_echeance
     * @return string : le statut calculé
     */
    public function recalculerStatut(int $id_echeance): string
    {
        if (!$this->pdo instanceof PDO) {
            return 'a_venir';
        }

        try {
            $echeance = $this->trouverParId($id_echeance);
            if (!$echeance) {
                return 'a_venir';
            }

            $montant_paye = (float) $echeance['montant_paye'];
            $montant_prevu = (float) $echeance['montant_prevu'];
            $date_echeance = $echeance['date_echeance'];
            $today = date('Y-m-d');

            // Logique de statut selon schéma (enum: a_venir, payee, partielle, en_retard)
            if ($montant_paye >= $montant_prevu) {
                $statut = 'payee';
            } elseif ($date_echeance < $today && $montant_paye < $montant_prevu) {
                $statut = 'en_retard';
            } elseif ($montant_paye > 0 && $montant_paye < $montant_prevu) {
                $statut = 'partielle';
            } else {
                $statut = 'a_venir';  // Non payée et date future
            }

            // Mettre à jour en base
            $stmt_update = $this->pdo->prepare(
                'UPDATE echeance SET statut_echeance = :statut WHERE id_echeance = :id'
            );
            $stmt_update->execute([
                ':statut' => $statut,
                ':id' => $id_echeance,
            ]);

            return $statut;
        } catch (Throwable $e) {
            error_log('EcheancierDAO::recalculerStatut() : ' . $e->getMessage());
            return 'a_venir';
        }
    }

    /**
     * Impute un paiement aux échéances d'une facture.
     * Stratégie : imputer d'abord à la plus ancienne non soldée (FIFO).
     * Si un paiement déborde une échéance, le surplus passe à la suivante.
     * 
     * @param int $id_facture
     * @param float $montant : montant du paiement
     * @return bool
     */
    public function impurerPaiement(int $id_facture, float $montant): bool
    {
        if (!$this->pdo instanceof PDO || $montant <= 0) {
            error_log('EcheancierDAO::impurerPaiement() : PDO ou montant invalide');
            return false;
        }

        try {
            $this->pdo->beginTransaction();
            $success = $this->impurerPaiementSansTransaction($id_facture, $montant);
            if (!$success) {
                $this->pdo->rollBack();
                return false;
            }
            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('EcheancierDAO::impurerPaiement() : ' . $e->getMessage());
            return false;
        }
    }

    public function appliquerPaiementAEcheance(int $id_echeance, float $montant): bool
    {
        if (!$this->pdo instanceof PDO || $id_echeance <= 0 || $montant <= 0) {
            error_log('EcheancierDAO::appliquerPaiementAEcheance() : PDO ou montant invalide');
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('SELECT id_facture, montant_prevu, montant_paye FROM echeance WHERE id_echeance = :id FOR UPDATE');
            $stmt->execute([':id' => $id_echeance]);
            $echeance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$echeance) {
                $this->pdo->rollBack();
                return false;
            }

            $montant_prevu = (float) $echeance['montant_prevu'];
            $montant_paye = (float) $echeance['montant_paye'];
            $id_facture = (int) $echeance['id_facture'];
            $restant_echeance = max(0, $montant_prevu - $montant_paye);
            $aImputer = min($montant, $restant_echeance);
            $reste = $montant - $aImputer;

            if ($aImputer > 0) {
                $stmt_update = $this->pdo->prepare('UPDATE echeance SET montant_paye = montant_paye + :impute WHERE id_echeance = :id');
                $stmt_update->execute([
                    ':impute' => $aImputer,
                    ':id' => $id_echeance,
                ]);
                $this->recalculerStatut($id_echeance);
            }

            if ($reste > 0) {
                if (!$this->impurerPaiementSansTransaction($id_facture, $reste)) {
                    $this->pdo->rollBack();
                    return false;
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('EcheancierDAO::appliquerPaiementAEcheance() : ' . $e->getMessage());
            return false;
        }
    }

    private function impurerPaiementSansTransaction(int $id_facture, float $montant): bool
    {
        if ($montant <= 0) {
            return true;
        }

        try {
            $stmt_echances = $this->pdo->prepare(
                'SELECT id_echeance, montant_prevu, montant_paye, numero_ordre
                 FROM echeance 
                 WHERE id_facture = :id_facture AND montant_paye < montant_prevu 
                 ORDER BY numero_ordre ASC'
            );
            $stmt_echances->execute([':id_facture' => $id_facture]);
            $echances = $stmt_echances->fetchAll(PDO::FETCH_ASSOC);

            $restant = $montant;

            foreach ($echances as $ech) {
                if ($restant <= 0) {
                    break;
                }

                $id_echeance = $ech['id_echeance'];
                $montant_prevu = (float) $ech['montant_prevu'];
                $montant_paye = (float) $ech['montant_paye'];
                $capacite = $montant_prevu - $montant_paye;
                $impute = min($capacite, $restant);

                if ($impute <= 0) {
                    continue;
                }

                $stmt_update = $this->pdo->prepare(
                    'UPDATE echeance SET montant_paye = montant_paye + :impute WHERE id_echeance = :id'
                );
                $stmt_update->execute([
                    ':impute' => $impute,
                    ':id' => $id_echeance,
                ]);

                $restant -= $impute;
                $this->recalculerStatut($id_echeance);
            }

            return true;
        } catch (Throwable $e) {
            error_log('EcheancierDAO::impurerPaiementSansTransaction() : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcule le montant total non payé pour toutes les échéances d'une facture.
     */
    public function calculerMontantNonPaye(int $id_facture): float
    {
        if (!$this->pdo instanceof PDO) {
            return 0;
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT SUM(montant_prevu - montant_paye) as solde FROM echeance WHERE id_facture = :id_facture'
            );
            $stmt->execute([':id_facture' => $id_facture]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return max(0, (float) ($result['solde'] ?? 0));
        } catch (Throwable $e) {
            error_log('EcheancierDAO::calculerMontantNonPaye() : ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calcule l'état global d'un échéancier (tous statuts pour une facture).
     */
    public function calculerEtatGlobal(int $id_facture): array
    {
        $echances = $this->listerParFacture($id_facture);
        
        $stats = [
            'total_echances' => count($echances),
            'a_venir' => 0,
            'payees' => 0,
            'partielles' => 0,
            'en_retard' => 0,
            'montant_total_prevu' => 0,
            'montant_total_paye' => 0,
        ];

        foreach ($echances as $ech) {
            $stats['montant_total_prevu'] += (float) $ech['montant_prevu'];
            $stats['montant_total_paye'] += (float) $ech['montant_paye'];
            
            // Utiliser 'statut' qui est aliasé depuis 'statut_echeance' dans les SELECT
            $statut = $ech['statut'] ?? 'a_venir';
            switch ($statut) {
                case 'a_venir':
                    $stats['a_venir']++;
                    break;
                case 'payee':
                    $stats['payees']++;
                    break;
                case 'partielle':
                    $stats['partielles']++;
                    break;
                case 'en_retard':
                    $stats['en_retard']++;
                    break;
            }
        }

        return $stats;
    }
}
