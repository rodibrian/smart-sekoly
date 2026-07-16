<?php
$routes = [
    '/smart-sekoly/communication/index' => 'Communication interne',
    '/smart-sekoly/communication/messages' => 'Messages internes',
    '/smart-sekoly/communication/annonces' => 'Annonces scolaires',
];

foreach ($routes as $uri => $attendu) {
    $root = realpath(__DIR__ . '/..');
    $script = "<?php\n";
    $script .= "chdir(" . var_export($root, true) . ");\n";
    $script .= "\$_SERVER['REQUEST_URI'] = " . var_export($uri, true) . ";\n";
    $script .= "ob_start();\n";
    $script .= "require 'index.php';\n";
    $script .= "echo ob_get_clean();\n";

    $tmpfile = tempnam(sys_get_temp_dir(), 'route');
    file_put_contents($tmpfile, $script);
    $process = proc_open([
        PHP_BINARY,
        $tmpfile,
    ], [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes);

    if (!is_resource($process)) {
        unlink($tmpfile);
        throw new RuntimeException('Impossible de lancer le processus PHP pour le test de routing.');
    }

    fclose($pipes[0]);
    $contenu = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $erreurs = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $code = proc_close($process);
    unlink($tmpfile);

    if ($code !== 0) {
        throw new RuntimeException(sprintf('Échec du test de route %s : %s', $uri, trim($erreurs)));
    }

    if (strpos($contenu, $attendu) === false) {
        throw new RuntimeException(sprintf('La route %s ne charge pas la vue attendue.', $uri));
    }
}

echo "Test RouteurCommunication : OK\n";
