<?php
/**
 * Contrôleur de caisse.
 */
class CaisseController
{
    private $module;
    private $action;
    private $parametre;
    private $dao;

    public function __construct($module = 'caisses', $action = 'index', $parametre = null)
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
                $insertedId = $this->enregistrer_caisse($resultat['donnees']);
                if (!headers_sent() && $insertedId) {
                    header('Location: ' . BASE_URL . '/caisses/fiche/' . $insertedId);
                    exit;
                }

                $donnees = $this->preparer_liste([
                    'message' => 'Caisse créée avec succès.',
                ]);
                require TEMPLATES_PATH . 'caisses/liste.view.php';
                return;
            }

            $donnees = $this->preparer_formulaire($resultat);
            require TEMPLATES_PATH . 'caisses/formulaire.view.php';
            return;
        }

        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'caisses/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'caisses/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'caisses/liste.view.php';
    }

    private function preparer_formulaire(array $resultat = []): array
    {
        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ], $resultat);
    }

    private function preparer_liste(array $resultat = []): array
    {
        $caisses = $this->recuperer_caisses();

        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'caisses' => array_map(function (array $caisse): array {
                return [
                    'id' => $caisse['id_caisse'],
                    'date' => $caisse['date_caisse'],
                    'fond' => number_format((float) $caisse['fond_de_caisse'], 0, ',', ' '),
                ];
            }, $caisses),
        ], $resultat);
    }

    private function recuperer_caisses(): array
    {
        return $this->dao->all('caisses');
    }

    private function enregistrer_caisse(array $donnees): int
    {
        if ($this->dao instanceof FinanceDAO) {
            return $this->dao->insertCaisse([
                'date_caisse' => $donnees['date_caisse'],
                'fond_de_caisse' => $donnees['fond_de_caisse'],
            ]);
        }

        if (!isset($_SESSION['caisses']) || !is_array($_SESSION['caisses'])) {
            $_SESSION['caisses'] = [];
        }

        $id = generer_identifiant($_SESSION['caisses'], 'id_caisse');
        $_SESSION['caisses'][] = [
            'id_caisse' => $id,
            'date_caisse' => $donnees['date_caisse'],
            'fond_de_caisse' => $donnees['fond_de_caisse'],
        ];

        return $id;
    }

    private function traiter_formulaire(array $donnees): array
    {
        $erreurs = [];
        $date_caisse = nettoyer_chaine($donnees['date_caisse'] ?? '');
        $fond_de_caisse = $donnees['fond_de_caisse'] ?? null;

        if ($date_caisse === '') {
            $erreurs['date_caisse'] = 'La date de caisse est obligatoire.';
        }

        if ($fond_de_caisse === null || $fond_de_caisse === '' || !is_numeric($fond_de_caisse)) {
            $erreurs['fond_de_caisse'] = 'Le fond de caisse initial doit être un nombre valide.';
        } else {
            $fond_de_caisse = (float) $fond_de_caisse;
        }

        if (empty($donnees['token_csrf']) || !verifier_token_csrf((string) $donnees['token_csrf'])) {
            $erreurs['token_csrf'] = 'Jeton CSRF invalide ou manquant.';
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'date_caisse' => $date_caisse,
                'fond_de_caisse' => $fond_de_caisse,
            ],
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $data = $this->dao->getById('caisse', $id);

        if (!empty($data)) {
            $caisse = new Caisse($data);
        } else {
            $caisse = new Caisse(['id_caisse' => $id, 'date_caisse' => '2026-10-01', 'fond_de_caisse' => 150000.00]);
        }

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'caisse' => [
                'id' => $caisse->get_id_caisse(),
                'date' => $caisse->get_date_caisse(),
                'fond' => number_format($caisse->get_fond_de_caisse(), 0, ',', ' '),
            ],
        ];
    }
}
