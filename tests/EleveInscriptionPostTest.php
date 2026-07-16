<?php
/**
 * Test d'intégration du formulaire d'inscription élève via serveur PHP intégré.
 */
declare(strict_types=1);

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$host = '127.0.0.1';
$port = 8123;
$baseUrl = 'http://' . $host . ':' . $port;
$cookieFile = $rootPath . 'tests' . DIRECTORY_SEPARATOR . 'eleve-cookie.txt';
@unlink($cookieFile);

$command = escapeshellcmd(PHP_BINARY) . ' -S ' . $host . ':' . $port . ' -t ' . escapeshellarg($rootPath) . ' ' . escapeshellarg($rootPath . 'index.php');
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];
$process = proc_open($command, $descriptors, $pipes, $rootPath);
if (!is_resource($process)) {
    throw new RuntimeException('Impossible de démarrer le serveur PHP intégré.');
}

$ready = false;
for ($i = 0; $i < 20; $i++) {
    $result = shell_exec('curl.exe -s -o NUL -w "%{http_code}" -X POST ' . escapeshellarg($baseUrl . '/eleves/inscription') . ' -d "nom=Nguyen&prenom=Mina&email=mina%40example.com&date_naissance=2012-04-01&matricule=EL-TEST-001&csrf_token=test"');
    if (is_string($result) && str_contains(trim($result), '302')) {
        $ready = true;
        break;
    }
    usleep(250000);
}

if (!$ready) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('Le serveur PHP intégré n’est pas prêt.');
}

$submitCommand = 'curl.exe -s -o NUL -D - -c ' . escapeshellarg($cookieFile) . ' -b ' . escapeshellarg($cookieFile) . ' -X POST ' . escapeshellarg($baseUrl . '/eleves/inscription') . ' -d "nom=Nguyen&prenom=Mina&email=mina%40example.com&date_naissance=2012-04-01&matricule=EL-TEST-001&csrf_token=test"';
$submitResult = shell_exec($submitCommand);
if (!is_string($submitResult) || !str_contains($submitResult, 'Location: /smart-sekoly/eleves/dossier/1')) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('L’inscription ne redirige pas vers le dossier élève.');
}

$checkCommand = 'curl.exe -s -c ' . escapeshellarg($cookieFile) . ' -b ' . escapeshellarg($cookieFile) . ' ' . escapeshellarg($baseUrl . '/eleves/dossier/1');
$checkResult = shell_exec($checkCommand);
if (!is_string($checkResult) || !str_contains($checkResult, 'Mina') || !str_contains($checkResult, 'Nguyen')) {
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('Le dossier élève ne reflète pas les données sauvegardées.');
}

proc_terminate($process);
proc_close($process);

echo "Eleve inscription POST test: OK\n";
