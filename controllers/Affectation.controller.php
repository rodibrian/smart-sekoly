<?php
/**
 * Contrôleur de gestion des affectations pédagogiques.
 */
class AffectationController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'affectations', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'fiche') {
            $donnees = $this->preparer_donnees_fiche();
            require TEMPLATES_PATH . 'affectations/fiche.view.php';
            return;
        }

        $donnees = $this->preparer_donnees_liste();
        require TEMPLATES_PATH . 'affectations/liste.view.php';
    }

    private function preparer_donnees_liste(): array
    {
        $affectations = [
            new Affectation([
                'id_affectation' => 1,
                'id_enseignant' => 1,
                'id_matiere' => 1,
                'id_classe' => 1,
                'id_annee' => 2026,
                'date_affectation' => '2026-09-01',
                'statut' => 'active',
            ]),
            new Affectation([
                'id_affectation' => 2,
                'id_enseignant' => 2,
                'id_matiere' => 2,
                'id_classe' => 2,
                'id_annee' => 2026,
                'date_affectation' => '2026-09-01',
                'statut' => 'reaffectee',
            ]),
            new Affectation([
                'id_affectation' => 3,
                'id_enseignant' => 3,
                'id_matiere' => 3,
                'id_classe' => 3,
                'id_annee' => 2025,
                'date_affectation' => '2025-09-15',
                'statut' => 'terminee',
            ]),
        ];

        $liste = array_map(function (Affectation $affectation): array {
            $enseignant = $this->recuperer_nom_enseignant($affectation->get_id_enseignant());
            $classe = $this->recuperer_nom_classe($affectation->get_id_classe());
            $matiere = $this->recuperer_nom_matiere($affectation->get_id_matiere());

            return [
                'id' => $affectation->get_id_affectation(),
                'enseignant' => $enseignant,
                'classe' => $classe,
                'matiere' => $matiere,
                'annee' => $affectation->get_id_annee(),
                'date' => $affectation->get_date_affectation(),
                'statut' => $affectation->get_statut(),
            ];
        }, $affectations);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'affectations' => $liste,
        ];
    }

    private function preparer_donnees_fiche(): array
    {
        $id = (int) ($this->parametre ?? 0);

        $affectation = new Affectation([
            'id_affectation' => $id,
            'id_enseignant' => 1,
            'id_matiere' => 1,
            'id_classe' => 1,
            'id_annee' => 2026,
            'date_affectation' => '2026-09-01',
            'statut' => 'active',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'affectation' => [
                'id' => $affectation->get_id_affectation(),
                'enseignant' => $this->recuperer_nom_enseignant($affectation->get_id_enseignant()),
                'classe' => $this->recuperer_nom_classe($affectation->get_id_classe()),
                'matiere' => $this->recuperer_nom_matiere($affectation->get_id_matiere()),
                'annee' => $affectation->get_id_annee(),
                'date_affectation' => $affectation->get_date_affectation(),
                'statut' => $affectation->get_statut(),
            ],
        ];
    }

    private function recuperer_nom_enseignant(int $id_enseignant): string
    {
        $enseignants = [
            1 => 'Rakoto Jean',
            2 => 'Randrianarisoa Fara',
            3 => 'Rajaonarivony Mira',
        ];

        return $enseignants[$id_enseignant] ?? 'Inconnu';
    }

    private function recuperer_nom_classe(int $id_classe): string
    {
        $classes = [
            1 => '6e A',
            2 => '5e B',
            3 => '4e C',
        ];

        return $classes[$id_classe] ?? 'Classe inconnue';
    }

    private function recuperer_nom_matiere(int $id_matiere): string
    {
        $matieres = [
            1 => 'Mathématiques',
            2 => 'Physique',
            3 => 'Français',
        ];

        return $matieres[$id_matiere] ?? 'Matière inconnue';
    }
}
