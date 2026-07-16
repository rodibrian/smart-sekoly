<?php
/**
 * Contrôleur des paiements.
 */
class PaiementController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'paiements', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultat = $this->traiter_formulaire($_POST);

            if ($resultat['valide']) {
                $this->enregistrer_paiement($resultat['donnees']);
                $donnees = $this->preparer_liste(['message' => 'Paiement enregistré avec succès.']);
                require TEMPLATES_PATH . 'paiements/liste.view.php';
                return;
            }

            $donnees = $this->preparer_formulaire($resultat);
            require TEMPLATES_PATH . 'paiements/formulaire.view.php';
            return;
        }

        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'paiements/fiche.view.php';
            return;
        }

        if ($this->action === 'nouveau') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'paiements/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'paiements/liste.view.php';
    }

    private function preparer_formulaire(array $resultat = []): array
    {
        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'modes' => ['espece' => 'Espèce', 'banque' => 'Banque', 'mobile_money' => 'Mobile money'],
        ], $resultat);
    }

    private function preparer_liste(array $resultat = []): array
    {
        $paiements = $this->recuperer_paiements();

        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'paiements' => array_map(function (array $paiement): array {
                return [
                    'id' => $paiement['id_paiement'],
                    'recu' => $paiement['numero_recu'],
                    'date' => $paiement['date_paiement'],
                    'montant' => number_format((float) $paiement['montant'], 0, ',', ' '),
                    'mode' => $paiement['mode_paiement'],
                    'statut' => $paiement['statut'],
                ];
            }, $paiements),
        ], $resultat);
    }

    private function recuperer_paiements(): array
    {
        if (!empty($_SESSION['paiements']) && is_array($_SESSION['paiements'])) {
            return $_SESSION['paiements'];
        }

        return [
            ['id_paiement' => 1, 'id_echeance' => 1, 'numero_recu' => 'REC-2026-001', 'date_paiement' => '2026-10-01 09:00:00', 'montant' => 50000.00, 'mode_paiement' => 'espece', 'statut' => 'actif'],
            ['id_paiement' => 2, 'id_echeance' => 2, 'numero_recu' => 'REC-2026-002', 'date_paiement' => '2026-10-02 11:15:00', 'montant' => 75000.00, 'mode_paiement' => 'mobile_money', 'statut' => 'actif'],
        ];
    }

    private function enregistrer_paiement(array $donnees): void
    {
        if (!isset($_SESSION['paiements']) || !is_array($_SESSION['paiements'])) {
            $_SESSION['paiements'] = [];
        }

        $_SESSION['paiements'][] = [
            'id_paiement' => generer_identifiant($_SESSION['paiements'], 'id_paiement'),
            'id_echeance' => $donnees['id_echeance'],
            'numero_recu' => $donnees['numero_recu'],
            'date_paiement' => $donnees['date_paiement'],
            'montant' => $donnees['montant'],
            'mode_paiement' => $donnees['mode_paiement'],
            'statut' => 'actif',
        ];
    }

    private function traiter_formulaire(array $donnees): array
    {
        $erreurs = [];
        $id_echeance = isset($donnees['id_echeance']) ? (int) $donnees['id_echeance'] : null;
        $numero_recu = nettoyer_chaine($donnees['numero_recu'] ?? '');
        $date_paiement = nettoyer_chaine($donnees['date_paiement'] ?? '');
        $montant = $donnees['montant'] ?? null;
        $mode_paiement = nettoyer_chaine($donnees['mode_paiement'] ?? '');

        if ($id_echeance === null || $id_echeance <= 0) {
            $erreurs['id_echeance'] = 'L’identifiant de l’échéance est requis.';
        }

        if ($numero_recu === '') {
            $erreurs['numero_recu'] = 'Le numéro de reçu est requis.';
        }

        if ($date_paiement === '') {
            $erreurs['date_paiement'] = 'La date du paiement est requise.';
        }

        if ($montant === null || $montant === '' || !is_numeric($montant)) {
            $erreurs['montant'] = 'Le montant doit être un nombre valide.';
        } else {
            $montant = (float) $montant;
        }

        if ($mode_paiement === '' || !in_array($mode_paiement, ['espece', 'banque', 'mobile_money'], true)) {
            $erreurs['mode_paiement'] = 'Le mode de paiement est invalide.';
        }

        if (empty($donnees['token_csrf']) || !verifier_token_csrf((string) $donnees['token_csrf'])) {
            $erreurs['token_csrf'] = 'Jeton CSRF invalide ou manquant.';
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'id_echeance' => $id_echeance,
                'numero_recu' => $numero_recu,
                'date_paiement' => $date_paiement,
                'montant' => $montant,
                'mode_paiement' => $mode_paiement,
            ],
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $paiement = new Paiement([
            'id_paiement' => $id,
            'id_echeance' => 1,
            'numero_recu' => 'REC-2026-001',
            'date_paiement' => '2026-10-01 09:00:00',
            'montant' => 50000.00,
            'mode_paiement' => 'espece',
            'statut' => 'actif',
        ]);

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'paiement' => [
                'id' => $paiement->get_id_paiement(),
                'recu' => $paiement->get_numero_recu(),
                'date' => $paiement->get_date_paiement(),
                'montant' => number_format($paiement->get_montant(), 0, ',', ' '),
                'mode' => $paiement->get_mode_paiement(),
                'statut' => $paiement->get_statut(),
            ],
        ];
    }
}
