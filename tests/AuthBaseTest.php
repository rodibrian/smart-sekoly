<?php
require_once __DIR__ . '/../classes/Utilisateur.class.php';
require_once __DIR__ . '/../classes/Role.class.php';
require_once __DIR__ . '/../classes/Permission.class.php';

$hash = Utilisateur::hacherMotDePasse('secret123');
$utilisateur = new Utilisateur([
    'nom' => 'Admin',
    'email' => 'admin@smart-sekoly.test',
    'mot_de_passe_hash' => $hash,
    'role' => 'admin',
]);

if (!$utilisateur->verifierMotDePasse('secret123')) {
    throw new RuntimeException('Le mot de passe ne vérifie pas.');
}

$role = new Role('admin', ['users.read', 'users.write']);
if (!$role->aLaPermission('users.read')) {
    throw new RuntimeException('La permission attendue est absente.');
}

echo "Auth base tests: OK\n";
