<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/AuthService.class.php';
require_once __DIR__ . '/../classes/UtilisateurDAO.class.php';
require_once __DIR__ . '/../classes/Utilisateur.class.php';

$_SESSION = [];

$utilisateurDao = new UtilisateurDAO();
$utilisateur = $utilisateurDao->trouverParIdentifiant('admin');
if ($utilisateur === null) {
    throw new RuntimeException('L’utilisateur admin n’a pas été initialisé.');
}

$utilisateurModele = new Utilisateur(['mot_de_passe_hash' => $utilisateur['mot_de_passe_hash']]);
if (!$utilisateurModele->verifierMotDePasse('admin')) {
    throw new RuntimeException('Le mot de passe admin par défaut ne correspond pas.');
}

$auth = new AuthService();
$auth->connecter([
    'id' => (int) $utilisateur['id_utilisateur'],
    'nom' => $utilisateur['identifiant'],
    'email' => $utilisateur['identifiant'],
    'role' => $utilisateur['role'] ?? 'admin',
]);

if (!$auth->estConnecte()) {
    throw new RuntimeException('L’utilisateur ne s’est pas connecté.');
}

$auth->deconnecter();
if ($auth->estConnecte()) {
    throw new RuntimeException('La déconnexion a échoué.');
}

echo "AuthLoginTest: OK\n";
