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
                $insertedId = $this->enregistrer_facture($resultat['donnees']);
                if (!headers_sent() && $insertedId && PHP_SAPI !== 'cli' && php_sapi_name() !== 'cli') {
                    header('Location: ' . BASE_URL . '/factures/fiche/' . $insertedId);
                    exit;
                }

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
        $eleves = [
            ['id' => 1, 'nom' => 'Mira Rajaonarivony', 'matricule' => 'ELEVE-001'],
            ['id' => 2, 'nom' => 'Jean Rakoto', 'matricule' => 'ELEVE-002'],
        ];

        if ($this->dao instanceof FinanceDAO) {
            $eleveDao = new EleveDAO();
            $dbEleves = $eleveDao->listerEleves();
            if (!empty($dbEleves)) {
                $eleves = array_map(function ($eleve) {
                    return [
                        'id' => $eleve['id'] ?? $eleve['id_eleve'] ?? null,
                        'nom' => trim(($eleve['prenom'] ?? '') . ' ' . ($eleve['nom'] ?? '')),
                        'matricule' => $eleve['matricule'] ?? '',
                    ];
                }, $dbEleves);
            }
        }

        $donnees = array_merge([
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'eleves' => $eleves,
        ], $resultat);

        if (empty($donnees['donnees']['numero'])) {
            $anneeId = $this->getActiveSchoolYearId();
            if ($anneeId !== null) {
                try {
                    $sequence = SequenceNumerotation::getNext('facture', $anneeId);
                    $donnees['donnees']['numero'] = $sequence['formatte'];
                } catch (Throwable $e) {
                    // keep empty and let save-time generation handle it
                }
            }
        }

        return $donnees;
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
        return $this->dao->all('factures');
    }

    private function enregistrer_facture(array $donnees): int
    {
        if (empty($donnees['numero'])) {
            $anneeId = $this->getActiveSchoolYearId();
            if ($anneeId !== null) {
                $sequence = SequenceNumerotation::getNext('facture', $anneeId);
                $donnees['numero'] = $sequence['formatte'];
            }
        }

        if ($this->dao instanceof FinanceDAO) {
            return $this->dao->insertFacture([
                'id_eleve' => $donnees['id_eleve'],
                'numero' => $donnees['numero'],
                'date_emission' => $donnees['date_emission'],
                'montant_total' => $donnees['montant_total'],
                'statut' => 'active',
            ]);
        }

        if (!isset($_SESSION['factures']) || !is_array($_SESSION['factures'])) {
            $_SESSION['factures'] = [];
        }

        $id = generer_identifiant($_SESSION['factures'], 'id_facture');
        $_SESSION['factures'][] = [
            'id_facture' => $id,
            'id_eleve' => $donnees['id_eleve'],
            'numero_sequentiel' => $donnees['numero'],
            'date_emission' => $donnees['date_emission'],
            'montant_total' => $donnees['montant_total'],
            'statut' => 'active',
        ];

        return $id;
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

    private function getActiveSchoolYearId(): ?int
    {
        $pdo = get_connexion_base_donnees();
        if (!$pdo instanceof PDO) {
            return null;
        }

        $stmt = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE etat = 'active' LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row !== false ? (int) $row['id_annee'] : null;
    }

    private function preparer_fiche(): array
    {
        $id = (int) ($this->parametre ?? 1);
        $factureDao = new FactureDAO();
        $data = $factureDao->trouverParId($id);

        if (!empty($data)) {
            $facture = new Facture([
                'id_facture' => $data['id_facture'] ?? $id,
                'id_eleve' => $data['id_eleve'] ?? 1,
                'numero_sequentiel' => $data['numero_sequentiel'] ?? 'FAC-000',
                'date_emission' => $data['date_emission'] ?? '',
                'statut' => $data['statut'] ?? 'active',
            ]);

            foreach ($data['lignes'] ?? [] as $ligne) {
                $facture->ajouter_ligne(new LigneFacture([
                    'id_ligne_facture' => $ligne['id_ligne_facture'] ?? 0,
                    'id_facture' => $ligne['id_facture'] ?? $id,
                    'id_type_frais' => $ligne['id_type_frais'] ?? 0,
                    'montant_ligne' => $ligne['montant_ligne'] ?? 0.0,
                ]));
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
        }

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
