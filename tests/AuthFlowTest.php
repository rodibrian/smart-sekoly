<?php
session_start();
require_once __DIR__ . '/../classes/AuthService.class.php';

$auth = new AuthService();
$auth->deconnecter();
if ($auth->estConnecte()) {
    throw new RuntimeException('L’utilisateur ne devrait pas être connecté au départ.');
}

$auth->connecter([
    'id' => 1,
    'nom' => 'Admin',
    'email' => 'admin@smart-sekoly.test',
    'role' => 'admin',
]);

if (!$auth->estConnecte()) {
    throw new RuntimeException('La connexion a échoué.');
}
if (!$auth->aLaPermission('finance.read')) {
    throw new RuntimeException('La permission attendue n’a pas été accordée.');
}

$auth->deconnecter();
if ($auth->estConnecte()) {
    throw new RuntimeException('La déconnexion a échoué.');
}

echo "Auth flow tests: OK\n";
