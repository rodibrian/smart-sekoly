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
        $heure = new HeureSupplementaire([
            'id_enseignant' => 1,
            'id_classe' => 2,
            'id_matiere' => 3,
            'date_heure' => '2026-09-15',
            'nombre_heures' => 4.5,
            'taux' => 15000,
        ]);

        $donnees = [
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

        require TEMPLATES_PATH . 'heures_supplementaires/fiche.view.php';
    }
}
