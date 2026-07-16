<?php
/**
 * Module Communication interne.
 * Gère la messagerie, les annonces et les événements de carnet.
 */

class CommunicationController
{
    private $module;
    private $action;

    public function __construct($module = 'communication', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
    }

    public function executer(): void
    {
        $action = $this->action ?? 'index';
        $data = match ($action) {
            'index' => $this->preparer_index(),
            'messages' => $this->preparer_messages(),
            'annonces' => $this->preparer_annonces(),
            default => $this->preparer_index(),
        };

        require $this->templatePath() . 'communication/' . $this->getNomVue($action) . '.view.php';
    }

    private function templatePath(): string
    {
        return defined('TEMPLATES_PATH') ? TEMPLATES_PATH : __DIR__ . '/../templates/';
    }

    private function getNomVue(string $action): string
    {
        return match ($action) {
            'messages' => 'messages',
            'annonces' => 'annonces',
            default => 'index',
        };
    }

    private function preparer_index(): array
    {
        $data = [
            'module' => $this->module,
            'action' => 'index',
            'token_csrf' => generer_token_csrf(),
            'stats' => [
                'messages' => count($_SESSION['communication']['messages'] ?? []),
                'annonces' => count($_SESSION['communication']['annonces'] ?? []),
                'evenements' => count($_SESSION['communication']['evenements'] ?? []),
            ]
        ];

        return $data;
    }

    private function preparer_messages(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_message();
        }

        $messages = $_SESSION['communication']['messages'] ?? [];

        return [
            'module' => $this->module,
            'action' => 'messages',
            'token_csrf' => generer_token_csrf(),
            'messages' => array_reverse($messages),
        ];
    }

    private function preparer_annonces(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiter_post_annonce();
        }

        $annonces = $_SESSION['communication']['annonces'] ?? [];

        return [
            'module' => $this->module,
            'action' => 'annonces',
            'token_csrf' => generer_token_csrf(),
            'annonces' => array_reverse($annonces),
        ];
    }

    private function traiter_post_message(): void
    {
        if (empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            return;
        }

        $destinataire = nettoyer_chaine($_POST['destinataire'] ?? '');
        $contenu = nettoyer_chaine($_POST['contenu'] ?? '');

        if ($destinataire === '' || $contenu === '') {
            return;
        }

        $message = new Message([
            'id' => generer_identifiant($_SESSION['communication']['messages'] ?? [], 'id'),
            'destinataire' => $destinataire,
            'contenu' => $contenu,
            'date' => date('d/m/Y H:i'),
        ]);

        $_SESSION['communication']['messages'][] = $message->toArray();
    }

    private function traiter_post_annonce(): void
    {
        if (empty($_POST['csrf_token']) || !verifier_token_csrf((string) $_POST['csrf_token'])) {
            return;
        }

        $titre = nettoyer_chaine($_POST['titre'] ?? '');
        $contenu = nettoyer_chaine($_POST['contenu'] ?? '');

        if ($titre === '' || $contenu === '') {
            return;
        }

        $annonce = new Annonce([
            'id' => generer_identifiant($_SESSION['communication']['annonces'] ?? [], 'id'),
            'titre' => $titre,
            'contenu' => $contenu,
            'date' => date('d/m/Y'),
        ]);

        $_SESSION['communication']['annonces'][] = $annonce->toArray();
    }
}
