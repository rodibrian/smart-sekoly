<?php
require_once __DIR__ . '/ParametrageEtablissement.class.php';
require_once __DIR__ . '/TypeFraisDAO.class.php';
require_once __DIR__ . '/SequenceNumerotation.class.php';

/**
 * FactureDAO — Data Access Object pour gestion des factures réelles.
 * Génère les factures à partir des types de frais et persiste en base.
 */
class FactureDAO
{
    private $pdo;
    private $typeFraisDAO;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
        $this->typeFraisDAO = new TypeFraisDAO();
    }

    /**
     * Crée une facture avec lignes à partir des types de frais applicables.
     * 
     * @param int $id_eleve
     * @param array $id_types_frais : liste des IDs de types de frais à inclure
     * @param int $id_annee : année scolaire (pour numérotation)
     * @param string|null $numero_override : si fourni, utilise ce numéro (tests uniquement)
     * @return int|null ID de la facture créée, ou null si erreur
     */
    public function creerFacture(int $id_eleve, array $id_types_frais, int $id_annee, ?string $numero_override = null): ?int
    {
        if (!$this->pdo instanceof PDO) {
            error_log('FactureDAO::creerFacture() : DB non disponible.');
            return null;
        }

        if (empty($id_types_frais)) {
            error_log('FactureDAO::creerFacture() : Aucun type de frais fourni.');
            return null;
        }

        try {
            // Récupérer les montants des types de frais
            $montant_total = 0.0;
            $types_data = [];
            foreach ($id_types_frais as $id_type) {
                $type = $this->typeFraisDAO->trouverParId((int) $id_type);
                if (!$type) {
                    throw new RuntimeException("Type de frais $id_type non trouvé");
                }
                $types_data[$id_type] = (float) $type['montant_defaut'];
                $montant_total += $types_data[$id_type];
            }

            // Générer le numéro séquentiel (AVANT la transaction, pour éviter les nesting)
            if ($numero_override) {
                $numero = $numero_override;
            } else {
                $seq_result = SequenceNumerotation::getNext('facture', $id_annee);
                if (!$seq_result || empty($seq_result['formatte'])) {
                    throw new RuntimeException('Erreur génération numéro séquentiel');
                }
                $numero = $seq_result['formatte'];
            }

            // Commencer la transaction APRÈS avoir généré le numéro
            $this->pdo->beginTransaction();

            // Vérifier que l'élève existe
            $stmt_check = $this->pdo->prepare('SELECT id_eleve FROM eleve WHERE id_eleve = :id LIMIT 1');
            $stmt_check->execute([':id' => $id_eleve]);
            if (!$stmt_check->fetch()) {
                throw new RuntimeException("Élève $id_eleve n'existe pas");
            }

            // Insérer la facture
            $stmt = $this->pdo->prepare(
                'INSERT INTO facture (id_eleve, numero_sequentiel, date_emission, montant_total, statut) 
                 VALUES (:id_eleve, :numero, :date_emission, :montant_total, :statut)'
            );
            $stmt->execute([
                ':id_eleve' => $id_eleve,
                ':numero' => $numero,
                ':date_emission' => date('Y-m-d'),
                ':montant_total' => $montant_total,
                ':statut' => 'active',
            ]);

            $id_facture = (int) $this->pdo->lastInsertId();

            // Insérer les lignes de facture
            foreach ($id_types_frais as $id_type) {
                $stmt_ligne = $this->pdo->prepare(
                    'INSERT INTO ligne_facture (id_facture, id_type_frais, montant_ligne) 
                     VALUES (:id_facture, :id_type_frais, :montant_ligne)'
                );
                $stmt_ligne->execute([
                    ':id_facture' => $id_facture,
                    ':id_type_frais' => (int) $id_type,
                    ':montant_ligne' => $types_data[$id_type],
                ]);
            }

            $this->pdo->commit();
            return $id_facture;

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('FactureDAO::creerFacture() erreur complète : [' . get_class($e) . '] ' . $e->getMessage() . ' (ligne ' . $e->getLine() . ')');
            return null;
        }
    }

    /**
     * Récupère une facture avec ses lignes détaillées.
     * @return array|null Facture avec clé 'lignes' contenant les lignes
     */
    public function trouverParId(int $id_facture): ?array
    {
        if (!$this->pdo instanceof PDO) {
            return null;
        }

        try {
            // Récupérer la facture
            $stmt = $this->pdo->prepare('SELECT * FROM facture WHERE id_facture = :id');
            $stmt->execute([':id' => $id_facture]);
            $facture = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$facture) {
                return null;
            }

            // Récupérer les lignes
            $stmt_lignes = $this->pdo->prepare(
                'SELECT lf.*, tf.libelle FROM ligne_facture lf 
                 LEFT JOIN type_frais tf ON lf.id_type_frais = tf.id_type_frais
                 WHERE lf.id_facture = :id_facture'
            );
            $stmt_lignes->execute([':id_facture' => $id_facture]);
            $facture['lignes'] = $stmt_lignes->fetchAll(PDO::FETCH_ASSOC);

            return $facture;
        } catch (Throwable $e) {
            error_log('FactureDAO::trouverParId() erreur : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Liste les factures d'un élève.
     * @return array Liste des factures (sans lignes détaillées)
     */
    public function listerParEleve(int $id_eleve): array
    {
        if (!$this->pdo instanceof PDO) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM facture WHERE id_eleve = :id_eleve ORDER BY date_emission DESC'
            );
            $stmt->execute([':id_eleve' => $id_eleve]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('FactureDAO::listerParEleve() erreur : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Annule une facture (suppression logique).
     * @return bool
     */
    public function annuler(int $id_facture, int $id_utilisateur_annulation): bool
    {
        if (!$this->pdo instanceof PDO) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE facture SET statut = :statut, date_annulation = :date_annulation, 
                 id_utilisateur_annulation = :id_utilisateur 
                 WHERE id_facture = :id'
            );
            return $stmt->execute([
                ':statut' => 'annulee',
                ':date_annulation' => date('Y-m-d H:i:s'),
                ':id_utilisateur' => $id_utilisateur_annulation,
                ':id' => $id_facture,
            ]);
        } catch (Throwable $e) {
            error_log('FactureDAO::annuler() erreur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcule le total d'une facture à partir de ses lignes.
     * Utilisé pour vérification/validation.
     * @return float
     */
    public function calculerTotal(int $id_facture): float
    {
        if (!$this->pdo instanceof PDO) {
            return 0.0;
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT COALESCE(SUM(montant_ligne), 0) as total FROM ligne_facture WHERE id_facture = :id'
            );
            $stmt->execute([':id' => $id_facture]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float) ($result['total'] ?? 0);
        } catch (Throwable $e) {
            error_log('FactureDAO::calculerTotal() erreur : ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Récupère toutes les factures (pour rapports/admin).
     * @return array
     */
    public function listerTout(): array
    {
        if (!$this->pdo instanceof PDO) {
            return [];
        }

        try {
            $stmt = $this->pdo->query('SELECT * FROM facture ORDER BY date_emission DESC LIMIT 1000');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('FactureDAO::listerTout() erreur : ' . $e->getMessage());
            return [];
        }
    }
}
