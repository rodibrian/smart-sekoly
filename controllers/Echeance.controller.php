<?php
/**
 * Contrôleur des échéances.
 */
class EcheanceController
{
    private $module;
    private $action;
    private $parametre;
    private $dao;

    public function __construct($module = 'echeances', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
        $this->dao = new FinanceDAO();
    }

    public function executer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultat = $this->traiter_formulaire($_POST);

            if ($resultat['valide']) {
                $this->enregistrer_echeance($resultat['donnees']);
                $donnees = $this->preparer_liste(['message' => 'Échéance créée avec succès.']);
                require TEMPLATES_PATH . 'echeances/liste.view.php';
                return;
            }

            $donnees = $this->preparer_formulaire($resultat);
            require TEMPLATES_PATH . 'echeances/formulaire.view.php';
            return;
        }

        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'echeances/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'echeances/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'echeances/liste.view.php';
    }

    private function preparer_formulaire(array $resultat = []): array
    {
        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'statuts' => ['payee' => 'Payée', 'partielle' => 'Partielle', 'en_retard' => 'En retard'],
        ], $resultat);
    }

    private function preparer_liste(array $resultat = []): array
    {
        $echeances = $this->recuperer_echeances();

        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'echeances' => array_map(function (array $echeance): array {
                return [
                    'id' => $echeance['id_echeance'],
                    'facture' => $echeance['id_facture'],
                    'date' => $echeance['date_echeance'],
                    'montant' => number_format((float) $echeance['montant_prevu'], 0, ',', ' '),
                    'statut' => $echeance['statut_echeance'],
                ];
            }, $echeances),
        ], $resultat);
    }

    private function recuperer_echeances(): array
    {
        // Prefer DAO when available
        if ($this->dao instanceof FinanceDAO) {
            $rows = $this->dao->all('echeances');
            if (!empty($rows)) {
                return $rows;
            }
        }

        if (!empty($_SESSION['echeances']) && is_array($_SESSION['echeances'])) {
            return $_SESSION['echeances'];
        }

        return [
            ['id_echeance' => 1, 'id_facture' => 1, 'date_echeance' => '2026-10-01', 'montant_prevu' => 70000.00, 'statut_echeance' => 'payee'],
            ['id_echeance' => 2, 'id_facture' => 1, 'date_echeance' => '2026-11-01', 'montant_prevu' => 70000.00, 'statut_echeance' => 'en_retard'],
        ];
    }

    private function enregistrer_echeance(array $donnees): void
    {
        if ($this->dao instanceof FinanceDAO) {
            $this->dao->insertEcheance($donnees);
            return;
        }

        if (!isset($_SESSION['echeances']) || !is_array($_SESSION['echeances'])) {
            $_SESSION['echeances'] = [];
        }

        $_SESSION['echeances'][] = [
            'id_echeance' => generer_identifiant($_SESSION['echeances'], 'id_echeance'),
            'id_facture' => $donnees['id_facture'],
            'date_echeance' => $donnees['date_echeance'],
            'montant_prevu' => $donnees['montant_prevu'],
            'statut_echeance' => $donnees['statut_echeance'],
        ];
    }

    private function traiter_formulaire(array $donnees): array
    {
        $erreurs = [];
        $id_facture = isset($donnees['id_facture']) ? (int) $donnees['id_facture'] : null;
        $date_echeance = nettoyer_chaine($donnees['date_echeance'] ?? '');
        $montant_prevu = $donnees['montant_prevu'] ?? null;
        $statut_echeance = nettoyer_chaine($donnees['statut_echeance'] ?? '');

        if ($id_facture === null || $id_facture <= 0) {
            $erreurs['id_facture'] = 'L’identifiant de la facture est requis.';
        }

        if ($date_echeance === '') {
            $erreurs['date_echeance'] = 'La date d’échéance est requise.';
        }

        if ($montant_prevu === null || $montant_prevu === '' || !is_numeric($montant_prevu)) {
            $erreurs['montant_prevu'] = 'Le montant prévu doit être un nombre valide.';
        } else {
            $montant_prevu = (float) $montant_prevu;
        }

        if ($statut_echeance === '' || !in_array($statut_echeance, ['payee', 'partielle', 'en_retard'], true)) {
            $erreurs['statut_echeance'] = 'Le statut de l’échéance est invalide.';
        }

        if (empty($donnees['token_csrf']) || !verifier_token_csrf((string) $donnees['token_csrf'])) {
            $erreurs['token_csrf'] = 'Jeton CSRF invalide ou manquant.';
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'id_facture' => $id_facture,
                'date_echeance' => $date_echeance,
                'montant_prevu' => $montant_prevu,
                'statut_echeance' => $statut_echeance,
            ],
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $echeance = new Echeance([
            'id_echeance' => $id,
            'id_facture' => 1,
            'date_echeance' => '2026-10-01',
            'montant_prevu' => 70000.00,
            'statut_echeance' => 'payee',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'echeance' => [
                'id' => $echeance->get_id_echeance(),
                'facture' => $echeance->get_id_facture(),
                'date' => $echeance->get_date_echeance(),
                'montant' => number_format($echeance->get_montant_prevu(), 0, ',', ' '),
                'statut' => $echeance->get_statut_echeance(),
            ],
        ];
    }
}
