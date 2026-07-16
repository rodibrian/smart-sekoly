<?php
/**
 * Contrôleur de gestion des contrats.
 */
class ContratController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'contrats', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_donnees_fiche();
            require TEMPLATES_PATH . 'contrats/fiche.view.php';
            return;
        }

        $donnees = $this->preparer_donnees_liste();
        require TEMPLATES_PATH . 'contrats/liste.view.php';
    }

    private function preparer_donnees_liste(): array
    {
        $contrats = [
            new Contrat([
                'id_contrat' => 1,
                'id_enseignant' => 1,
                'type_contrat' => 'permanent',
                'date_debut' => '2025-09-01',
                'date_fin' => null,
                'salaire' => 550000,
                'statut' => 'actif',
            ]),
            new Contrat([
                'id_contrat' => 2,
                'id_enseignant' => 2,
                'type_contrat' => 'horaire',
                'date_debut' => '2025-09-15',
                'date_fin' => '2026-06-30',
                'salaire' => 320000,
                'statut' => 'termine',
            ]),
            new Contrat([
                'id_contrat' => 3,
                'id_enseignant' => 3,
                'type_contrat' => 'CDD',
                'date_debut' => '2026-01-05',
                'date_fin' => '2026-12-31',
                'salaire' => 420000,
                'statut' => 'actif',
            ]),
        ];

        $liste = array_map(function (Contrat $contrat): array {
            return [
                'id' => $contrat->get_id_contrat(),
                'type' => $contrat->get_type_contrat(),
                'debut' => $contrat->get_date_debut(),
                'fin' => $contrat->get_date_fin() ?: 'En cours',
                'salaire' => number_format($contrat->get_salaire(), 0, ',', ' '),
                'statut' => $contrat->get_statut(),
            ];
        }, $contrats);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'contrats' => $liste,
        ];
    }

    private function preparer_donnees_fiche(): array
    {
        $id = (int) ($this->parametre ?? 0);

        $contrat = new Contrat([
            'id_contrat' => $id,
            'id_enseignant' => 1,
            'type_contrat' => 'permanent',
            'date_debut' => '2025-09-01',
            'date_fin' => null,
            'salaire' => 550000,
            'statut' => 'actif',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'contrat' => [
                'id' => $contrat->get_id_contrat(),
                'enseignant' => 'Rakoto Jean',
                'type' => $contrat->get_type_contrat(),
                'date_debut' => $contrat->get_date_debut(),
                'date_fin' => $contrat->get_date_fin() ?: 'En cours',
                'salaire' => number_format($contrat->get_salaire(), 0, ',', ' '),
                'statut' => $contrat->get_statut(),
            ],
        ];
    }
}
