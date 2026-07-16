<?php
/**
 * Contrôleur du tableau de bord principal.
 */
class TableauDeBordController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'tableau-de-bord', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'agenda') {
            $donnees = $this->preparer_agenda();
            require $this->templatePath() . 'tableau_de_bord/agenda.view.php';
            return;
        }

        if ($this->action === 'actualites') {
            $donnees = $this->preparer_actualites();
            require $this->templatePath() . 'tableau_de_bord/actualites.view.php';
            return;
        }

        if ($this->action === 'recherche') {
            $donnees = $this->preparer_recherche();
            require $this->templatePath() . 'tableau_de_bord/recherche.view.php';
            return;
        }

        if ($this->action === 'rapports') {
            $donnees = $this->preparer_rapports();
            require $this->templatePath() . 'tableau_de_bord/rapports.view.php';
            return;
        }

        if ($this->action === 'previsions') {
            $donnees = $this->preparer_previsions();
            require $this->templatePath() . 'tableau_de_bord/previsions.view.php';
            return;
        }

        if ($this->action === 'visionDirecteur') {
            $donnees = $this->preparer_visionDirecteur();
            require $this->templatePath() . 'tableau_de_bord/vision_directeur.view.php';
            return;
        }

        if ($this->action === 'comparatif') {
            $donnees = $this->preparer_comparatif();
            require $this->templatePath() . 'tableau_de_bord/comparatif.view.php';
            return;
        }

        $donnees = $this->preparer_indicateurs();
        require $this->templatePath() . 'tableau_de_bord/index.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }

    private function preparer_indicateurs(): array
    {
        $eleves_session = $_SESSION['eleves'] ?? [];
        $enseignants_session = $_SESSION['enseignants'] ?? [];
        $absences_session = $_SESSION['absences'] ?? [];
        $paiements_session = $_SESSION['paiements'] ?? [];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'indicateurs' => [
                'total_eleves' => count($eleves_session),
                'total_enseignants' => count($enseignants_session),
                'absences_mois' => count($absences_session),
                'paiements_mois' => count($paiements_session),
                'taux_presence' => count($eleves_session) > 0 
                    ? round(((count($eleves_session) - count($absences_session)) / count($eleves_session)) * 100, 1)
                    : 0,
            ],
        ];
    }

    private function preparer_agenda(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'agenda' => [
                ['heure' => '08:00', 'evenement' => 'Appel général', 'lieu' => 'Cour'],
                ['heure' => '09:00', 'evenement' => 'Cours de mathématiques', 'lieu' => 'Salle 101'],
                ['heure' => '11:00', 'evenement' => 'Réunion de staff', 'lieu' => 'Bureau directeur'],
                ['heure' => '14:00', 'evenement' => 'Séance de rattrapage', 'lieu' => 'Bibliothèque'],
            ],
        ];
    }

    private function preparer_actualites(): array
    {
        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'actualites' => [
                ['date' => '2026-07-16', 'titre' => 'Réunion de rentrée scolaire', 'contenu' => 'La réunion aura lieu le 31 juillet 2026'],
                ['date' => '2026-07-15', 'titre' => 'Actualisation des fiches élèves', 'contenu' => 'Veuillez mettre à jour les fiches avant le 20 juillet'],
                ['date' => '2026-07-14', 'titre' => 'Paiement des frais avant congés', 'contenu' => 'Dernier délai pour effectuer les paiements'],
            ],
        ];
    }

    private function preparer_recherche(): array
    {
        $resultats = [];
        if (isset($_GET['q'])) {
            $query = strtolower(trim($_GET['q']));
            $eleves = $_SESSION['eleves'] ?? [];
            $enseignants = $_SESSION['enseignants'] ?? [];

            foreach ($eleves as $eleve) {
                if (stripos($eleve['nom'] ?? '', $query) !== false || 
                    stripos($eleve['prenom'] ?? '', $query) !== false || 
                    stripos($eleve['matricule'] ?? '', $query) !== false) {
                    $resultats[] = ['type' => 'Élève', 'nom' => $eleve['prenom'] . ' ' . $eleve['nom'], 'matricule' => $eleve['matricule'] ?? ''];
                }
            }

            foreach ($enseignants as $enseignant) {
                if (stripos($enseignant['nom'] ?? '', $query) !== false || 
                    stripos($enseignant['prenom'] ?? '', $query) !== false) {
                    $resultats[] = ['type' => 'Enseignant', 'nom' => $enseignant['prenom'] . ' ' . $enseignant['nom'], 'matricule' => $enseignant['matricule'] ?? ''];
                }
            }
        }

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'query' => $_GET['q'] ?? '',
            'resultats' => $resultats,
        ];
    }

    private function preparer_rapports(): array
    {
        $rapports = [
            ['mois' => 'Juillet 2026', 'total_eleves' => count($_SESSION['eleves'] ?? []), 'presences' => 95, 'paiements' => 87],
            ['mois' => 'Juin 2026', 'total_eleves' => count($_SESSION['eleves'] ?? []), 'presences' => 93, 'paiements' => 85],
            ['mois' => 'Mai 2026', 'total_eleves' => count($_SESSION['eleves'] ?? []), 'presences' => 91, 'paiements' => 82],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'rapports' => $rapports,
        ];
    }

    private function preparer_previsions(): array
    {
        $previsions = [
            ['annee' => '2026-2027', 'eleves_attendus' => 250, 'enseignants_requis' => 18, 'budjet_estime' => 150000],
            ['annee' => '2027-2028', 'eleves_attendus' => 280, 'enseignants_requis' => 20, 'budjet_estime' => 168000],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'previsions' => $previsions,
        ];
    }

    private function preparer_visionDirecteur(): array
    {
        $eleves_total = count($_SESSION['eleves'] ?? []);
        $places_totales = 300;
        $taux_occupation = $eleves_total > 0 ? round(($eleves_total / $places_totales) * 100, 1) : 0;
        $enseignants = count($_SESSION['enseignants'] ?? []);
        $ratio_eleve_enseignant = $enseignants > 0 ? round($eleves_total / $enseignants, 1) : 0;

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'vision' => [
                'taux_occupation' => $taux_occupation,
                'places_utilisees' => $eleves_total,
                'places_totales' => $places_totales,
                'ratio_eleve_enseignant' => $ratio_eleve_enseignant,
                'enseignants_total' => $enseignants,
                'revenus_mensuels' => count($_SESSION['paiements'] ?? []) * 5000,
            ],
        ];
    }

    private function preparer_comparatif(): array
    {
        $comparatif = [
            ['periode' => '2024-2025', 'eleves' => 180, 'presences' => 88, 'reussite' => 75],
            ['periode' => '2025-2026', 'eleves' => 210, 'presences' => 91, 'reussite' => 82],
            ['periode' => '2026-2027 (prévision)', 'eleves' => 250, 'presences' => 93, 'reussite' => 85],
        ];

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'comparatif' => $comparatif,
        ];
    }
}
