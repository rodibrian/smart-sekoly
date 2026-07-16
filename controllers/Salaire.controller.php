<?php
/**
 * Contrôleur de gestion des salaires des enseignants.
 */
class SalaireController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'salaires', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $templateBase = defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';

        if ($this->action === 'index') {
            $donnees = $this->preparer_liste();
            require $templateBase . 'salaires/liste.view.php';
            return;
        }

        if ($this->action === 'contrat') {
            $donnees = $this->preparer_calcul();
            require $templateBase . 'salaires/calcul.view.php';
            return;
        }

        $donnees = $this->preparer_fiche();
        require $templateBase . 'salaires/fiche.view.php';
    }

    private function preparer_liste(): array
    {
        $salaires = [
            [
                'id' => 1,
                'enseignant' => 'Rakoto Jean',
                'periode' => '2026-09',
                'brut' => 500000,
                'retenues' => 75000,
                'net' => 425000,
                'statut' => 'en_attente',
            ],
            [
                'id' => 2,
                'enseignant' => 'Randrianarisoa Fara',
                'periode' => '2026-09',
                'brut' => 420000,
                'retenues' => 63000,
                'net' => 357000,
                'statut' => 'valide',
            ],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'salaires' => $salaires,
        ];
    }

    private function preparer_calcul(): array
    {
        $contrats = [
            new Contrat([
                'id_contrat' => 1,
                'id_enseignant' => 1,
                'type_contrat' => 'permanent',
                'salaire' => 500000,
            ]),
            new Contrat([
                'id_contrat' => 2,
                'id_enseignant' => 2,
                'type_contrat' => 'horaire',
                'salaire' => 15000,
            ]),
            new Contrat([
                'id_contrat' => 3,
                'id_enseignant' => 3,
                'type_contrat' => 'CDD',
                'salaire' => 18000,
            ]),
        ];

        $salaires = array_map(function (Contrat $contrat): array {
            $salaire = Salaire::calculerPourContrat($contrat, ['heures' => 100, 'periode' => '2026-09']);

            return [
                'type_contrat' => $contrat->get_type_contrat(),
                'montant_brut' => $salaire->get_montant_brut(),
                'retenues' => $salaire->get_retenues(),
                'montant_net' => $salaire->get_montant_net(),
            ];
        }, $contrats);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'calculs' => $salaires,
        ];
    }

    private function preparer_fiche(): array
    {
        $salaire = new Salaire([
            'id_enseignant' => 1,
            'periode' => '2026-09',
            'montant_brut' => 500000.00,
            'retenues' => 75000.00,
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'salaire' => [
                'periode' => $salaire->get_periode(),
                'montant_brut' => $salaire->get_montant_brut(),
                'retenues' => $salaire->get_retenues(),
                'montant_net' => $salaire->get_montant_net(),
                'statut' => $salaire->get_statut(),
                'date_paiement' => $salaire->get_date_paiement(),
            ],
        ];
    }
}
