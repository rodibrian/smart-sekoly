<?php
/**
 * Module Bibliothèque documentaire.
 * Gère les documents administratifs et leur historique de versions.
 */

class BibliothequeController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'bibliotheque', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $action = $this->action ?? 'index';
        $data = match ($action) {
            'index' => $this->preparer_index(),
            'versions' => $this->preparer_versions(),
            'manuel' => $this->preparer_manuel(),
            'tutoriels' => $this->preparer_tutoriels(),
            default => $this->preparer_index(),
        };

        require $this->templatePath() . 'bibliotheque/' . $this->getNomVue($action) . '.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }

    private function getNomVue(string $action): string
    {
        return match ($action) {
            'versions' => 'versions',
            'manuel' => 'manuel',
            'tutoriels' => 'tutoriels',
            default => 'index',
        };
    }

    private function preparer_index(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_document();
        }

        $documents = $_SESSION['bibliotheque']['documents'] ?? [];

        return [
            'module' => $this->module,
            'action' => 'index',
            'token_csrf' => generer_token_csrf(),
            'documents' => array_reverse($documents),
        ];
    }

    private function preparer_versions(): array
    {
        $document_id = (int) ($_GET['id'] ?? $this->parametre ?? 0);
        $document = $this->trouver_document($document_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_version($document_id);
        }

        $versions = $_SESSION['bibliotheque']['versions'][$document_id] ?? [];

        return [
            'module' => $this->module,
            'action' => 'versions',
            'token_csrf' => generer_token_csrf(),
            'document' => $document,
            'versions' => array_reverse($versions),
        ];
    }

    private function preparer_manuel(): array
    {
        return [
            'module' => $this->module,
            'action' => 'manuel',
            'token_csrf' => generer_token_csrf(),
        ];
    }

    private function preparer_tutoriels(): array
    {
        return [
            'module' => $this->module,
            'action' => 'tutoriels',
            'token_csrf' => generer_token_csrf(),
            'tutoriels' => [
                [
                    'role' => 'Administrateur',
                    'introduction' => 'Accédez et gérez les documents administratifs de l’établissement.',
                    'etapes' => [
                        'Ajouter ou modifier des documents dans la bibliothèque documentaire.',
                        'Consulter l’historique des versions de chaque document.',
                        'Utiliser le manuel intégré pour comprendre les bonnes pratiques.',
                    ],
                ],
                [
                    'role' => 'Secrétaire',
                    'introduction' => 'Publiez et mettez à jour les notes de service et les circulaires.',
                    'etapes' => [
                        'Créer un document administratif avec un titre, une catégorie et une description.',
                        'Consulter les versions antérieures pour suivre l’historique des modifications.',
                        'Utiliser les tutoriels pour retrouver les opérations courantes.',
                    ],
                ],
                [
                    'role' => 'Responsable qualité',
                    'introduction' => 'Vérifiez les versions et maintenez la conformité documentaire.',
                    'etapes' => [
                        'Consulter les documents et leurs versions créés par le service.',
                        'Vérifier les commentaires de version pour comprendre les changements.',
                        'S’assurer que les documents importants sont bien archivés.',
                    ],
                ],
            ],
        ];
    }

    private function traiter_post_document(): void
    {
        if (empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            return;
        }

        $titre = nettoyer_chaine($_POST['titre'] ?? '');
        $categorie = nettoyer_chaine($_POST['categorie'] ?? '');
        $description = nettoyer_chaine($_POST['description'] ?? '');

        if ($titre === '' || $categorie === '') {
            return;
        }

        $document = new DocumentAdministratif([
            'id' => generer_identifiant($_SESSION['bibliotheque']['documents'] ?? [], 'id'),
            'titre' => $titre,
            'categorie' => $categorie,
            'description' => $description,
            'date_creation' => date('d/m/Y'),
        ]);

        $_SESSION['bibliotheque']['documents'][] = $document->toArray();
    }

    private function traiter_post_version(int $document_id): void
    {
        if ($document_id <= 0 || $this->trouver_document($document_id) === null) {
            return;
        }

        if (empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            return;
        }

        $auteur = nettoyer_chaine($_POST['auteur'] ?? '');
        $commentaire = nettoyer_chaine($_POST['commentaire'] ?? '');
        $contenu = nettoyer_chaine($_POST['contenu'] ?? '');

        if ($auteur === '' || $contenu === '') {
            return;
        }

        $version = new VersionDocument([
            'id' => generer_identifiant($_SESSION['bibliotheque']['versions'][$document_id] ?? [], 'id'),
            'document_id' => $document_id,
            'auteur' => $auteur,
            'commentaire' => $commentaire,
            'contenu' => $contenu,
            'date_version' => date('d/m/Y H:i'),
        ]);

        $_SESSION['bibliotheque']['versions'][$document_id][] = $version->toArray();
    }

    private function trouver_document(int $id): ?array
    {
        foreach ($_SESSION['bibliotheque']['documents'] ?? [] as $document) {
            if (($document['id'] ?? 0) === $id) {
                return $document;
            }
        }

        return null;
    }
}
