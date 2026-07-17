<?php
/**
 * TypeFraisDAO — Data Access Object pour gestion des types de frais paramétrables.
 * Persistance réelle en base (pas de session).
 */
class TypeFraisDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = function_exists('get_connexion_base_donnees') ? get_connexion_base_donnees() : null;
    }

    /**
     * Crée un nouveau type de frais.
     * @param array $donnees : ['libelle' => '...', 'montant_defaut' => 50000.00]
     * @return int|null ID du type créé, ou null si erreur DB
     */
    public function creer(array $donnees): ?int
    {
        if (!$this->pdo instanceof PDO) {
            error_log('TypeFraisDAO::creer() : DB non disponible.');
            return null;
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO type_frais (libelle, montant_defaut) VALUES (:libelle, :montant_defaut)'
            );
            $stmt->execute([
                ':libelle' => nettoyer_chaine($donnees['libelle'] ?? ''),
                ':montant_defaut' => max(0, (float) ($donnees['montant_defaut'] ?? 0)),
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (Throwable $e) {
            error_log('TypeFraisDAO::creer() erreur : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère un type de frais par ID.
     * @return array|null
     */
    public function trouverParId(int $id): ?array
    {
        if (!$this->pdo instanceof PDO) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM type_frais WHERE id_type_frais = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (Throwable $e) {
            error_log('TypeFraisDAO::trouverParId() erreur : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère tous les types de frais.
     * @return array Liste des types
     */
    public function lister(): array
    {
        if (!$this->pdo instanceof PDO) {
            return [];
        }

        try {
            $stmt = $this->pdo->query('SELECT * FROM type_frais ORDER BY libelle ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('TypeFraisDAO::lister() erreur : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cherche un type de frais par libellé.
     * @return array|null
     */
    public function trouverParLibelle(string $libelle): ?array
    {
        if (!$this->pdo instanceof PDO) {
            return null;
        }

        try {
            $stmt = $this->pdo->prepare('SELECT * FROM type_frais WHERE libelle = :libelle');
            $stmt->execute([':libelle' => nettoyer_chaine($libelle)]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (Throwable $e) {
            error_log('TypeFraisDAO::trouverParLibelle() erreur : ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Met à jour le montant par défaut d'un type de frais.
     * @return bool
     */
    public function mettreAJourMontantDefaut(int $id, float $montant_defaut): bool
    {
        if (!$this->pdo instanceof PDO) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE type_frais SET montant_defaut = :montant WHERE id_type_frais = :id'
            );
            $result = $stmt->execute([
                ':montant' => max(0, $montant_defaut),
                ':id' => $id,
            ]);

            return $result;
        } catch (Throwable $e) {
            error_log('TypeFraisDAO::mettreAJourMontantDefaut() erreur : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Suppression logique : marquer comme archivé (via UPDATE de libelle ou statut si colonne existe).
     * Pour l'instant, on ne supprime pas réellement (décision #26 = suppression logique).
     * @return bool
     */
    public function archiver(int $id): bool
    {
        // Placeholder : ne rien faire pour l'instant (suppression logique pas encore implémentée sur type_frais)
        error_log("TypeFraisDAO::archiver($id) : suppression logique pas encore implémentée.");
        return true;
    }
}
