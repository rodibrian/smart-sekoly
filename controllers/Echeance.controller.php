<?php
/**
 * Contrôleur des échéances.
 */
class EcheanceController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'echeances', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'echeances/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'echeances/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'echeances/liste.view.php';
    }

    private function preparer_formulaire(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'statuts' => ['payee' => 'Payée', 'partielle' => 'Partielle', 'en_retard' => 'En retard'],
        ];
    }

    private function preparer_liste(): array
    {
        $echeances = [
            new Echeance([
                'id_echeance' => 1,
                'id_facture' => 1,
                'date_echeance' => '2026-10-01',
                'montant_prevu' => 70000.00,
                'statut_echeance' => 'payee',
            ]),
            new Echeance([
                'id_echeance' => 2,
                'id_facture' => 1,
                'date_echeance' => '2026-11-01',
                'montant_prevu' => 70000.00,
                'statut_echeance' => 'en_retard',
            ]),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'echeances' => array_map(function (Echeance $echeance): array {
                return [
                    'id' => $echeance->get_id_echeance(),
                    'facture' => $echeance->get_id_facture(),
                    'date' => $echeance->get_date_echeance(),
                    'montant' => number_format($echeance->get_montant_prevu(), 0, ',', ' '),
                    'statut' => $echeance->get_statut_echeance(),
                ];
            }, $echeances),
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $echeance = new Echeance([
            'id_echeance' => $id,
            'id_facture' => 1,
            'date_echeance' => '2026-10-01',
            'montant_prevu' => 70000.00,
            'statut_echeance' => 'payee',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'echeance' => [
                'id' => $echeance->get_id_echeance(),
                'facture' => $echeance->get_id_facture(),
                'date' => $echeance->get_date_echeance(),
                'montant' => number_format($echeance->get_montant_prevu(), 0, ',', ' '),
                'statut' => $echeance->get_statut_echeance(),
            ],
        ];
    }
}
