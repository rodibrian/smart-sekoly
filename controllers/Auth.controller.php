<?php

class AuthController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'auth', $action = 'login', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($this->action === 'logout') {
            $this->deconnecter();
            header('Location: ' . BASE_URL . '/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterConnexion();
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'erreurs' => [],
            'valeurs' => [
                'identifiant' => '',
            ],
        ];

        require TEMPLATES_PATH . 'auth/login.view.php';
    }

    private function traiterConnexion(): void
    {
        $donnees = [
            'identifiant' => nettoyer_chaine($_POST['identifiant'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
            'csrf_token' => $_POST['csrf_token'] ?? '',
        ];

        $erreurs = [];

        if (empty($donnees['csrf_token']) || !verifier_token_csrf($donnees['csrf_token'])) {
            $erreurs[] = 'Jeton CSRF invalide.';
        }

        if ($donnees['identifiant'] === '') {
            $erreurs[] = 'L’identifiant est requis.';
        }

        if ($donnees['mot_de_passe'] === '') {
            $erreurs[] = 'Le mot de passe est requis.';
        }

        if (!empty($erreurs)) {
            $this->afficherFormulaire($donnees, $erreurs);
            return;
        }

        $utilisateurDao = new UtilisateurDAO();
        $utilisateur = $utilisateurDao->trouverParIdentifiant($donnees['identifiant']);

        if ($utilisateur === null) {
            $erreurs[] = 'Identifiant ou mot de passe invalide.';
            $this->afficherFormulaire($donnees, $erreurs);
            return;
        }

        if (($utilisateur['statut_compte'] ?? 'actif') === 'verrouille') {
            $erreurs[] = 'Ce compte est verrouillé après trop de tentatives échouées.';
            $this->afficherFormulaire($donnees, $erreurs);
            return;
        }

        $utilisateurModele = new Utilisateur([
            'mot_de_passe_hash' => $utilisateur['mot_de_passe_hash'],
        ]);

        if (!$utilisateurModele->verifierMotDePasse($donnees['mot_de_passe'])) {
            $tentatives = (int) ($utilisateur['nombre_essais_echoues'] ?? 0) + 1;
            $statut = $tentatives >= 5 ? 'verrouille' : null;
            $utilisateurDao->mettreAJourTentatives((int) $utilisateur['id_utilisateur'], $tentatives, $statut);
            $erreurs[] = 'Identifiant ou mot de passe invalide.';
            $this->afficherFormulaire($donnees, $erreurs);
            return;
        }

        $utilisateurDao->reinitialiserEssais((int) $utilisateur['id_utilisateur']);
        $utilisateurDao->mettreAJourDerniereConnexion((int) $utilisateur['id_utilisateur']);

        $authService = new AuthService();
        $authService->connecter([
            'id' => (int) $utilisateur['id_utilisateur'],
            'nom' => $utilisateur['identifiant'],
            'email' => $utilisateur['identifiant'],
            'role' => $utilisateur['role'] ?? 'admin',
        ]);

        header('Location: ' . BASE_URL . '/tableau-de-bord');
    }

    private function afficherFormulaire(array $valeurs, array $erreurs): void
    {
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'erreurs' => $erreurs,
            'valeurs' => [
                'identifiant' => $valeurs['identifiant'] ?? '',
            ],
        ];

        require TEMPLATES_PATH . 'auth/login.view.php';
    }

    private function deconnecter(): void
    {
        $authService = new AuthService();
        $authService->deconnecter();
    }
}
