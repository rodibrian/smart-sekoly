<?php
/**
 * Test d'intégration de l'édition d'un dossier élève.
 */
declare(strict_types=1);

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$host = '127.0.0.1';
$port = 8128;
$baseUrl = 'http://' . $host . ':' . $port;

$command = escapeshellcmd(PHP_BINARY) . ' -S ' . $host . ':' . $port . ' -t ' . escapeshellarg($rootPath) . ' ' . escapeshellarg($rootPath . 'index.php');
$descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
$process = proc_open($command, $descriptors, $pipes, $rootPath);
if (!is_resource($process)) {
    throw new RuntimeException('Impossible de démarrer le serveur PHP intégré.');
}

$payload = http_build_query([
    'nom' => 'Rakoto',
    'prenom' => 'Miora',
    'email' => 'miora@example.com',
    'date_naissance' => '2009-11-12',
    'matricule' => 'EL-EDIT-001',
    'csrf_token' => 'test',
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $payload,
        'ignore_errors' => true,
        'follow_location' => 0,
    ],
]);

@file_get_contents($baseUrl . '/eleves/inscription', false, $context);

$editPayload = http_build_query([
    'nom' => 'Rakoto',
    'prenom' => 'Miora',
    'email' => 'miora.updated@example.com',
    'date_naissance' => '2009-11-12',
    'matricule' => 'EL-EDIT-001',
    'csrf_token' => 'test',
]);

$editContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $editPayload,
        'ignore_errors' => true,
        'follow_location' => 0,
    ],
]);

$editResult = @file_get_contents($baseUrl . '/eleves/edition/1', false, $editContext);
if (!is_string($editResult) || !str_contains($editResult, 'miora.updated@example.com')) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('L’édition du dossier élève ne s’affiche pas correctement.');
}

proc_terminate($process);
proc_close($process);

echo "Eleve edition test: OK\n";
