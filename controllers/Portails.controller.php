<?php
/**
 * Contrôleur du module Portails Élève / Parent.
 * Gère l'accès sécurisé, la consultation, le portail paiements, emploi du temps et repas.
 */

class PortailsController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'portails', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $action = $this->action ?? 'index';
        $data = match ($action) {
            'acces-codes' => $this->preparer_acces_codes(),
            'portail-consultation' => $this->preparer_portail_consultation(),
            'portail-paiements' => $this->preparer_portail_paiements(),
            'emplois-du-temps' => $this->preparer_emplois_du_temps(),
            'repas' => $this->preparer_repas(),
            default => $this->preparer_index(),
        };

        require $this->templatePath() . 'portails/' . $this->getNomVue($action) . '.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }

    private function getNomVue(string $action): string
    {
        return match ($action) {
            'acces-codes' => 'acces-codes',
            'portail-consultation' => 'portail-consultation',
            'portail-paiements' => 'portail-paiements',
            'emplois-du-temps' => 'emplois-du-temps',
            'repas' => 'repas',
            default => 'index',
        };
    }

    private function preparer_index(): array
    {
        $portails = $_SESSION['portails']['codes'] ?? [];
        $data = [
            'module' => $this->module,
            'action' => 'index',
            'token_csrf' => generer_token_csrf(),
            'portails' => $portails,
            'statut' => [
                'codes_actifs' => count($portails),
                'eleves_actifs' => count($_SESSION['eleves'] ?? []),
                'parents_actifs' => count(array_unique(array_map(fn($code) => $code['parent_nom'], $portails)))
            ]
        ];

        return $data;
    }

    private function preparer_acces_codes(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_acces_codes();
        }

        $codes = $_SESSION['portails']['codes'] ?? [];
        $eleves = $_SESSION['eleves'] ?? [];
        $data = [
            'module' => $this->module,
            'action' => 'acces-codes',
            'token_csrf' => generer_token_csrf(),
            'codes' => $codes,
            'eleves' => $eleves,
            'parents_types' => ['Père', 'Mère', 'Tuteur']
        ];

        return $data;
    }

    private function preparer_portail_consultation(): array
    {
        $data = [
            'module' => $this->module,
            'action' => 'portail-consultation',
            'token_csrf' => generer_token_csrf(),
            'portails' => $_SESSION['portails']['codes'] ?? [],
            'modes' => ['élève' => 'Consultation élève', 'parent' => 'Consultation parent']
        ];

        return $data;
    }

    private function preparer_portail_paiements(): array
    {
        $factures = $_SESSION['factures'] ?? [];
        $paiements = $_SESSION['paiements'] ?? [];

        $data = [
            'module' => $this->module,
            'action' => 'portail-paiements',
            'token_csrf' => generer_token_csrf(),
            'factures' => $factures,
            'paiements' => $paiements,
            'total_factures' => array_sum(array_map(fn($f) => $f['montant'] ?? 0, $factures)),
            'total_paye' => array_sum(array_map(fn($p) => $p['montant'] ?? 0, $paiements))
        ];

        return $data;
    }

    private function preparer_emplois_du_temps(): array
    {
        $data = [
            'module' => $this->module,
            'action' => 'emplois-du-temps',
            'token_csrf' => generer_token_csrf(),
            'emplois' => [
                ['jour' => 'Lundi', 'matieres' => 'Maths, Français, EPS'],
                ['jour' => 'Mardi', 'matieres' => 'Sciences, Anglais, Histoire'],
                ['jour' => 'Mercredi', 'matieres' => 'Technologie, Arts Plastiques'],
                ['jour' => 'Jeudi', 'matieres' => 'Maths, Français, Musique'],
                ['jour' => 'Vendredi', 'matieres' => 'SVT, EPS, Informatique']
            ]
        ];

        return $data;
    }

    private function preparer_repas(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_repas();
        }

        $reservations = $_SESSION['portails']['repas'] ?? [];
        $eleves = $_SESSION['eleves'] ?? [];

        $data = [
            'module' => $this->module,
            'action' => 'repas',
            'token_csrf' => generer_token_csrf(),
            'reservations' => $reservations,
            'eleves' => $eleves,
            'options' => ['Déjeuner', 'Dîner', 'Repas végétarien', 'Repas sans gluten']
        ];

        return $data;
    }

    private function traiter_post_acces_codes(): void
    {
        $parent_nom = nettoyer_chaine($_POST['parent_nom'] ?? '');
        $type = nettoyer_chaine($_POST['parent_type'] ?? '');
        $eleves_selectionnes = $_POST['eleves'] ?? [];

        if (empty($parent_nom) || empty($type) || empty($eleves_selectionnes)) {
            return;
        }

        $code = strtoupper(substr(sha1($parent_nom . microtime(true)), 0, 10));
        $acces = new AccesParentEleve([
            'parent_nom' => $parent_nom,
            'parent_type' => $type,
            'code' => $code,
            'enfants' => $eleves_selectionnes,
            'statut' => 'actif'
        ]);

        if (!isset($_SESSION['portails']['codes'])) {
            $_SESSION['portails']['codes'] = [];
        }

        $_SESSION['portails']['codes'][] = $acces->toArray();
    }

    private function traiter_post_repas(): void
    {
        $id_eleve = (int) ($_POST['eleve'] ?? 0);
        $option = nettoyer_chaine($_POST['option_repas'] ?? '');

        if ($id_eleve <= 0 || $option === '') {
            return;
        }

        $reservation = [
            'id' => uniqid('repas_', true),
            'id_eleve' => $id_eleve,
            'option' => $option,
            'date' => date('d/m/Y'),
            'statut' => 'confirmé'
        ];

        if (!isset($_SESSION['portails']['repas'])) {
            $_SESSION['portails']['repas'] = [];
        }
        $_SESSION['portails']['repas'][] = $reservation;
    }
}
