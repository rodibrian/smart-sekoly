<?php
/**
 * Test d'intégration de la page de suivi des documents obligatoires.
 */
declare(strict_types=1);

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$host = '127.0.0.1';
$port = 8129;
$baseUrl = 'http://' . $host . ':' . $port;

$command = escapeshellcmd(PHP_BINARY) . ' -S ' . $host . ':' . $port . ' -t ' . escapeshellarg($rootPath) . ' ' . escapeshellarg($rootPath . 'index.php');
$descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
$process = proc_open($command, $descriptors, $pipes, $rootPath);
if (!is_resource($process)) {
    throw new RuntimeException('Impossible de démarrer le serveur PHP intégré.');
}

$payload = http_build_query([
    'nom' => 'Rabe',
    'prenom' => 'Sitraka',
    'email' => 'sitraka@example.com',
    'date_naissance' => '2010-02-03',
    'matricule' => 'EL-DOC-001',
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

$result = @file_get_contents($baseUrl . '/eleves/documents/1');
if (!is_string($result) || !str_contains($result, 'Documents obligatoires') || !str_contains($result, 'CNI')) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('La page des documents obligatoires ne rend pas les informations attendues.');
}

proc_terminate($process);
proc_close($process);

echo "Eleve documents test: OK\n";
