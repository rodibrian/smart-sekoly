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

    public function get_nom_etablissement()
    {
        return $this->nom_etablissement;
    }

    public static function findById($id)
    {
        return null;
    }

    public function sauvegarder()
    {
        return true;
    }
}
