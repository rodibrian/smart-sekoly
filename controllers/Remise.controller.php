<?php
/**
 * Contrôleur des remises.
 */
class RemiseController
{
    private $module;
    private $action;
    private $parametre;
    private $dao;

    public function __construct($module = 'remises', $action = 'index', $parametre = null)
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
                $insertedId = $this->enregistrer_remise($resultat['donnees']);
                if (!headers_sent() && $insertedId) {
                    header('Location: ' . BASE_URL . '/remises/fiche/' . $insertedId);
                    exit;
                }

                $donnees = $this->preparer_liste(['message' => 'Remise créée avec succès.']);
                require TEMPLATES_PATH . 'remises/liste.view.php';
                return;
            }

            $donnees = $this->preparer_formulaire($resultat);
            require TEMPLATES_PATH . 'remises/formulaire.view.php';
            return;
        }

        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'remises/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'remises/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'remises/liste.view.php';
    }

    private function preparer_formulaire(array $resultat = []): array
    {
        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'types' => [
                'pourcentage' => 'Pourcentage',
                'montant_fixe' => 'Montant fixe',
            ],
        ], $resultat);
    }

    private function preparer_liste(array $resultat = []): array
    {
        $remises = $this->recuperer_remises();

        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'remises' => array_map(function (array $remise): array {
                return [
                    'id' => $remise['id_remise'],
                    'type' => $remise['type_remise'],
                    'valeur' => $remise['valeur_remise'],
                    'motif' => $remise['motif'],
                ];
            }, $remises),
        ], $resultat);
    }

    private function recuperer_remises(): array
    {
        // Prefer DAO when available
        if ($this->dao instanceof FinanceDAO) {
            $rows = $this->dao->all('remises');
            if (!empty($rows)) {
                return $rows;
            }
        }

        if (!empty($_SESSION['remises']) && is_array($_SESSION['remises'])) {
            return $_SESSION['remises'];
        }

        return [
            ['id_remise' => 1, 'type_remise' => 'pourcentage', 'valeur_remise' => 10.0, 'motif' => 'Bourse sociale', 'id_utilisateur_validation' => 1],
            ['id_remise' => 2, 'type_remise' => 'montant_fixe', 'valeur_remise' => 15000.00, 'motif' => 'Remise fidélité', 'id_utilisateur_validation' => 2],
        ];
    }

    private function enregistrer_remise(array $donnees): int
    {
        $payload = array_merge($donnees, ['id_utilisateur_validation' => 1]);
        if ($this->dao instanceof FinanceDAO) {
            return $this->dao->insertRemise($payload);
        }

        if (!isset($_SESSION['remises']) || !is_array($_SESSION['remises'])) {
            $_SESSION['remises'] = [];
        }

        $id = generer_identifiant($_SESSION['remises'], 'id_remise');
        $_SESSION['remises'][] = array_merge(['id_remise' => $id], $payload);

        return $id;
    }

    private function traiter_formulaire(array $donnees): array
    {
        $erreurs = [];
        $type_remise = nettoyer_chaine($donnees['type_remise'] ?? '');
        $valeur_remise = $donnees['valeur_remise'] ?? null;
        $motif = nettoyer_chaine($donnees['motif'] ?? '');

        if ($type_remise === '' || !in_array($type_remise, ['pourcentage', 'montant_fixe'], true)) {
            $erreurs['type_remise'] = 'Le type de remise est invalide.';
        }

        if ($valeur_remise === null || $valeur_remise === '' || !is_numeric($valeur_remise)) {
            $erreurs['valeur_remise'] = 'La valeur de la remise doit être un nombre valide.';
        } else {
            $valeur_remise = (float) $valeur_remise;
        }

        if ($motif === '') {
            $erreurs['motif'] = 'Le motif de la remise est obligatoire.';
        }

        if (empty($donnees['token_csrf']) || !verifier_token_csrf((string) $donnees['token_csrf'])) {
            $erreurs['token_csrf'] = 'Jeton CSRF invalide ou manquant.';
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'type_remise' => $type_remise,
                'valeur_remise' => $valeur_remise,
                'motif' => $motif,
            ],
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $remise = new Remise([
            'id_remise' => $id,
            'type_remise' => 'pourcentage',
            'valeur_remise' => 10.0,
            'motif' => 'Bourse sociale',
            'id_utilisateur_validation' => 1,
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'remise' => [
                'id' => $remise->get_id_remise(),
                'type' => $remise->get_type_remise(),
                'valeur' => $remise->get_valeur_remise(),
                'motif' => $remise->get_motif(),
            ],
        ];
    }
}
