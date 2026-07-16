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
