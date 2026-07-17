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

    /**
     * Handle the multi-step assistant (19 steps). Saves per-step data and logs changes.
     */
    private function handleAssistant(): void
    {
        $step = isset($_GET['step']) ? max(1, (int) $_GET['step'] ) : 1;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $posted = $_POST;
            $errors = [];

            // Basic server-side CSRF check
            if (empty($posted['csrf_token']) || !verifier_token_csrf((string)$posted['csrf_token'])) {
                $errors['csrf'] = 'Jeton CSRF invalide.';
            }

            if (empty($errors)) {
                $model = ParametrageEtablissement::findCurrent() ?? new ParametrageEtablissement();
                $before = [
                    'nom_etablissement' => $model->get_nom_etablissement(),
                    'format_matricule' => $model->get_format_matricule(),
                    'prefixe_matricule' => $model->get_prefixe_matricule(),
                    'monnaie' => $model->get_monnaie(),
                    'annee_courante' => $model->get_annee_courante(),
                ];

                // Map posted fields by step (simple strategy: accept any known keys)
                $saveData = [];
                $allowed = ['nom_etablissement','format_matricule','prefixe_matricule','monnaie','langue_par_defaut','theme_par_defaut','chemin_stockage_documents','annee_courante'];
                foreach ($allowed as $k) {
                    if (isset($posted[$k])) {
                        $saveData[$k] = nettoyer_chaine($posted[$k]);
                    }
                }

                $model->updateFromArray($saveData);
                $ok = $model->sauvegarder();

                // Log change
                $after = [
                    'nom_etablissement' => $model->get_nom_etablissement(),
                    'format_matricule' => $model->get_format_matricule(),
                    'prefixe_matricule' => $model->get_prefixe_matricule(),
                    'monnaie' => $model->get_monnaie(),
                    'annee_courante' => $model->get_annee_courante(),
                ];

                $journal = new JournalAudit();
                $journal->enregistrer([
                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                    'type_action' => 'parametrage:step:' . $step,
                    'table_concernee' => 'parametrage_etablissement',
                    'id_enregistrement_concerne' => $model->get_id_parametrage(),
                    'ancienne_valeur' => $before,
                    'nouvelle_valeur' => $after,
                ]);

                if ($ok) {
                    // redirect to next step
                    $next = $step < 19 ? $step + 1 : 19;
                    header('Location: ' . BASE_URL . '/parametrage/assistant?step=' . $next);
                    return;
                }
            }
        }

        // Render step view
        $vue = 'parametrage/assistant_step.view.php';
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'step' => $step,
            'token_csrf' => generer_token_csrf(),
            'parametrage' => ParametrageEtablissement::findCurrent(),
        ];
        require TEMPLATES_PATH . $vue;
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
        } elseif ($this->action === 'generer_matricule') {
            // API: génère un nouveau numéro de matricule pour l'année active ou id_annee fourni
            try {
                $id_annee = $_REQUEST['id_annee'] ?? null;
                $pdo = get_connexion_base_donnees();
                if ($id_annee === null) {
                    $stmt = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE etat = 'active' LIMIT 1");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row === false) {
                        throw new RuntimeException('Aucune année active trouvée.');
                    }
                    $id_annee = (int) $row['id_annee'];
                } else {
                    $id_annee = (int) $id_annee;
                }

                $res = SequenceNumerotation::getNext('matricule', $id_annee);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => true, 'numero' => $res['numero'], 'formatte' => $res['formatte']]);
            } catch (Throwable $e) {
                header('Content-Type: application/json; charset=utf-8', true, 500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }

            return;
        }

        // Load current parametrage from DB or defaults
        $current = ParametrageEtablissement::findCurrent();
        if ($current === null) {
            $current = new ParametrageEtablissement();
        }

        // If assistant wizard step handling
        if ($this->action === 'assistant') {
            $this->handleAssistant();
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'theme_actuel' => $_SESSION['theme_app'] ?? $current->get_theme_par_defaut() ?? 'clair',
            'parametrage' => [
                'nom_etablissement' => $current->get_nom_etablissement(),
                'monnaie' => $current->get_monnaie() ?? 'MGA',
                'langue_par_defaut' => $current->get_langue_par_defaut() ?? 'fr',
                'theme_par_defaut' => $current->get_theme_par_defaut() ?? 'clair',
                'format_matricule' => $current->get_format_matricule(),
                'prefixe_matricule' => $current->get_prefixe_matricule(),
                'auto_download_escpos' => defined('DEFAULT_AUTO_DOWNLOAD_ESC_POS') ? DEFAULT_AUTO_DOWNLOAD_ESC_POS : false,
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


            $model = ParametrageEtablissement::findCurrent();
            if ($model === null) {
                $model = new ParametrageEtablissement();
            }

            $model->updateFromArray([
                'nom_etablissement' => $nom_etablissement,
                'monnaie' => $monnaie,
                'langue_par_defaut' => $langue_par_defaut,
                'theme_par_defaut' => $theme_par_defaut,
                'format_matricule' => $format_matricule,
                'prefixe_matricule' => $prefixe_matricule,
                'annee_courante' => $annee_courante,
            ]);

            $saved = $model->sauvegarder();

            if (!$saved) {
                $erreurs['general'] = 'Impossible de sauvegarder la configuration en base de données.';
            }
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
