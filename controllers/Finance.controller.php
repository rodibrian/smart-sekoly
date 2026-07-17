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
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'stats' => [
                'total_factures' => count($_SESSION['factures'] ?? []),
                'total_paiements' => count($_SESSION['paiements'] ?? []),
                'montant_collecte' => $this->calculer_montant_total('paiements'),
                'montant_impayé' => $this->calculer_montant_impayé(),
            ],
            'dernieres_factures' => array_slice($_SESSION['factures'] ?? [], -3),
            'derniers_paiements' => array_slice($_SESSION['paiements'] ?? [], -3),
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
            'factures' => $_SESSION['factures'] ?? [],
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

        $donnees = [
            'module' => $this->module,
            'action' => 'facture-creer',
            'token_csrf' => generer_token_csrf(),
            'eleves' => $_SESSION['eleves'] ?? [],
            'types_frais' => $types_frais,
        ];

        require $this->templatePath() . 'finance/facture-creer.view.php';
    }

    private function editer_facture(): void
    {
        $id = $this->parametre;
        $factures = $_SESSION['factures'] ?? [];
        $facture = null;

        foreach ($factures as $f) {
            if ($f['id_facture'] === $id) {
                $facture = $f;
                break;
            }
        }

        if (!$facture) {
            echo "Facture introuvable.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editer') {
            $this->traiter_edition_facture($id);
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'facture-editer',
            'token_csrf' => generer_token_csrf(),
            'facture' => $facture,
            'types_frais' => $_SESSION['types_frais'] ?? [],
        ];

        require $this->templatePath() . 'finance/facture-editer.view.php';
    }

    private function details_facture(): void
    {
        $id = $this->parametre;
        $factures = $_SESSION['factures'] ?? [];
        $facture = null;

        foreach ($factures as $f) {
            if ($f['id_facture'] === $id) {
                $facture = $f;
                break;
            }
        }

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
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'paiements' => $_SESSION['paiements'] ?? [],
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
            'factures' => $_SESSION['factures'] ?? [],
            'methodes_paiement' => ['Espèces', 'Chèque', 'Virement', 'Carte bancaire'],
        ];

        require $this->templatePath() . 'finance/paiement-enregistrer.view.php';
    }

    private function afficher_caisses(): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'caisses' => $_SESSION['caisses'] ?? [],
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
            'remises' => $_SESSION['remises'] ?? [],
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
            'factures' => $_SESSION['factures'] ?? [],
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
        $id_paiement = $this->parametre;
        $paiements = $_SESSION['paiements'] ?? [];
        $paiement = null;

        foreach ($paiements as $p) {
            if ($p['id_paiement'] === $id_paiement) {
                $paiement = $p;
                break;
            }
        }

        if (!$paiement) {
            echo "Paiement introuvable.";
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => 'reçu',
            'token_csrf' => generer_token_csrf(),
            'paiement' => $paiement,
        ];

        require $this->templatePath() . 'finance/recu.view.php';
    }

    // === TRAITEMENTS POST ===

    private function traiter_post_facture(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $_SESSION['factures'] = $_SESSION['factures'] ?? [];

        // Exemple : supprimer une facture
        if (isset($_POST['supprimer']) && isset($_POST['id_facture'])) {
            $_SESSION['factures'] = array_filter($_SESSION['factures'], function ($f) {
                return $f['id_facture'] !== $_POST['id_facture'];
            });
        }
    }

    private function traiter_creation_facture(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $_SESSION['factures'] = $_SESSION['factures'] ?? [];

        $facture = [
            'id_facture' => 'FACT-' . count($_SESSION['factures']) + 1,
            'id_eleve' => $_POST['id_eleve'] ?? '',
            'numero_sequentiel' => count($_SESSION['factures']) + 1,
            'date_emission' => date('Y-m-d'),
            'montant_total' => (float) ($_POST['montant_total'] ?? 0),
            'statut' => 'active',
            'lignes' => [],
        ];

        // Ajouter les lignes de facture
        if (isset($_POST['type_frais']) && is_array($_POST['type_frais'])) {
            foreach ($_POST['type_frais'] as $idx => $type) {
                if (!empty($type)) {
                    $facture['lignes'][] = [
                        'type_frais' => $type,
                        'montant' => (float) ($_POST['montant_ligne'][$idx] ?? 0),
                        'quantite' => (int) ($_POST['quantite'][$idx] ?? 1),
                    ];
                }
            }
        }

        $_SESSION['factures'][] = $facture;
    }

    private function traiter_edition_facture($id): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $_SESSION['factures'] = $_SESSION['factures'] ?? [];

        foreach ($_SESSION['factures'] as &$f) {
            if ($f['id_facture'] === $id) {
                $f['montant_total'] = (float) ($_POST['montant_total'] ?? $f['montant_total']);
                $f['statut'] = $_POST['statut'] ?? $f['statut'];
                break;
            }
        }
    }

    private function traiter_post_paiement(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $_SESSION['paiements'] = $_SESSION['paiements'] ?? [];

        $paiement = [
            'id_paiement' => 'PAY-' . count($_SESSION['paiements']) + 1,
            'id_facture' => $_POST['id_facture'] ?? '',
            'montant_paye' => (float) ($_POST['montant_paye'] ?? 0),
            'methode_paiement' => $_POST['methode_paiement'] ?? 'Espèces',
            'date_paiement' => date('Y-m-d'),
            'reference' => $_POST['reference'] ?? '',
        ];

        $_SESSION['paiements'][] = $paiement;

        // Rediriger vers le reçu
        header('Location: ?module=finance&action=reçu&parametre=' . $paiement['id_paiement']);
        exit;
    }

    private function traiter_creation_caisse(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $_SESSION['caisses'] = $_SESSION['caisses'] ?? [];

        $caisse = [
            'id_caisse' => 'CAISSE-' . count($_SESSION['caisses']) + 1,
            'nom_caisse' => $_POST['nom_caisse'] ?? '',
            'solde_initial' => (float) ($_POST['solde_initial'] ?? 0),
            'solde_actuel' => (float) ($_POST['solde_initial'] ?? 0),
            'date_creation' => date('Y-m-d'),
        ];

        $_SESSION['caisses'][] = $caisse;
    }

    private function traiter_creation_remise(): void
    {
        if (!isset($_POST['token_csrf']) || $_POST['token_csrf'] !== ($_SESSION['token_csrf'] ?? '')) {
            return;
        }

        $_SESSION['remises'] = $_SESSION['remises'] ?? [];

        $remise = [
            'id_remise' => 'REM-' . count($_SESSION['remises']) + 1,
            'id_facture' => $_POST['id_facture'] ?? '',
            'pourcentage' => (float) ($_POST['pourcentage'] ?? 0),
            'motif' => $_POST['motif'] ?? '',
            'date_application' => date('Y-m-d'),
        ];

        $_SESSION['remises'][] = $remise;
    }

    // === UTILITAIRES ===

    private function calculer_montant_total($key): float
    {
        $items = $_SESSION[$key] ?? [];
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

    private function calculer_montant_impayé(): float
    {
        $factures = $_SESSION['factures'] ?? [];
        $paiements = $_SESSION['paiements'] ?? [];
        $montant_total_factures = 0;
        $montant_paye = 0;

        foreach ($factures as $f) {
            if ($f['statut'] === 'active') {
                $montant_total_factures += (float) ($f['montant_total'] ?? 0);
            }
        }

        foreach ($paiements as $p) {
            $montant_paye += (float) ($p['montant_paye'] ?? 0);
        }

        return max(0, $montant_total_factures - $montant_paye);
    }

    private function calculer_impayés(): array
    {
        $factures = $_SESSION['factures'] ?? [];
        $paiements = $_SESSION['paiements'] ?? [];
        $factures_payees = [];

        foreach ($paiements as $p) {
            $factures_payees[$p['id_facture']] = true;
        }

        $impayés = [];
        foreach ($factures as $f) {
            if (!isset($factures_payees[$f['id_facture']]) && $f['statut'] === 'active') {
                $impayés[] = $f;
            }
        }

        return $impayés;
    }

    private function generer_rapports(): array
    {
        return [
            [
                'periode' => 'Juillet 2026',
                'montant_factures' => $this->calculer_montant_total('factures'),
                'montant_paiements' => $this->calculer_montant_total('paiements'),
                'montant_impayé' => $this->calculer_montant_impayé(),
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
