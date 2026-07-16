<?php
/**
 * Test d'intégration du dossier élève.
 */
declare(strict_types=1);

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$host = '127.0.0.1';
$port = 8126;
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
    'matricule' => 'EL-DOSSIER-001',
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

$result = @file_get_contents($baseUrl . '/eleves/inscription', false, $context);
if (!is_string($result) || empty($http_response_header)) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('L’inscription POST n’a pas retourné de réponse.');
}

$dossierResult = @file_get_contents($baseUrl . '/eleves/dossier/1');
if (!is_string($dossierResult) || !str_contains($dossierResult, 'Dossier élève') || !str_contains($dossierResult, 'Matricule')) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('Le dossier élève ne contient pas les informations attendues.');
}

proc_terminate($process);
proc_close($process);

echo "Eleve dossier test: OK\n";
