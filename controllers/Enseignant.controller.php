<?php
/**
 * Contrôleur de gestion des enseignants.
 */
class EnseignantController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'enseignants', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $enseignant = new Enseignant([
            'nom' => 'Rakoto',
            'prenom' => 'Jean',
            'email' => 'jean@example.com',
            'matricule' => 'ENS-2026-001',
        ]);

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'enseignant' => [
                'nom' => $enseignant->get_nom(),
                'prenom' => $enseignant->get_prenom(),
                'nom_complet' => $enseignant->get_nom_complet(),
                'email' => $enseignant->get_email(),
                'matricule' => $enseignant->get_matricule(),
                'telephone' => '032 12 345 67',
                'fonction' => 'Enseignant de mathématiques',
                'date_embauche' => '2024-01-15',
            ],
            'contrats' => [
                ['periode' => '2026-09', 'type_contrat' => 'horaire', 'statut' => 'actif'],
                ['periode' => '2025-09', 'type_contrat' => 'permanent', 'statut' => 'termine'],
            ],
        ];

        require TEMPLATES_PATH . 'enseignants/dossier.view.php';
    }
}
