<?php

class PermissionsController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'permissions', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $dao = new PermissionDAO();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->action === 'ajouter') {
            $this->traiterAjout();
            header('Location: ' . BASE_URL . '/permissions/index');
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'permissions' => $dao->listerPermissions(),
            'message' => $_SESSION['messages']['permissions'] ?? null,
        ];

        unset($_SESSION['messages']['permissions']);
        require TEMPLATES_PATH . 'permissions/index.view.php';
    }

    private function traiterAjout(): void
    {
        if (empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            $_SESSION['messages']['permissions'] = 'Jeton CSRF invalide.';
            return;
        }

        $module = nettoyer_chaine($_POST['module'] ?? '');
        $sous_module = nettoyer_chaine($_POST['sous_module'] ?? '');
        $action = nettoyer_chaine($_POST['action'] ?? '');

        if ($module === '' || $action === '') {
            $_SESSION['messages']['permissions'] = 'Module et action sont requis.';
            return;
        }

        $dao = new PermissionDAO();
        $dao->creerPermission($module, $sous_module, $action);
        $_SESSION['messages']['permissions'] = 'Permission ajoutée avec succès.';
    }
}
