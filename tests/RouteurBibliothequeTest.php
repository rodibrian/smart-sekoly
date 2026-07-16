<?php

$root = realpath(__DIR__ . '/..');
$sessionPath = $root . '/tmp_session';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

$routes = [
    '/smart-sekoly/bibliotheque/index' => 'Bibliothèque documentaire',
    '/smart-sekoly/bibliotheque/versions/1' => 'Versions du document',
    '/smart-sekoly/bibliotheque/manuel' => 'Manuel utilisateur',
];

foreach ($routes as $uri => $attendu) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $uri;
    $_GET = [];
    $_POST = [];

    $sessionId = 'bibliotheque1';
    $sessionData = [];

    if ($uri === '/smart-sekoly/bibliotheque/versions/1') {
        $sessionData['bibliotheque']['documents'][] = [
            'id' => 1,
            'titre' => 'Document test',
            'categorie' => 'Test',
            'description' => 'Description test',
            'date_creation' => '01/01/2025',
        ];
    }

    $script = "<?php\n";
    $script .= "chdir(" . var_export($root, true) . ");\n";
    $script .= "ini_set('session.save_path', " . var_export($sessionPath, true) . ");\n";
    $script .= "ini_set('session.use_cookies', '0');\n";
    $script .= "session_id('" . $sessionId . "');\n";
    $script .= "session_start();\n";
    $script .= "\$_SESSION = " . var_export($sessionData, true) . ";\n";
    $script .= "session_write_close();\n";
    $script .= "session_id('" . $sessionId . "');\n";
    $script .= "\$_SERVER['REQUEST_URI'] = " . var_export($uri, true) . ";\n";
    $script .= "\$_SERVER['REQUEST_METHOD'] = 'GET';\n";
    $script .= "ob_start();\n";
    $script .= "require " . var_export($root . DIRECTORY_SEPARATOR . 'index.php', true) . ";\n";
    $script .= "echo ob_get_clean();\n";

    $tmpfile = tempnam(sys_get_temp_dir(), 'route_');
    file_put_contents($tmpfile, $script);

    $output = [];
    $exitCode = 0;
    exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($tmpfile), $output, $exitCode);
    unlink($tmpfile);

    if ($exitCode !== 0) {
        throw new RuntimeException(sprintf('La route %s a échoué (%d): %s', $uri, $exitCode, implode("\n", $output)));
    }

    $contenu = implode("\n", $output);
    if (strpos($contenu, $attendu) === false) {
        throw new RuntimeException(sprintf('La route %s ne charge pas la vue attendue.', $uri));
    }
}

echo "Test RouteurBibliotheque : OK\n";
