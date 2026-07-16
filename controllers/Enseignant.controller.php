<?php
/**
 * Contrôleur de gestion des enseignants.
 */
class EnseignantController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'enseignants', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $enseignant = new Enseignant([
            'nom' => 'Rakoto',
            'prenom' => 'Jean',
            'email' => 'jean@example.com',
            'matricule' => 'ENS-2026-001',
        ]);

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'enseignant' => [
                'nom' => $enseignant->get_nom(),
                'prenom' => $enseignant->get_prenom(),
                'email' => $enseignant->get_email(),
                'matricule' => $enseignant->get_matricule(),
            ],
        ];

        require TEMPLATES_PATH . 'enseignants/fiche.view.php';
    }
}
