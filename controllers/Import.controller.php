<?php
/**
 * Contrôleur d'import et migration de données.
 */
class ImportController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'import', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $import = new ImportDonnees();
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'modele_csv' => $import->generer_modele(),
            'resultat' => null,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fichier = $_FILES['fichier_csv']['tmp_name'] ?? null;
            if ($fichier !== null && is_file($fichier)) {
                $donnees['resultat'] = $import->importer($fichier);
            } else {
                $donnees['resultat'] = [
                    'total_lignes' => 0,
                    'lignes_validees' => 0,
                    'lignes_erreur' => 1,
                    'erreurs' => ['Aucun fichier n’a été fourni.'],
                ];
            }
        }

        require TEMPLATES_PATH . 'import/import.view.php';
    }
}
