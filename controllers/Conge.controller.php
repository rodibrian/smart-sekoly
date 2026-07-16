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
        if ($this->action === 'liste') {
            $donnees = $this->preparer_liste();
            require TEMPLATES_PATH . 'conges/liste.view.php';
            return;
        }

        if ($this->action === 'validation') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'conges/validation.view.php';
            return;
        }

        $donnees = $this->preparer_fiche();
        require TEMPLATES_PATH . 'conges/fiche.view.php';
    }

    private function preparer_liste(): array
    {
        $demandes = [
            [
                'id' => 1,
                'enseignant' => 'Rakoto Jean',
                'type_conge' => 'maladie',
                'date_debut' => '2026-09-01',
                'date_fin' => '2026-09-10',
                'statut' => 'demande',
            ],
            [
                'id' => 2,
                'enseignant' => 'Randrianarisoa Fara',
                'type_conge' => 'annuel',
                'date_debut' => '2026-07-05',
                'date_fin' => '2026-07-20',
                'statut' => 'valide',
            ],
            [
                'id' => 3,
                'enseignant' => 'Rajaonarivony Mira',
                'type_conge' => 'congé parental',
                'date_debut' => '2026-10-01',
                'date_fin' => '2026-10-15',
                'statut' => 'refuse',
            ],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'demandes' => $demandes,
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);

        $conge = new Conge([
            'id_enseignant' => 1,
            'type_conge' => 'maladie',
            'date_debut' => '2026-09-01',
            'date_fin' => '2026-09-10',
            'raison' => 'Repos médical',
            'statut' => 'demande',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'conge' => [
                'id' => $id,
                'enseignant' => 'Rakoto Jean',
                'type_conge' => $conge->get_type_conge(),
                'date_debut' => $conge->get_date_debut(),
                'date_fin' => $conge->get_date_fin(),
                'raison' => $conge->get_raison(),
                'statut' => $conge->get_statut(),
            ],
        ];
    }
}
