<?php
/**
 * Contrôleur de la gestion financière.
 * Gère les factures, paiements, caisses, remises et rapports.
 */
class FinanceController
{
    private $module;
    private $action;
    private $parametre;
    private $dao;

    public function __construct($module = 'finance', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
        $this->dao = new FinanceDAO();
    }

    public function executer(): void
    {
        // Routeur des actions
        match ($this->action) {
            'factures' => $this->afficher_factures(),
            'facture-creer' => $this->creer_facture(),
            'facture-editer' => $this->editer_facture(),
            'facture-details' => $this->details_facture(),
            'paiements' => $this->afficher_paiements(),
            'paiement-enregistrer' => $this->enregistrer_paiement(),
            'caisses' => $this->afficher_caisses(),
            'caisse-creer' => $this->creer_caisse(),
            'remises' => $this->afficher_remises(),
            'remise-creer' => $this->creer_remise(),
            'rapports' => $this->afficher_rapports(),
            'impayés' => $this->afficher_impayés(),
            'reçu' => $this->afficher_recu(),
            'types-frais' => $this->gerer_types_frais(),
            default => $this->afficher_accueil(),
        };
    }

    private function afficher_accueil(): void
    {
        $factures = $this->recuperer_items('factures');
        $paiementsBruts = $this->recuperer_items('paiements');
        $paiements = $this->enrichirPaiements($paiementsBruts);

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'stats' => [
                'total_factures' => count($factures),
                'total_paiements' => count($paiements),
                'montant_collecte' => $this->calculer_montant_total($paiements),
                'montant_impayé' => $this->calculer_montant_impayé($factures, $paiements),
            ],
            'dernieres_factures' => array_slice($factures, -3),
            'derniers_paiements' => array_slice($paiements, -3),
        ];

