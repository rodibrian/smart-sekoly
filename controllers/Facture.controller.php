<?php
/**
 * Contrôleur de facturation.
 */
class FactureController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'factures', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'factures/fiche.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'factures/liste.view.php';
    }

    private function preparer_liste(): array
    {
        $factures = [
            new Facture([
                'id_facture' => 1,
                'id_eleve' => 1,
                'numero_sequentiel' => 'FAC-2026-001',
                'date_emission' => '2026-09-01',
                'montant_total' => 210000.00,
                'statut' => 'active',
            ]),
            new Facture([
                'id_facture' => 2,
                'id_eleve' => 2,
                'numero_sequentiel' => 'FAC-2026-002',
                'date_emission' => '2026-09-05',
                'montant_total' => 175000.00,
                'statut' => 'annulee',
            ]),
        ];

        $liste = array_map(function (Facture $facture): array {
            return [
                'id' => $facture->get_id_facture(),
                'numero' => $facture->get_numero_sequentiel(),
                'date' => $facture->get_date_emission(),
                'montant_total' => number_format($facture->get_montant_total(), 0, ',', ' '),
                'statut' => $facture->get_statut(),
            ];
        }, $factures);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'factures' => $liste,
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);

        $facture = new Facture([
            'id_facture' => $id,
            'id_eleve' => 1,
            'numero_sequentiel' => 'FAC-2026-001',
            'date_emission' => '2026-09-01',
            'montant_total' => 210000.00,
            'statut' => 'active',
        ]);

        $remises = [
            new Remise([
                'type_remise' => 'pourcentage',
                'valeur_remise' => 10.0,
                'motif' => 'Bourse sociale',
                'id_utilisateur_validation' => 1,
            ]),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'facture' => [
                'id' => $facture->get_id_facture(),
                'numero' => $facture->get_numero_sequentiel(),
                'date_emission' => $facture->get_date_emission(),
                'montant_total' => number_format($facture->get_montant_total(), 0, ',', ' '),
                'montant_net' => number_format($facture->calculer_montant_net($remises), 0, ',', ' '),
                'statut' => $facture->get_statut(),
                'remises' => array_map(function (Remise $remise): array {
                    return [
                        'type' => $remise->get_type_remise(),
                        'valeur' => $remise->get_valeur_remise(),
                        'motif' => $remise->get_motif(),
                    ];
                }, $remises),
            ],
        ];
    }
}
