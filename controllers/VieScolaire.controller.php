<?php
/**
 * Contrôleur du module vie scolaire et discipline.
 */
class VieScolaireController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'vie-scolaire', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'sanctions') {
            $donnees = $this->preparer_sanctions();
            require $this->templatePath() . 'vie_scolaire/sanctions.view.php';
            return;
        }

        if ($this->action === 'retards') {
            $donnees = $this->preparer_retards();
            require $this->templatePath() . 'vie_scolaire/retards.view.php';
            return;
        }

        if ($this->action === 'incidents') {
            $donnees = $this->preparer_incidents();
            require $this->templatePath() . 'vie_scolaire/incidents.view.php';
            return;
        }

        if ($this->action === 'billets') {
            $donnees = $this->preparer_billets();
            require $this->templatePath() . 'vie_scolaire/billets.view.php';
            return;
        }

        if ($this->action === 'sorties') {
            $donnees = $this->preparer_sorties();
            require $this->templatePath() . 'vie_scolaire/sorties.view.php';
            return;
        }

        if ($this->action === 'planning') {
            $donnees = $this->preparer_planning();
            require $this->templatePath() . 'vie_scolaire/planning.view.php';
            return;
        }

        if ($this->action === 'carnet') {
            $donnees = $this->preparer_carnet();
            require $this->templatePath() . 'vie_scolaire/carnet.view.php';
            return;
        }

        $donnees = $this->preparer_absences();
        require $this->templatePath() . 'vie_scolaire/absences.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }

    private function preparer_absences(): array
    {
        $absences = [
            new Absence(['id_eleve' => 1, 'date_absence' => '2026-07-10', 'motif' => 'Maladie', 'statut' => 'valide']),
            new Absence(['id_eleve' => 1, 'date_absence' => '2026-07-15', 'motif' => 'Rendez-vous médical', 'statut' => 'en_attente']),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'absences' => array_map(function (Absence $absence): array {
                return [
                    'date_absence' => $absence->get_date_absence(),
                    'motif' => $absence->get_motif(),
                    'statut' => $absence->get_statut(),
                ];
            }, $absences),
        ];
    }

    private function preparer_sanctions(): array
    {
        $sanctions = [
            new Sanction(['id_eleve' => 1, 'type' => 'avertissement', 'description' => 'Retard répété', 'statut' => 'proposee']),
            new Sanction(['id_eleve' => 1, 'type' => 'blâme', 'description' => 'Absence non justifiée', 'statut' => 'validee']),
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'sanctions' => array_map(function (Sanction $sanction): array {
                return [
                    'type' => $sanction->get_type(),
                    'description' => $sanction->get_description(),
                    'statut' => $sanction->get_statut(),
                ];
            }, $sanctions),
        ];
    }

    private function preparer_retards(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'retards' => [
                ['eleve' => 'Lova Andriamihaja', 'date' => '2026-07-16', 'duree' => '10 min', 'motif' => 'Trafic'],
                ['eleve' => 'Jean Rakoto', 'date' => '2026-07-16', 'duree' => '15 min', 'motif' => 'Retard de réveil'],
            ],
        ];
    }

    private function preparer_incidents(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'incidents' => [
                ['eleve' => 'Lova Andriamihaja', 'date' => '2026-07-16', 'type' => 'Problème de comportement', 'statut' => 'en attente'],
                ['eleve' => 'Jean Rakoto', 'date' => '2026-07-16', 'type' => 'Non-respect du règlement', 'statut' => 'traite'],
            ],
        ];
    }

    private function preparer_billets(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'billets' => [
                ['eleve' => 'Lova Andriamihaja', 'date' => '2026-07-16', 'type' => 'Entrée', 'motif' => 'Retard de transport'],
                ['eleve' => 'Jean Rakoto', 'date' => '2026-07-16', 'type' => 'Sortie', 'motif' => 'Rendez-vous parental'],
            ],
        ];
    }

    private function preparer_sorties(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'sorties' => [
                ['eleve' => 'Lova Andriamihaja', 'date' => '2026-07-16', 'heure' => '14:30', 'responsable' => 'Mme Rakoto', 'statut' => 'approuvee'],
                ['eleve' => 'Jean Rakoto', 'date' => '2026-07-16', 'heure' => '16:00', 'responsable' => 'M. Randria', 'statut' => 'en attente'],
            ],
        ];
    }

    private function preparer_planning(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'planning' => [
                ['surveillant' => 'M. Rabe', 'poste' => 'Entrée principale', 'heure' => '07:00-09:00'],
                ['surveillant' => 'Mme Ranaivo', 'poste' => 'Cour de récréation', 'heure' => '09:00-11:00'],
            ],
        ];
    }

    private function preparer_carnet(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'carnet' => [
                ['date' => '2026-07-16', 'evenement' => 'Réunion de classe', 'responsable' => 'Mme Noro'],
                ['date' => '2026-07-16', 'evenement' => 'Observation comportementale', 'responsable' => 'M. Solo'],
            ],
        ];
    }
}
