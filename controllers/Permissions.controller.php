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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->action === 'modifier') {
            $this->traiterModification();
            header('Location: ' . BASE_URL . '/permissions/index');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->action === 'supprimer') {
            $this->traiterSuppression();
            header('Location: ' . BASE_URL . '/permissions/index');
            return;
        }

        $permissionAModifier = null;
        if ($this->action === 'modifier') {
            $permissionAModifier = $dao->trouverPermissionParId((int) ($this->parametre ?? 0));
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'permissions' => $dao->listerPermissions(),
            'permission' => $permissionAModifier,
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

    private function traiterModification(): void
    {
        $id = (int) ($this->parametre ?? 0);
        if ($id <= 0 || empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            $_SESSION['messages']['permissions'] = 'Données de modification invalides.';
            return;
        }

        $module = nettoyer_chaine($_POST['module'] ?? '');
        $sous_module = nettoyer_chaine($_POST['sous_module'] ?? '');
        $action = nettoyer_chaine($_POST['action'] ?? '');

        if ($module === '' || $action === '') {
            $_SESSION['messages']['permissions'] = 'Module et action sont requis pour la modification.';
            return;
        }

        $dao = new PermissionDAO();
        if ($dao->modifierPermission($id, $module, $sous_module, $action)) {
            $_SESSION['messages']['permissions'] = 'Permission mise à jour avec succès.';
        } else {
            $_SESSION['messages']['permissions'] = 'Impossible de mettre à jour la permission.';
        }
    }

    private function traiterSuppression(): void
    {
        $id = (int) ($this->parametre ?? 0);
        if ($id <= 0 || empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            $_SESSION['messages']['permissions'] = 'Suppression invalide.';
            return;
        }

        $dao = new PermissionDAO();
        if ($dao->supprimerPermission($id)) {
            $_SESSION['messages']['permissions'] = 'Permission supprimée avec succès.';
        } else {
            $_SESSION['messages']['permissions'] = 'Impossible de supprimer la permission.';
        }
    }
}
