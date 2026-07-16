<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../classes/PermissionDAO.class.php';
require_once __DIR__ . '/../classes/AuthService.class.php';

$dao = new PermissionDAO();

$id = $dao->creerPermission('test', 'module', 'lire');
if ($id <= 0) {
    throw new RuntimeException('Échec de la création de permission.');
}

$permission = $dao->trouverPermissionParId($id);
if ($permission === null || $permission['module'] !== 'test' || $permission['action'] !== 'lire') {
    throw new RuntimeException('La permission créée est incorrecte.');
}

if (!$dao->modifierPermission($id, 'test', 'module', 'modifier')) {
    throw new RuntimeException('Échec de la modification de permission.');
}

$permission = $dao->trouverPermissionParId($id);
if ($permission === null || $permission['action'] !== 'modifier') {
    throw new RuntimeException('La permission n’a pas été mise à jour.');
}

if (!$dao->supprimerPermission($id)) {
    throw new RuntimeException('Échec de la suppression de permission.');
}

if ($dao->trouverPermissionParId($id) !== null) {
    throw new RuntimeException('La permission devrait avoir été supprimée.');
}

$auth = new AuthService();
$auth->deconnecter();
$auth->connecter(['id' => 1, 'nom' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin']);
if (!$auth->aLaPermission('any.permission')) {
    throw new RuntimeException('L’admin devrait toujours avoir toutes les permissions.');
}

$auth->deconnecter();
$auth->connecter(['id' => 2, 'nom' => 'Enseignant', 'email' => 'enseignant@example.com', 'role' => 'enseignant']);
if (!$auth->aLaPermission('eleves.read')) {
    throw new RuntimeException('L’enseignant devrait avoir eleves.read.');
}
if ($auth->aLaPermission('finance.write')) {
    throw new RuntimeException('L’enseignant ne devrait pas avoir finance.write.');
}

echo "PermissionsCRUDTest: OK\n";
