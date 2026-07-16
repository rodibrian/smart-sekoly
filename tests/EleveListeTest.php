<?php
/**
 * Test d'intégration de la liste des élèves.
 */
declare(strict_types=1);

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$host = '127.0.0.1';
$port = 8124;
$baseUrl = 'http://' . $host . ':' . $port;
$cookieFile = $rootPath . 'tests' . DIRECTORY_SEPARATOR . 'eleve-list-cookie.txt';
@unlink($cookieFile);

$command = escapeshellcmd(PHP_BINARY) . ' -S ' . $host . ':' . $port . ' -t ' . escapeshellarg($rootPath) . ' ' . escapeshellarg($rootPath . 'index.php');
$descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
$process = proc_open($command, $descriptors, $pipes, $rootPath);
if (!is_resource($process)) {
    throw new RuntimeException('Impossible de démarrer le serveur PHP intégré.');
}

$payload = http_build_query([
    'nom' => 'Dupont',
    'prenom' => 'Alice',
    'email' => 'alice@example.com',
    'date_naissance' => '2011-09-14',
    'matricule' => 'EL-LIST-001',
    'csrf_token' => 'test',
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $payload,
        'ignore_errors' => true,
    ],
]);

$headers = [];
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $payload,
        'ignore_errors' => true,
        'follow_location' => 0,
    ],
]);

for ($i = 0; $i < 20; $i++) {
    $result = @file_get_contents($baseUrl . '/eleves/inscription', false, $context);
    $headers = $http_response_header ?? [];
    if (is_string($result) && !empty($headers)) {
        break;
    }
    usleep(250000);
}

$cookie = '';
foreach ($headers as $header) {
    if (preg_match('/Set-Cookie: ([^;]+)/i', $header, $matches)) {
        $cookie = $matches[1];
        break;
    }
}

if ($cookie === '') {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('Le serveur n’a pas retourné de cookie de session.');
}

$listeContext = stream_context_create([
    'http' => [
        'header' => "Cookie: {$cookie}\r\n",
        'ignore_errors' => true,
    ],
]);

$listeResult = @file_get_contents($baseUrl . '/eleves/liste?q=Dupont', false, $listeContext);
if (!is_string($listeResult) || !str_contains($listeResult, 'Dupont') || !str_contains($listeResult, 'Alice')) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('La liste des élèves ne rend pas les données attendues.');
}

proc_terminate($process);
proc_close($process);

echo "Eleve list test: OK\n";
