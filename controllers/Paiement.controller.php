<?php
/**
 * Contrôleur des paiements.
 */
class PaiementController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'paiements', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'paiements/fiche.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'paiements/liste.view.php';
    }

    private function preparer_liste(): array
    {
        $paiements = [
            new Paiement([
                'id_paiement' => 1,
                'id_echeance' => 1,
                'numero_recu' => 'REC-2026-001',
                'date_paiement' => '2026-10-01 09:00:00',
                'montant' => 50000.00,
                'mode_paiement' => 'espece',
                'statut' => 'actif',
            ]),
            new Paiement([
                'id_paiement' => 2,
                'id_echeance' => 2,
                'numero_recu' => 'REC-2026-002',
                'date_paiement' => '2026-10-02 11:15:00',
                'montant' => 75000.00,
                'mode_paiement' => 'mobile_money',
                'statut' => 'actif',
            ]),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'paiements' => array_map(function (Paiement $paiement): array {
                return [
                    'id' => $paiement->get_id_paiement(),
                    'recu' => $paiement->get_numero_recu(),
                    'date' => $paiement->get_date_paiement(),
                    'montant' => number_format($paiement->get_montant(), 0, ',', ' '),
                    'mode' => $paiement->get_mode_paiement(),
                    'statut' => $paiement->get_statut(),
                ];
            }, $paiements),
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $paiement = new Paiement([
            'id_paiement' => $id,
            'id_echeance' => 1,
            'numero_recu' => 'REC-2026-001',
            'date_paiement' => '2026-10-01 09:00:00',
            'montant' => 50000.00,
            'mode_paiement' => 'espece',
            'statut' => 'actif',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'paiement' => [
                'id' => $paiement->get_id_paiement(),
                'recu' => $paiement->get_numero_recu(),
                'date' => $paiement->get_date_paiement(),
                'montant' => number_format($paiement->get_montant(), 0, ',', ' '),
                'mode' => $paiement->get_mode_paiement(),
                'statut' => $paiement->get_statut(),
            ],
        ];
    }
}
