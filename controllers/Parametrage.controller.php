<?php
/**
 * Contrôleur de paramétrage de l'établissement.
 *
 * @package Smart-Sekoly
 * @subpackage Controllers
 */
class ParametrageController
{
    private $module;
    private $action;

    public function __construct($module = 'parametrage', $action = 'assistant')
    {
        $this->module = $module;
        $this->action = $action;
    }

    public function executer(): void
    {
        $vue = 'parametrage/assistant.view.php';

        if ($this->action === 'themes') {
            $vue = 'parametrage/themes.view.php';
        } elseif ($this->action === 'courant') {
            $vue = 'parametrage/courant.view.php';
        } elseif ($this->action === 'sauvegardes') {
            $vue = 'parametrage/sauvegardes.view.php';
        }

        // Load current parametrage from session or defaults
        $paramSession = $_SESSION['parametrage'] ?? [];
        $defaultsObj = new ParametrageEtablissement();

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'theme_actuel' => $_SESSION['theme_app'] ?? 'clair',
            'parametrage' => [
                'nom_etablissement' => $paramSession['nom_etablissement'] ?? $defaultsObj->get_nom_etablissement(),
                'monnaie' => $paramSession['monnaie'] ?? $defaultsObj->monnaie ?? 'MGA',
                'langue_par_defaut' => $paramSession['langue_par_defaut'] ?? $defaultsObj->langue_par_defaut ?? 'fr',
                'theme_par_defaut' => $paramSession['theme_par_defaut'] ?? $defaultsObj->theme_par_defaut ?? 'clair',
                'format_matricule' => $paramSession['format_matricule'] ?? $defaultsObj->get_format_matricule(),
                'prefixe_matricule' => $paramSession['prefixe_matricule'] ?? $defaultsObj->get_prefixe_matricule(),
                'auto_download_escpos' => isset($paramSession['auto_download_escpos']) ? (bool) $paramSession['auto_download_escpos'] : DEFAULT_AUTO_DOWNLOAD_ESC_POS,
            ],
        ];

        require TEMPLATES_PATH . $vue;
    }

    public function traiter_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];

        $nom_etablissement = nettoyer_chaine($donnees_formulaire['nom_etablissement'] ?? '');
        $monnaie = nettoyer_chaine($donnees_formulaire['monnaie'] ?? '');
        $langue_par_defaut = nettoyer_chaine($donnees_formulaire['langue_par_defaut'] ?? '');
        $theme_par_defaut = nettoyer_chaine($donnees_formulaire['theme_par_defaut'] ?? '');
        $format_matricule = nettoyer_chaine($donnees_formulaire['format_matricule'] ?? '');
        $prefixe_matricule = nettoyer_chaine($donnees_formulaire['prefixe_matricule'] ?? '');
        $annee_courante = nettoyer_chaine($donnees_formulaire['annee_courante'] ?? '');
        $auto_download = !empty($donnees_formulaire['auto_download_escpos']) ? '1' : '0';

        if ($nom_etablissement === '') {
            $erreurs['nom_etablissement'] = 'Le nom de l’établissement est obligatoire.';
        }

        if ($format_matricule === '') {
            $erreurs['format_matricule'] = 'Le format du matricule est obligatoire.';
        }

        if ($prefixe_matricule === '') {
            $erreurs['prefixe_matricule'] = 'Le préfixe du matricule est obligatoire.';
        }

        if ($annee_courante === '' || !is_numeric($annee_courante)) {
            $erreurs['annee_courante'] = 'L’année scolaire doit être numérique.';
        }

        if ($monnaie === '') {
            $erreurs['monnaie'] = 'La monnaie est obligatoire.';
        }

        if (!in_array($theme_par_defaut, ['clair', 'sombre'], true)) {
            $erreurs['theme_par_defaut'] = 'Le thème sélectionné est invalide.';
        }

        if (empty($erreurs)) {
            $_SESSION['theme_app'] = $theme_par_defaut;
            // persist parametrage in session for now
            $_SESSION['parametrage'] = [
                'nom_etablissement' => $nom_etablissement,
                'monnaie' => $monnaie,
                'langue_par_defaut' => $langue_par_defaut,
                'theme_par_defaut' => $theme_par_defaut,
                'format_matricule' => $format_matricule,
                'prefixe_matricule' => $prefixe_matricule,
                'annee_courante' => $annee_courante,
                'auto_download_escpos' => $auto_download,
            ];
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'nom_etablissement' => $nom_etablissement,
                'format_matricule' => $format_matricule,
                'prefixe_matricule' => $prefixe_matricule,
                'annee_courante' => $annee_courante,
                'monnaie' => $monnaie,
                'langue_par_defaut' => $langue_par_defaut,
                'theme_par_defaut' => $theme_par_defaut,
                'auto_download_escpos' => $auto_download,
            ],
        ];
    }

    public function traiter_theme_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];
        $theme = nettoyer_chaine($donnees_formulaire['theme'] ?? '');

        if (!in_array($theme, ['clair', 'sombre'], true)) {
            $erreurs['theme'] = 'Le thème sélectionné est invalide.';
        }

        if (empty($donnees_formulaire['csrf_token'] ?? '')) {
            $erreurs['csrf_token'] = 'Le jeton CSRF est absent.';
        } elseif (!verifier_token_csrf((string) $donnees_formulaire['csrf_token'])) {
            $erreurs['csrf_token'] = 'Le jeton CSRF est invalide.';
        }

        if (empty($erreurs)) {
            $_SESSION['theme_app'] = $theme;
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'theme' => $theme,
            ],
        ];
    }

    public function traiter_sauvegarde_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];
        $frequence = nettoyer_chaine($donnees_formulaire['frequence'] ?? '');
        $repertoire = nettoyer_chaine($donnees_formulaire['repertoire'] ?? '');
        $retention = nettoyer_chaine($donnees_formulaire['retention'] ?? '');
        $activer = !empty($donnees_formulaire['activer']) ? '1' : '0';

        if (!in_array($frequence, ['quotidienne', 'hebdomadaire', 'mensuelle'], true)) {
            $erreurs['frequence'] = 'La fréquence de sauvegarde est invalide.';
        }

        if ($repertoire === '') {
            $erreurs['repertoire'] = 'Le répertoire de sauvegarde est obligatoire.';
        }

        if ($retention === '' || !is_numeric($retention)) {
            $erreurs['retention'] = 'La rétention doit être numérique.';
        }

        if (empty($erreurs)) {
            $_SESSION['sauvegarde_config'] = [
                'frequence' => $frequence,
                'repertoire' => $repertoire,
                'retention' => $retention,
                'activer' => $activer,
            ];
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'frequence' => $frequence,
                'repertoire' => $repertoire,
                'retention' => $retention,
                'activer' => $activer,
            ],
        ];
    }
}
