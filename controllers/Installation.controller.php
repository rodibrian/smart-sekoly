<?php
/**
 * Contrôleur d'installation initiale de Smart-Sekoly.
 *
 * @package Smart-Sekoly
 * @subpackage Controllers
 */
class InstallationController
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $action;

    /**
     * @var mixed
     */
    private $parametre;

    /**
     * Constructeur.
     *
     * @param string $module
     * @param string $action
     * @param mixed $parametre
     */
    public function __construct($module = 'installation', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    /**
     * Exécute la logique de la page d'installation.
     */
    public function executer(): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'parametre' => $this->parametre,
            'base_url' => BASE_URL,
            'base_donnees_disponible' => est_base_donnees_disponible(),
            'token_csrf' => generer_token_csrf(),
        ];

        require TEMPLATES_PATH . 'installation.view.php';
    }
}
