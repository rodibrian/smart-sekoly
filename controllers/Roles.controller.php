<?php

class RolesController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'roles', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $dao = new RoleDAO();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->action === 'ajouter') {
            $this->traiter_ajout();
            header('Location: ' . BASE_URL . '/roles/index');
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'roles' => $dao->listerRoles(),
            'message' => $_SESSION['messages']['roles'] ?? null,
        ];

        unset($_SESSION['messages']['roles']);
        require TEMPLATES_PATH . 'roles/index.view.php';
    }

    private function traiter_ajout(): void
    {
        if (empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            $_SESSION['messages']['roles'] = 'Jeton CSRF invalide.';
            return;
        }

        $libelle = nettoyer_chaine($_POST['libelle'] ?? '');
        if ($libelle === '') {
            $_SESSION['messages']['roles'] = 'Le libellé du rôle est obligatoire.';
            return;
        }

        $dao = new RoleDAO();
        $dao->creerRole($libelle);
        $_SESSION['messages']['roles'] = 'Rôle ajouté avec succès.';
    }
}
