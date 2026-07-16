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
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'theme_actuel' => $_SESSION['theme_app'] ?? 'clair',
        ];

        require TEMPLATES_PATH . $vue;
    }

    public function traiter_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];

        $nom_etablissement = nettoyer_chaine($donnees_formulaire['nom_etablissement'] ?? '');
        $format_matricule = nettoyer_chaine($donnees_formulaire['format_matricule'] ?? '');
        $prefixe_matricule = nettoyer_chaine($donnees_formulaire['prefixe_matricule'] ?? '');
        $annee_courante = nettoyer_chaine($donnees_formulaire['annee_courante'] ?? '');

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

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'nom_etablissement' => $nom_etablissement,
                'format_matricule' => $format_matricule,
                'prefixe_matricule' => $prefixe_matricule,
                'annee_courante' => $annee_courante,
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
}
