<?php
/**
 * Contrôleur du tableau de bord RH.
 */
class TableauDeBordRhController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'tableau-de-bord-rh', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $templateBase = defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
        $donnees = $this->preparer_donnees();
        require $templateBase . 'tableau_de_bord_rh/dashboard.view.php';
    }

    private function preparer_donnees(): array
    {
        $enseignants = [
            ['nom' => 'Rakoto Jean', 'statut' => 'actif'],
            ['nom' => 'Randrianarisoa Fara', 'statut' => 'actif'],
            ['nom' => 'Rajaonarivony Mira', 'statut' => 'en_conge'],
        ];

        $contrats = [
            ['type' => 'permanent', 'statut' => 'actif'],
            ['type' => 'horaire', 'statut' => 'actif'],
            ['type' => 'CDD', 'statut' => 'termine'],
        ];

        $conges = [
            ['enseignant' => 'Rajaonarivony Mira', 'type' => 'annuel', 'periode' => '2026-10-01 → 2026-10-15', 'statut' => 'en attente'],
            ['enseignant' => 'Rakoto Jean', 'type' => 'maladie', 'periode' => '2026-09-01 → 2026-09-10', 'statut' => 'validé'],
        ];

        $heures = [
            ['enseignant' => 'Randrianarisoa Fara', 'heures' => 4.5, 'statut' => 'en attente'],
            ['enseignant' => 'Rakoto Jean', 'heures' => 2.0, 'statut' => 'validé'],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'indicateurs' => [
                'enseignants_actifs' => count(array_filter($enseignants, fn($item) => $item['statut'] === 'actif')),
                'conges_en_attente' => count(array_filter($conges, fn($item) => $item['statut'] === 'en attente')),
                'heures_en_attente' => count(array_filter($heures, fn($item) => $item['statut'] === 'en attente')),
                'contrats_actifs' => count(array_filter($contrats, fn($item) => $item['statut'] === 'actif')),
            ],
            'contrats' => $contrats,
            'conges' => $conges,
            'heures' => $heures,
        ];
    }
}
