<?php
/**
 * Contrôleur des remises.
 */
class RemiseController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'remises', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'remises/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'remises/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'remises/liste.view.php';
    }

    private function preparer_formulaire(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'types' => [
                'pourcentage' => 'Pourcentage',
                'montant_fixe' => 'Montant fixe',
            ],
        ];
    }

    private function preparer_liste(): array
    {
        $remises = [
            new Remise([
                'id_remise' => 1,
                'type_remise' => 'pourcentage',
                'valeur_remise' => 10.0,
                'motif' => 'Bourse sociale',
                'id_utilisateur_validation' => 1,
            ]),
            new Remise([
                'id_remise' => 2,
                'type_remise' => 'montant_fixe',
                'valeur_remise' => 15000.00,
                'motif' => 'Remise fidélité',
                'id_utilisateur_validation' => 2,
            ]),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'remises' => array_map(function (Remise $remise): array {
                return [
                    'id' => $remise->get_id_remise(),
                    'type' => $remise->get_type_remise(),
                    'valeur' => $remise->get_valeur_remise(),
                    'motif' => $remise->get_motif(),
                ];
            }, $remises),
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $remise = new Remise([
            'id_remise' => $id,
            'type_remise' => 'pourcentage',
            'valeur_remise' => 10.0,
            'motif' => 'Bourse sociale',
            'id_utilisateur_validation' => 1,
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'remise' => [
                'id' => $remise->get_id_remise(),
                'type' => $remise->get_type_remise(),
                'valeur' => $remise->get_valeur_remise(),
                'motif' => $remise->get_motif(),
            ],
        ];
    }
}