        require $this->templatePath() . 'finance/index.view.php';
    }

    private function afficher_factures(): void
    {
        // Traiter les POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_facture();
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'factures' => $this->recuperer_items('factures'),
        ];

        require $this->templatePath() . 'finance/factures.view.php';
    }

    private function creer_facture(): void
    {
        // Traiter les POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer') {
            $this->traiter_creation_facture();
            return;
        }

        // Récupérer les types de frais depuis la DB (réel, pas codé en dur)
        $typeFraisDAO = new TypeFraisDAO();
        $types_db = $typeFraisDAO->lister();
        // Reformater pour compatibilité front (ancien schema avec 'nom_type')
        $types_frais = array_map(function ($type) {
            return [
                'id_type_frais' => $type['id_type_frais'],
                'nom_type' => $type['libelle'],  // Ancienne colonne devient nom_type
                'montant_default' => $type['montant_defaut'],
            ];
        }, $types_db);

        $eleves = [];
        $eleveDao = new EleveDAO();
        $dbEleves = $eleveDao->listerEleves();
        if (!empty($dbEleves)) {
            $eleves = array_map(function ($eleve) {
                return [
                    'id_eleve' => $eleve['id'] ?? $eleve['id_eleve'] ?? null,
                    'nom' => $eleve['nom'] ?? '',
                    'prenom' => $eleve['prenom'] ?? '',
                    'matricule' => $eleve['matricule'] ?? '',
                ];
            }, $dbEleves);
        } elseif (!empty($_SESSION['eleves'])) {
            $eleves = $_SESSION['eleves'];
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'facture-creer',
            'token_csrf' => generer_token_csrf(),
            'eleves' => $eleves,
            'types_frais' => $types_frais,
        ];

        require $this->templatePath() . 'finance/facture-creer.view.php';
    }

    private function editer_facture(): void
    {
        $id = (int) $this->parametre;
        $facture = $this->dao->getById('facture', $id);

        if (!$facture) {
            echo "Facture introuvable.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editer') {
            $this->traiter_edition_facture($id);
            return;
        }

        $typeFraisDAO = new TypeFraisDAO();
        $typesFrais = $typeFraisDAO->lister();

        $donnees = [
            'module' => $this->module,
            'action' => 'facture-editer',
            'token_csrf' => generer_token_csrf(),
            'facture' => $facture,
            'types_frais' => $typesFrais,
        ];

        require $this->templatePath() . 'finance/facture-editer.view.php';
    }

    private function details_facture(): void
    {
        $id = (int) $this->parametre;
        $facture = $this->dao->getById('facture', $id);

        if (!$facture) {
            echo "Facture introuvable.";
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'facture-details',
            'token_csrf' => generer_token_csrf(),
            'facture' => $facture,
        ];

        require $this->templatePath() . 'finance/facture-details.view.php';
    }

    private function afficher_paiements(): void
    {
        $paiementsBruts = $this->dao->all('paiements');

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'paiements' => $this->enrichirPaiements($paiementsBruts),
        ];

        require $this->templatePath() . 'finance/paiements.view.php';
    }

    private function enregistrer_paiement(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enregistrer') {
            $this->traiter_post_paiement();
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'paiement-enregistrer',
            'token_csrf' => generer_token_csrf(),
            'echeances' => $this->recuperer_items('echeances'),
            'methodes_paiement' => [
                'espece' => 'Espèces',
                'banque' => 'Banque',
                'mobile_money' => 'Mobile money',
            ],
            'date_paiement' => date('Y-m-d\TH:i'),
            'numero_recu' => $this->genererNumeroRecu(),
        ];

        require $this->templatePath() . 'finance/paiement-enregistrer.view.php';
    }

    private function afficher_caisses(): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'caisses' => $this->dao->all('caisses'),
        ];

        require $this->templatePath() . 'finance/caisses.view.php';
    }

    private function creer_caisse(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer') {
            $this->traiter_creation_caisse();
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'caisse-creer',
            'token_csrf' => generer_token_csrf(),
        ];

        require $this->templatePath() . 'finance/caisse-creer.view.php';
    }

    private function afficher_remises(): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'remises' => $this->dao->all('remises'),
        ];

        require $this->templatePath() . 'finance/remises.view.php';
    }

    private function creer_remise(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'creer') {
            $this->traiter_creation_remise();
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'remise-creer',
            'token_csrf' => generer_token_csrf(),
            'factures' => $this->recuperer_items('factures'),
            'eleves' => $_SESSION['eleves'] ?? [],
        ];

        require $this->templatePath() . 'finance/remise-creer.view.php';
    }

    private function afficher_rapports(): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'rapports' => $this->generer_rapports(),
        ];

        require $this->templatePath() . 'finance/rapports.view.php';
    }

    private function afficher_impayés(): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'impayés' => $this->calculer_impayés(),
        ];

        require $this->templatePath() . 'finance/impayés.view.php';
    }

    private function afficher_recu(): void
    {
        $id_paiement = (int) $this->parametre;
        $paiement = $this->dao->getById('paiement', $id_paiement);

        if (!$paiement) {
            echo "Paiement introuvable.";
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'reçu',
            'token_csrf' => generer_token_csrf(),
            'paiement' => $this->enrichirPaiement($paiement),
        ];

        require $this->templatePath() . 'finance/recu.view.php';
    }

    // === TRAITEMENTS POST ===

    private function traiter_post_facture(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        // Exemple : annuler une facture existante
        if (isset($_POST['supprimer']) && isset($_POST['id_facture'])) {
            $factureDao = new FactureDAO();
            $idFacture = (int) $_POST['id_facture'];
            $factureDao->annuler($idFacture, $_SESSION['auth_utilisateur']['id'] ?? 1);
        }
    }

    private function traiter_creation_facture(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $this->dao->insertFacture([
            'id_eleve' => $_POST['id_eleve'] ?? null,
            'numero' => $_POST['numero'] ?? '',
            'date_emission' => date('Y-m-d'),
            'montant_total' => (float) ($_POST['montant_total'] ?? 0),
            'statut' => 'active',
        ]);
    }

    private function traiter_edition_facture($id): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $montant_total = (float) ($_POST['montant_total'] ?? 0);
        $statut = $_POST['statut'] ?? 'active';
        if ($statut === 'cancelled') {
            $statut = 'annulee';
        }

        $factureDao = new FactureDAO();
        if (!$factureDao->mettreAJourFacture($id, [
            'montant_total' => $montant_total,
            'statut' => $statut,
        ])) {
            $_SESSION['factures'] = $_SESSION['factures'] ?? [];

            foreach ($_SESSION['factures'] as &$f) {
                if ($f['id_facture'] === $id) {
                    $f['montant_total'] = $montant_total;
                    $f['statut'] = $statut;
                    break;
                }
            }
        }
    }

    private function traiter_post_paiement(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $idEcheance = (int) ($_POST['id_echeance'] ?? 0);
        $montant = (float) ($_POST['montant'] ?? 0);
        $datePaiement = $_POST['date_paiement'] ?? date('Y-m-d H:i:s');
        $modePaiement = nettoyer_chaine($_POST['mode_paiement'] ?? 'espece');
        $numeroRecu = nettoyer_chaine($_POST['numero_recu'] ?? $this->genererNumeroRecu());

        if ($idEcheance <= 0 || $montant <= 0 || !in_array($modePaiement, ['espece', 'banque', 'mobile_money'], true)) {
            return;
        }

        $paiementId = $this->dao->insertPaiement([
            'id_echeance' => $idEcheance,
            'numero_recu' => $numeroRecu,
            'date_paiement' => $datePaiement,
            'montant' => $montant,
            'mode_paiement' => $modePaiement,
            'id_utilisateur_enregistrement' => $_SESSION['auth_utilisateur']['id'] ?? 1,
            'id_caisse' => $this->dao->getOrCreateCaisseDuJourId() ?? $this->dao->getDerniereCaisseId(),
            'statut' => 'actif',
        ]);

        if ($paiementId) {
            $echeancierDAO = new EcheancierDAO();
            $echeancierDAO->appliquerPaiementAEcheance($idEcheance, $montant);
            header('Location: ?module=finance&action=reçu&parametre=' . $paiementId);
            exit;
        }
    }

    private function traiter_creation_caisse(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $this->dao->insertCaisse([
            'date_caisse' => $_POST['date_caisse'] ?? date('Y-m-d'),
            'fond_de_caisse' => (float) ($_POST['fond_de_caisse'] ?? 0),
        ]);
    }

    private function traiter_creation_remise(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $this->dao->insertRemise([
            'type_remise' => $_POST['type_remise'] ?? 'pourcentage',
            'valeur_remise' => (float) ($_POST['valeur_remise'] ?? 0),
            'motif' => $_POST['motif'] ?? '',
            'id_utilisateur_validation' => $_SESSION['auth_utilisateur']['id'] ?? 1,
        ]);
    }

    // === UTILITAIRES ===

    private function recuperer_items(string $tableAlias): array
    {
        return $this->dao->all($tableAlias);
    }

    private function recuperer_echeances(): array
    {
        return $this->dao->all('echeances');
    }

    private function genererNumeroRecu(): string
    {
        try {
            $anneeId = $this->getActiveSchoolYearId();
            if ($anneeId !== null) {
                $sequence = SequenceNumerotation::getNext('recu', $anneeId, 'REC-{ANNEE}-{NUMERO_SEQUENTIEL}');
                return $sequence['formatte'] ?? '';
            }
        } catch (Throwable $e) {
            error_log('FinanceController::genererNumeroRecu() : ' . $e->getMessage());
        }

        return 'REC-' . date('YmdHis');
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

    private function enrichirPaiement(array $paiement): array
    {
        $echeanceId = (int) ($paiement['id_echeance'] ?? $paiement['id'] ?? 0);
        $idFacture = null;

        if ($echeanceId > 0) {
            $echeanceDAO = new EcheancierDAO();
            $echeance = $echeanceDAO->trouverParId($echeanceId);
            $idFacture = $echeance['id_facture'] ?? null;
        }

        $montant = $paiement['montant'] ?? $paiement['montant_paye'] ?? 0;

        return [
            'id_paiement' => $paiement['id_paiement'] ?? $paiement['id'] ?? null,
            'id_facture' => $idFacture,
            'id_echeance' => $echeanceId,
            'numero_recu' => $paiement['numero_recu'] ?? $paiement['recu'] ?? '',
            'date_paiement' => $paiement['date_paiement'] ?? $paiement['date'] ?? '',
            'montant' => $montant,
            'montant_paye' => $montant,
            'mode_paiement' => $paiement['mode_paiement'] ?? $paiement['methode_paiement'] ?? '',
            'statut' => $paiement['statut'] ?? '',
            'reference' => $paiement['reference'] ?? '',
        ];
    }

    private function enrichirPaiements(array $paiements): array
    {
        return array_map([$this, 'enrichirPaiement'], $paiements);
    }

    private function calculer_montant_total(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            if (isset($item['montant_paye'])) {
                $total += (float) $item['montant_paye'];
            } elseif (isset($item['montant'])) {
                $total += (float) $item['montant'];
            } elseif (isset($item['montant_total'])) {
                $total += (float) $item['montant_total'];
            }
        }

        return $total;
    }

    private function calculer_montant_impayé(array $factures, array $paiements): float
    {
        $montant_total_factures = 0;
        $montant_paye = 0;

        foreach ($factures as $f) {
            if (($f['statut'] ?? '') === 'active') {
                $montant_total_factures += (float) ($f['montant_total'] ?? 0);
            }
        }

        foreach ($paiements as $p) {
            $montant_paye += (float) ($p['montant_paye'] ?? $p['montant'] ?? 0);
        }

        return max(0, $montant_total_factures - $montant_paye);
    }

    private function calculer_impayés(): array
    {
        $factures = $this->recuperer_items('factures');
        $paiements = $this->recuperer_items('paiements');
        $echeances = $this->recuperer_items('echeances');

        $echeanceVersFacture = [];
        foreach ($echeances as $echeance) {
            if (!empty($echeance['id_echeance']) && !empty($echeance['id_facture'])) {
                $echeanceVersFacture[(int) $echeance['id_echeance']] = (int) $echeance['id_facture'];
            }
        }

        $factures_payees = [];
        foreach ($paiements as $p) {
            $factureId = (int) ($p['id_facture'] ?? $echeanceVersFacture[(int) ($p['id_echeance'] ?? 0)] ?? 0);
            if ($factureId > 0) {
                $factures_payees[$factureId] = true;
            }
        }

        $impayés = [];
        foreach ($factures as $f) {
            $idFacture = (int) ($f['id_facture'] ?? 0);
            if ($idFacture > 0 && !isset($factures_payees[$idFacture]) && ($f['statut'] ?? '') === 'active') {
                $impayés[] = $f;
            }
        }

        return $impayés;
    }

    private function generer_rapports(): array
    {
        $factures = $this->recuperer_items('factures');
        $paiements = $this->recuperer_items('paiements');

        return [
            [
                'periode' => 'Juillet 2026',
                'montant_factures' => $this->calculer_montant_total($factures),
                'montant_paiements' => $this->calculer_montant_total($paiements),
                'montant_impayé' => $this->calculer_montant_impayé($factures, $paiements),
            ],
        ];
    }

    /**
     * Gérer les types de frais paramétrables.
     * Routes: finance/types-frais?action=lister | action=creer_type
     */
    private function gerer_types_frais(): void
    {
        $dao = new TypeFraisDAO();
        $action = $_GET['action'] ?? 'lister';

        if ($action === 'creer_type' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Créer un nouveau type de frais
            $libelle = nettoyer_chaine($_POST['libelle'] ?? '');
            $montant = max(0, (float) ($_POST['montant_defaut'] ?? 0));

            if (empty($libelle)) {
                $_SESSION['erreur'] = 'Le libellé est obligatoire.';
            } else {
                $id = $dao->creer([
                    'libelle' => $libelle,
                    'montant_defaut' => $montant,
                ]);

                if ($id) {
                    $_SESSION['succes'] = "Type de frais « $libelle » créé avec succès.";
                    // Redirect vers lister (GET)
                    if (PHP_SAPI !== 'cli') {
                        header('Location: /smart-sekoly/finance/types-frais?action=lister');
                        exit;
                    }
                } else {
                    $_SESSION['erreur'] = 'Erreur lors de la création du type de frais.';
                }
            }
        }

        // Afficher la liste
        $donnees = [
            'module' => $this->module,
            'action' => 'types-frais',
            'token_csrf' => generer_token_csrf(),
            'types_frais' => $dao->lister(),
            'succes' => $_SESSION['succes'] ?? null,
            'erreur' => $_SESSION['erreur'] ?? null,
        ];

        unset($_SESSION['succes'], $_SESSION['erreur']);

        require $this->templatePath() . 'finance/types-frais.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }
}
