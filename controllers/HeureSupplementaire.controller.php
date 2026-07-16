<?php
/**
 * Contrôleur de gestion des heures supplémentaires des enseignants.
 */
class HeureSupplementaireController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'heures-supplementaires', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'liste') {
            $donnees = $this->preparer_liste();
            require TEMPLATES_PATH . 'heures_supplementaires/liste.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'heures_supplementaires/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_fiche();
        require TEMPLATES_PATH . 'heures_supplementaires/fiche.view.php';
    }

    private function preparer_liste(): array
    {
        $heures = [
            [
                'id' => 1,
                'enseignant' => 'Rakoto Jean',
                'classe' => '6e A',
                'matiere' => 'Mathématiques',
                'date' => '2026-09-15',
                'nombre_heures' => 4.5,
                'taux' => 15000,
                'montant' => 67500,
                'statut' => 'en attente',
            ],
            [
                'id' => 2,
                'enseignant' => 'Randrianarisoa Fara',
                'classe' => '5e B',
                'matiere' => 'Physique',
                'date' => '2026-09-12',
                'nombre_heures' => 3,
                'taux' => 15000,
                'montant' => 45000,
                'statut' => 'validé',
            ],
            [
                'id' => 3,
                'enseignant' => 'Rajaonarivony Mira',
                'classe' => '4e C',
                'matiere' => 'Français',
                'date' => '2026-09-18',
                'nombre_heures' => 2.5,
                'taux' => 15000,
                'montant' => 37500,
                'statut' => 'refusé',
            ],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'heures' => $heures,
        ];
    }

    private function preparer_formulaire(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'enseignants' => [
                ['id' => 1, 'nom' => 'Rakoto Jean'],
                ['id' => 2, 'nom' => 'Randrianarisoa Fara'],
                ['id' => 3, 'nom' => 'Rajaonarivony Mira'],
            ],
            'classes' => ['6e A', '5e B', '4e C'],
            'matieres' => ['Mathématiques', 'Physique', 'Français'],
        ];
    }

    private function preparer_fiche(): array
    {
        $heure = new HeureSupplementaire([
            'id_enseignant' => 1,
            'id_classe' => 2,
            'id_matiere' => 3,
            'date_heure' => '2026-09-15',
            'nombre_heures' => 4.5,
            'taux' => 15000,
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'heure' => [
                'date_heure' => $heure->get_date_heure(),
                'nombre_heures' => $heure->get_nombre_heures(),
                'taux' => $heure->get_taux(),
                'montant' => $heure->get_montant(),
                'statut' => $heure->get_statut(),
            ],
        ];
    }
}
