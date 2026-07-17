<?php
/**
 * ParametrageEtablissement — configuration de l'établissement et format des matricules.
 *
 * @package Smart-Sekoly
 * @subpackage Classes
 */
class ParametrageEtablissement
{
    private $id_parametrage;
    private $nom_etablissement;
    private $logo;
    private $monnaie;
    private $langue_par_defaut;
    private $theme_par_defaut;
    private $chemin_stockage_documents;
    private $format_matricule;
    private $prefixe_matricule;
    private $annee_courante;
    private $date_creation;
    private $date_modification;

    public function __construct(array $donnees = [])
    {
        $this->id_parametrage = isset($donnees['id_parametrage']) ? (int) $donnees['id_parametrage'] : null;
        $this->nom_etablissement = $donnees['nom_etablissement'] ?? 'Smart-Sekoly';
        $this->logo = $donnees['logo'] ?? null;
        $this->monnaie = $donnees['monnaie'] ?? 'MGA';
        $this->langue_par_defaut = $donnees['langue_par_defaut'] ?? 'fr';
        $this->theme_par_defaut = $donnees['theme_par_defaut'] ?? 'clair';
        $this->chemin_stockage_documents = $donnees['chemin_stockage_documents'] ?? 'documents';
        $this->format_matricule = $donnees['format_matricule'] ?? '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}';
        $this->prefixe_matricule = $donnees['prefixe_matricule'] ?? 'EL';
        $this->annee_courante = $donnees['annee_courante'] ?? date('Y');
    }

    public function get_logo()
    {
        return $this->logo;
    }

    public function set_logo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    public function get_id_parametrage()
    {
        return $this->id_parametrage;
    }

    public function set_format_matricule($format_matricule)
    {
        $this->format_matricule = trim((string) $format_matricule);
        return $this;
    }

    public function set_prefixe_matricule($prefixe_matricule)
    {
        $this->prefixe_matricule = trim((string) $prefixe_matricule);
        return $this;
    }

    public function generer_matricule($prefixe = null, $numero_sequentiel = 1, $annee = null)
    {
        $prefixe_utilise = $prefixe !== null ? strtoupper((string) $prefixe) : strtoupper((string) $this->prefixe_matricule);
        $annee_utilisee = $annee !== null ? (string) $annee : (string) $this->annee_courante;
        $numero = (string) $numero_sequentiel;

        $format = $this->format_matricule;
        $format = str_replace('{PREFIXE}', $prefixe_utilise, $format);
        $format = str_replace('{ANNEE}', $annee_utilisee, $format);
        $format = str_replace('{NUMERO_SEQUENTIEL}', $numero, $format);

        return $format;
    }

    public function get_format_matricule()
    {
        return $this->format_matricule;
    }

    public function get_prefixe_matricule()
    {
        return $this->prefixe_matricule;
    }

    public function get_monnaie()
    {
        return $this->monnaie;
    }

    public function get_langue_par_defaut()
    {
        return $this->langue_par_defaut;
    }

    public function get_theme_par_defaut()
    {
        return $this->theme_par_defaut;
    }

    public function get_chemin_stockage_documents()
    {
        return $this->chemin_stockage_documents;
    }

    public function get_annee_courante()
    {
        return $this->annee_courante;
    }

    public function set_nom_etablissement($nom)
    {
        $this->nom_etablissement = (string) $nom;
        return $this;
    }

    public function set_monnaie($monnaie)
    {
        $this->monnaie = (string) $monnaie;
        return $this;
    }

    public function set_langue_par_defaut($langue)
    {
        $this->langue_par_defaut = (string) $langue;
        return $this;
    }

    public function set_theme_par_defaut($theme)
    {
        $this->theme_par_defaut = (string) $theme;
        return $this;
    }

    public function set_chemin_stockage_documents($chemin)
    {
        $this->chemin_stockage_documents = (string) $chemin;
        return $this;
    }

    public function set_annee_courante($annee)
    {
        $this->annee_courante = $annee;
        return $this;
    }

    public function updateFromArray(array $data)
    {
        if (isset($data['nom_etablissement'])) {
            $this->set_nom_etablissement($data['nom_etablissement']);
        }
        if (isset($data['monnaie'])) {
            $this->set_monnaie($data['monnaie']);
        }
        if (isset($data['logo'])) {
            $this->set_logo($data['logo']);
        }
        if (isset($data['langue_par_defaut'])) {
            $this->set_langue_par_defaut($data['langue_par_defaut']);
        }
        if (isset($data['theme_par_defaut'])) {
            $this->set_theme_par_defaut($data['theme_par_defaut']);
        }
        if (isset($data['chemin_stockage_documents'])) {
            $this->set_chemin_stockage_documents($data['chemin_stockage_documents']);
        }
        if (isset($data['format_matricule'])) {
            $this->set_format_matricule($data['format_matricule']);
        }
        if (isset($data['prefixe_matricule'])) {
            $this->set_prefixe_matricule($data['prefixe_matricule']);
        }
        if (isset($data['annee_courante'])) {
            $this->set_annee_courante($data['annee_courante']);
        }

        return $this;
    }

