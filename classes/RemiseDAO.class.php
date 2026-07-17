<?php
/**
 * RemiseDAO — Data Access Object pour gestion des remises (discounts) réelles.
 * Remises avec validation obligatoire avant application.
 */
require_once __DIR__ . '/JournalAudit.class.php';

class RemiseDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    /**
     * Crée une remise à l'état "attente" (en attente de validation).
     * 
     * @param int $id_createur : utilisateur qui propose la remise
     * @param string $type : 'pourcentage' ou 'montant_fixe'
     * @param float $valeur : pourcentage (0-100) ou montant fixe
     * @param string $motif : raison de la remise (obligatoire)
     * @return int|null : ID remise créée ou null si erreur
     */
    public function creer(int $id_createur, string $type, float $valeur, string $motif): ?int
    {
        if (!$this->pdo instanceof PDO) {
            error_log('RemiseDAO::creer() : DB non disponible');
            return null;
        }

        if (!in_array($type, ['pourcentage', 'montant_fixe'])) {
            error_log('RemiseDAO::creer() : type invalide');
            return null;
        }

        if ($valeur <= 0) {
            error_log('RemiseDAO::creer() : valeur invalide');
            return null;
        }

        if (empty($motif)) {
            error_log('RemiseDAO::creer() : motif vide');
            return null;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO remise (type_remise, valeur_remise, motif, id_createur, statut) 
                 VALUES (:type, :valeur, :motif, :id_createur, :statut)'
            );
            $stmt->execute([
                ':type' => $type,
                ':valeur' => $valeur,
                ':motif' => $motif,
                ':id_createur' => $id_createur,
                ':statut' => 'attente',
            ]);

            $id_remise = (int) $this->pdo->lastInsertId();

            // Log création
            $audit = new JournalAudit();
            $audit->enregistrer([
                'id_utilisateur' => $id_createur,
                'type_action' => 'creation',
                'table_concernee' => 'remise',
                'id_enregistrement_concerne' => $id_remise,
                'nouvelle_valeur' => ['type' => $type, 'valeur' => $valeur, 'motif' => $motif],
            ]);

            return $id_remise;
        } catch (Throwable $e) {
            error_log('RemiseDAO::creer() : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère une remise par ID.
     */
    public function trouverParId(int $id_remise): ?array
    {
        if (!$this->pdo instanceof PDO) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM remise WHERE id_remise = :id');
            $stmt->execute([':id' => $id_remise]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Throwable $e) {
            error_log('RemiseDAO::trouverParId() : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Liste les remises en attente de validation.
     */
    public function listerEnAttenteValidation(): array
    {
        if (!$this->pdo instanceof PDO) {
            return [];
        }

        try {
            $stmt = $this->pdo->query(
                'SELECT r.*, p.nom, p.prenom FROM remise r 
                 JOIN utilisateur u ON r.id_createur = u.id_utilisateur 
                 JOIN personne p ON u.id_personne = p.id_personne
                 WHERE r.statut = "attente" 
                 ORDER BY r.date_creation DESC LIMIT 100'
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('RemiseDAO::listerEnAttenteValidation() : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Valide une remise (change statut à 'approuvee').
     * IMPORTANT : validation obligatoire AVANT application à une facture.
     * 
     * @param int $id_remise
     * @param int $id_validateur : utilisateur autorisé (rôle responsable_finance)
     * @return bool
     */
    public function valider(int $id_remise, int $id_validateur): bool
    {
        if (!$this->pdo instanceof PDO) {
            error_log('RemiseDAO::valider() : DB non disponible');
            return false;
        }

        try {
            // Vérifier que la remise est en attente
            $remise = $this->trouverParId($id_remise);
            if (!$remise || $remise['statut'] !== 'attente') {
                error_log('RemiseDAO::valider() : remise non en attente (statut=' . ($remise['statut'] ?? 'unknown') . ')');
                return false;
            }

            // Mettre à jour le statut
            $stmt = $this->pdo->prepare(
                'UPDATE remise SET statut = :statut, id_utilisateur_validation = :id_validateur, date_validation = NOW() 
                 WHERE id_remise = :id'
            );
            $success = $stmt->execute([
                ':statut' => 'approuvee',
                ':id_validateur' => $id_validateur,
                ':id' => $id_remise,
            ]);

            if ($success) {
                // Log validation
                $audit = new JournalAudit();
                $audit->enregistrer([
                    'id_utilisateur' => $id_validateur,
                    'type_action' => 'validation',
                    'table_concernee' => 'remise',
                    'id_enregistrement_concerne' => $id_remise,
                    'ancienne_valeur' => ['statut' => 'attente'],
                    'nouvelle_valeur' => ['statut' => 'approuvee'],
                ]);
                return true;
            }

            return false;
        } catch (Throwable $e) {
            error_log('RemiseDAO::valider() : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejette une remise (change statut à 'rejetee').
     */
    public function rejeter(int $id_remise, int $id_rejecteur): bool
    {
        if (!$this->pdo instanceof PDO) {
            return false;
        }

        try {
            $remise = $this->trouverParId($id_remise);
            if (!$remise || $remise['statut'] !== 'attente') {
                return false;
            }

            $stmt = $this->pdo->prepare(
                'UPDATE remise SET statut = :statut, id_utilisateur_validation = :id_rejecteur, date_validation = NOW() 
                 WHERE id_remise = :id'
            );
            $success = $stmt->execute([
                ':statut' => 'rejetee',
                ':id_rejecteur' => $id_rejecteur,
                ':id' => $id_remise,
            ]);

            if ($success) {
                $audit = new JournalAudit();
                $audit->enregistrer([
                    'id_utilisateur' => $id_rejecteur,
                    'type_action' => 'rejet',
                    'table_concernee' => 'remise',
                    'id_enregistrement_concerne' => $id_remise,
                    'ancienne_valeur' => ['statut' => 'attente'],
                    'nouvelle_valeur' => ['statut' => 'rejetee'],
                ]);
            }

            return $success;
        } catch (Throwable $e) {
            error_log('RemiseDAO::rejeter() : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Applique une remise à une facture.
     * BLOCAGE : ne peut s'appliquer que si statut='approuvee'.
     * 
     * @param int $id_remise
     * @param int $id_facture
     * @return bool
     */
    public function appliquerAFacture(int $id_remise, int $id_facture): bool
    {
        if (!$this->pdo instanceof PDO) {
            error_log('RemiseDAO::appliquerAFacture() : DB non disponible');
            return false;
        }

        // BLOCAGE CLÉS : vérifier statut='approuvee'
        $remise = $this->trouverParId($id_remise);
        if (!$remise || $remise['statut'] !== 'approuvee') {
            error_log('RemiseDAO::appliquerAFacture() : remise non approuvée (statut=' . ($remise['statut'] ?? 'unknown') . ')');
            return false;
        }

        try {
            // Vérifier que la facture existe
            $stmt_check = $this->pdo->prepare('SELECT id_facture FROM facture WHERE id_facture = :id LIMIT 1');
            $stmt_check->execute([':id' => $id_facture]);
            if (!$stmt_check->fetch()) {
                error_log('RemiseDAO::appliquerAFacture() : facture non trouvée');
                return false;
            }

            // Appliquer la remise (insérer dans facture_remise)
            $stmt = $this->pdo->prepare(
                'INSERT INTO facture_remise (id_facture, id_remise) VALUES (:id_facture, :id_remise)'
            );
            $success = $stmt->execute([
                ':id_facture' => $id_facture,
                ':id_remise' => $id_remise,
            ]);

            if ($success) {
                $audit = new JournalAudit();
                $audit->enregistrer([
                    'id_utilisateur' => $remise['id_utilisateur_validation'] ?? $remise['id_createur'] ?? 0,
                    'type_action' => 'application_facture',
                    'table_concernee' => 'remise',
                    'id_enregistrement_concerne' => $id_remise,
                    'nouvelle_valeur' => ['id_facture' => $id_facture],
                ]);
            }

            return $success;
        } catch (Throwable $e) {
            error_log('RemiseDAO::appliquerAFacture() : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calcule le montant après remise.
     */
    public function calculerMontantApres(float $montant_original, string $type, float $valeur): float
    {
        if ($type === 'pourcentage') {
            return max(0, $montant_original * (1 - ($valeur / 100)));
        } elseif ($type === 'montant_fixe') {
            return max(0, $montant_original - $valeur);
        }
        return $montant_original;
    }

    /**
     * Liste les remises appliquées à une facture.
     */
    public function listerParFacture(int $id_facture): array
    {
        if (!$this->pdo instanceof PDO) {
            return [];
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT r.* FROM remise r 
                 JOIN facture_remise fr ON r.id_remise = fr.id_remise 
                 WHERE fr.id_facture = :id_facture'
            );
            $stmt->execute([':id_facture' => $id_facture]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('RemiseDAO::listerParFacture() : ' . $e->getMessage());
            return [];
        }
    }
}
