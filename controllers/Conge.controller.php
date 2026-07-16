<?php
/**
 * Contrôleur de gestion des congés des enseignants.
 */
class CongeController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'conges', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $conge = new Conge([
            'id_enseignant' => 1,
            'type_conge' => 'maladie',
            'date_debut' => '2026-09-01',
            'date_fin' => '2026-09-10',
            'raison' => 'Repos médical',
            'statut' => 'demande',
        ]);

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'conge' => [
                'type_conge' => $conge->get_type_conge(),
                'date_debut' => $conge->get_date_debut(),
                'date_fin' => $conge->get_date_fin(),
                'statut' => $conge->get_statut(),
                'raison' => $conge->get_raison(),
            ],
        ];

        require TEMPLATES_PATH . 'conges/fiche.view.php';
    }
}
