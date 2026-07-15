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
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];

        require TEMPLATES_PATH . 'parametrage/assistant.view.php';
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
}
