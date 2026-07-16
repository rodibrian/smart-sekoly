<?php
/**
 * Contrôleur de facturation.
 */
class FactureController
{
    private $module;
    private $action;
    private $parametre;
    private $dao;

    public function __construct($module = 'factures', $action = 'index', $parametre = null)
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
                $this->enregistrer_facture($resultat['donnees']);
                $donnees = $this->preparer_liste(['message' => 'Facture créée avec succès.']);
                require TEMPLATES_PATH . 'factures/liste.view.php';
                return;
            }

            $donnees = $this->preparer_formulaire($resultat);
            require TEMPLATES_PATH . 'factures/formulaire.view.php';
            return;
        }

        if ($this->action === 'fiche') {
            $donnees = $this->preparer_fiche();
            require TEMPLATES_PATH . 'factures/fiche.view.php';
            return;
        }

        if ($this->action === 'nouvelle') {
            $donnees = $this->preparer_formulaire();
            require TEMPLATES_PATH . 'factures/formulaire.view.php';
            return;
        }

        $donnees = $this->preparer_liste();
        require TEMPLATES_PATH . 'factures/liste.view.php';
    }

    private function preparer_formulaire(array $resultat = []): array
    {
        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'eleves' => [
                ['id' => 1, 'nom' => 'Rajaonarivony Mira'],
                ['id' => 2, 'nom' => 'Rakoto Jean'],
            ],
        ], $resultat);
    }

    private function preparer_liste(array $resultat = []): array
    {
        $factures = $this->recuperer_factures();

        $liste = array_map(function (array $facture): array {
            return [
                'id' => $facture['id_facture'] ?? $facture['id'] ?? null,
                'numero' => $facture['numero_sequentiel'] ?? $facture['numero'] ?? '',
                'date' => $facture['date_emission'] ?? $facture['date'] ?? '',
                'montant_total' => number_format((float) ($facture['montant_total'] ?? 0), 0, ',', ' '),
                'statut' => $facture['statut'] ?? '',
            ];
        }, $factures);

        return array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'factures' => $liste,
        ], $resultat);
    }

    private function recuperer_factures(): array
    {
        // Prefer DAO (DB) when available
        if ($this->dao instanceof FinanceDAO) {
            $factures = $this->dao->all('factures');
            if (!empty($factures)) {
                return $factures;
            }
        }

        if (!empty($_SESSION['factures']) && is_array($_SESSION['factures'])) {
            return $_SESSION['factures'];
        }

        return [
            ['id_facture' => 1, 'id_eleve' => 1, 'numero_sequentiel' => 'FAC-2026-001', 'date_emission' => '2026-09-01', 'montant_total' => 210000.00, 'statut' => 'active'],
            ['id_facture' => 2, 'id_eleve' => 2, 'numero_sequentiel' => 'FAC-2026-002', 'date_emission' => '2026-09-05', 'montant_total' => 175000.00, 'statut' => 'annulee'],
        ];
    }

    private function enregistrer_facture(array $donnees): void
    {
        if ($this->dao instanceof FinanceDAO) {
            $this->dao->insertFacture([
                'id_eleve' => $donnees['id_eleve'],
                'numero_sequentiel' => $donnees['numero'],
                'date_emission' => $donnees['date_emission'],
                'montant_total' => $donnees['montant_total'],
                'statut' => 'active',
            ]);
            return;
        }

        if (!isset($_SESSION['factures']) || !is_array($_SESSION['factures'])) {
            $_SESSION['factures'] = [];
        }

        $_SESSION['factures'][] = [
            'id_facture' => generer_identifiant($_SESSION['factures'], 'id_facture'),
            'id_eleve' => $donnees['id_eleve'],
            'numero_sequentiel' => $donnees['numero'],
            'date_emission' => $donnees['date_emission'],
            'montant_total' => $donnees['montant_total'],
            'statut' => 'active',
        ];
    }

    private function traiter_formulaire(array $donnees): array
    {
        $erreurs = [];
        $id_eleve = isset($donnees['eleve']) ? (int) $donnees['eleve'] : null;
        $numero = nettoyer_chaine($donnees['numero'] ?? '');
        $date_emission = nettoyer_chaine($donnees['date_emission'] ?? '');
        $montant_total = $donnees['montant_total'] ?? null;

        if ($id_eleve === null || $id_eleve <= 0) {
            $erreurs['eleve'] = 'L’élève est requis.';
        }

        if ($numero === '') {
            $erreurs['numero'] = 'Le numéro de facture est requis.';
        }

        if ($date_emission === '') {
            $erreurs['date_emission'] = 'La date d’émission est requise.';
        }

        if ($montant_total === null || $montant_total === '' || !is_numeric($montant_total)) {
            $erreurs['montant_total'] = 'Le montant total doit être un nombre valide.';
        } else {
            $montant_total = (float) $montant_total;
        }

        if (empty($donnees['token_csrf']) || !verifier_token_csrf((string) $donnees['token_csrf'])) {
            $erreurs['token_csrf'] = 'Jeton CSRF invalide ou manquant.';
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'id_eleve' => $id_eleve,
                'numero' => $numero,
                'date_emission' => $date_emission,
                'montant_total' => $montant_total,
            ],
        ];
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);

        // Try to get facture from DAO first
        if ($this->dao instanceof FinanceDAO) {
            $f = $this->dao->getFacture($id);
            if (!empty($f)) {
                $facture = new Facture([
                    'id_facture' => $f['id_facture'] ?? $f['id'] ?? $id,
                    'id_eleve' => $f['id_eleve'] ?? 1,
                    'numero_sequentiel' => $f['numero_sequentiel'] ?? ($f['numero'] ?? 'FAC-000'),
                    'date_emission' => $f['date_emission'] ?? '',
                    'statut' => $f['statut'] ?? 'active',
                ]);
            }
        }

        if (!isset($facture)) {
            $facture = new Facture([
                'id_facture' => $id,
                'id_eleve' => 1,
                'numero_sequentiel' => 'FAC-2026-001',
                'date_emission' => '2026-09-01',
                'statut' => 'active',
            ]);
        }

        $facture->ajouter_ligne(new LigneFacture([
            'id_ligne_facture' => 1,
            'id_facture' => $id,
            'id_type_frais' => 1,
            'montant_ligne' => 120000.00,
        ]));
        $facture->ajouter_ligne(new LigneFacture([
            'id_ligne_facture' => 2,
            'id_facture' => $id,
            'id_type_frais' => 2,
            'montant_ligne' => 90000.00,
        ]));

        $facture->ajouter_remise(new Remise([
            'id_remise' => 1,
            'type_remise' => 'pourcentage',
            'valeur_remise' => 10.0,
            'motif' => 'Bourse sociale',
            'id_utilisateur_validation' => 1,
        ]));

        return [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'facture' => [
                'id' => $facture->get_id_facture(),
                'numero' => $facture->get_numero_sequentiel(),
                'date_emission' => $facture->get_date_emission(),
                'montant_total' => number_format($facture->get_montant_total(), 0, ',', ' '),
                'montant_net' => number_format($facture->calculer_montant_net(), 0, ',', ' '),
                'statut' => $facture->get_statut(),
                'lignes' => array_map(function (LigneFacture $ligne): array {
                    return [
                        'id' => $ligne->get_id_ligne_facture(),
                        'type_frais' => $ligne->get_id_type_frais(),
                        'montant' => number_format($ligne->get_montant_ligne(), 0, ',', ' '),
                    ];
                }, $facture->get_lignes()),
                'remises' => array_map(function (Remise $remise): array {
                    return [
                        'type' => $remise->get_type_remise(),
                        'valeur' => $remise->get_valeur_remise(),
                        'motif' => $remise->get_motif(),
                    ];
                }, $facture->get_remises()),
            ],
        ];
    }
}