    public function get_nom_etablissement()
    {
        return $this->nom_etablissement;
    }

    public static function findById($id)
    {
        $pdo = get_connexion_base_donnees();
        if (!$pdo instanceof PDO) {
            return null;
        }

        $sql = 'SELECT * FROM parametrage_etablissement WHERE id_parametrage = :id LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new self([
            'id_parametrage' => $row['id_parametrage'] ?? null,
            'nom_etablissement' => $row['nom_etablissement'] ?? null,
            'logo' => $row['logo'] ?? null,
            'monnaie' => $row['monnaie'] ?? null,
            'langue_par_defaut' => $row['langue_par_defaut'] ?? null,
            'theme_par_defaut' => $row['theme_par_defaut'] ?? null,
            'chemin_stockage_documents' => $row['chemin_stockage_documents'] ?? null,
            'format_matricule' => $row['format_matricule'] ?? null,
            'prefixe_matricule' => $row['prefixe_matricule'] ?? null,
            'annee_courante' => $row['annee_courante'] ?? null,
            'date_creation' => $row['date_creation'] ?? null,
            'date_modification' => $row['date_modification'] ?? null,
        ]);
    }

    /**
     * Retourne le dernier enregistrement de parametrage (id le plus élevé).
     */
    public static function findCurrent()
    {
        $pdo = get_connexion_base_donnees();
        if (!$pdo instanceof PDO) {
            return null;
        }

        $sql = 'SELECT * FROM parametrage_etablissement ORDER BY id_parametrage DESC LIMIT 1';
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return new self([
            'id_parametrage' => $row['id_parametrage'] ?? null,
            'nom_etablissement' => $row['nom_etablissement'] ?? null,
            'logo' => $row['logo'] ?? null,
            'monnaie' => $row['monnaie'] ?? null,
            'langue_par_defaut' => $row['langue_par_defaut'] ?? null,
            'theme_par_defaut' => $row['theme_par_defaut'] ?? null,
            'chemin_stockage_documents' => $row['chemin_stockage_documents'] ?? null,
            'format_matricule' => $row['format_matricule'] ?? null,
            'prefixe_matricule' => $row['prefixe_matricule'] ?? null,
            'annee_courante' => $row['annee_courante'] ?? null,
            'date_creation' => $row['date_creation'] ?? null,
            'date_modification' => $row['date_modification'] ?? null,
        ]);
    }

    public function sauvegarder()
    {
        $pdo = get_connexion_base_donnees();
        if (!$pdo instanceof PDO) {
            return false;
        }

        // Try to detect existing record: if id is set and > 0, update; otherwise insert a new row.
        if (!empty($this->id_parametrage)) {
            $sql = 'UPDATE parametrage_etablissement SET nom_etablissement = :nom, logo = :logo, monnaie = :monnaie, langue_par_defaut = :langue, theme_par_defaut = :theme, chemin_stockage_documents = :chemin, format_matricule = :format, prefixe_matricule = :prefixe, annee_courante = :annee WHERE id_parametrage = :id';
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':nom' => $this->nom_etablissement,
                ':logo' => $this->logo,
                ':monnaie' => $this->monnaie,
                ':langue' => $this->langue_par_defaut,
                ':theme' => $this->theme_par_defaut,
                ':chemin' => $this->chemin_stockage_documents,
                ':format' => $this->format_matricule,
                ':prefixe' => $this->prefixe_matricule,
                ':annee' => $this->annee_courante,
                ':id' => (int) $this->id_parametrage,
            ]);
        }

        $sql = 'INSERT INTO parametrage_etablissement (nom_etablissement, logo, monnaie, langue_par_defaut, theme_par_defaut, chemin_stockage_documents, format_matricule, prefixe_matricule, annee_courante) VALUES (:nom, :logo, :monnaie, :langue, :theme, :chemin, :format, :prefixe, :annee)';
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':nom' => $this->nom_etablissement,
            ':logo' => $this->logo,
            ':monnaie' => $this->monnaie,
            ':langue' => $this->langue_par_defaut,
            ':theme' => $this->theme_par_defaut,
            ':chemin' => $this->chemin_stockage_documents,
            ':format' => $this->format_matricule,
            ':prefixe' => $this->prefixe_matricule,
            ':annee' => $this->annee_courante,
        ]);

        if ($ok) {
            $this->id_parametrage = (int) $pdo->lastInsertId();
        }

        return (bool) $ok;
    }

    /**
     * Génère un matricule avec padding par défaut pour le numéro séquentiel.
     */
    public function generer_matricule_padder($prefixe = null, $numero_sequentiel = 1, $annee = null, $padLength = 6)
    {
        $numero = str_pad((string) $numero_sequentiel, (int) $padLength, '0', STR_PAD_LEFT);
        return $this->generer_matricule($prefixe, $numero, $annee);
    }
}
