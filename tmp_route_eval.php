<?php
$tests = [
    ['/smart-sekoly/enseignants/liste', 'Liste des enseignants'],
    ['/smart-sekoly/eleves/liste', 'Liste des élèves'],
    ['/smart-sekoly/roles/index', 'Gestion des rôles'],
    ['/smart-sekoly/', 'Tableau de bord'],
    ['/smart-sekoly/smart-sekoly/enseignants/liste', 'Liste des enseignants'],
    ['/smart-sekoly/smart-sekoly/eleves/liste', 'Liste des élèves'],
    ['/smart-sekoly/smart-sekoly/roles/index', 'Gestion des rôles'],
    ['/smart-sekoly/smart-sekoly/', 'Tableau de bord'],
];
foreach ($tests as $test) {
    $uri = $test[0];
    $needle = $test[1];
    $_SERVER['REQUEST_URI'] = $uri;
    ob_start();
    require 'index.php';
    $output = ob_get_clean();
    $ok = strpos($output, $needle) !== false ? 'OK' : 'FAIL';
    echo "$uri => $ok\n";
    if ($ok === 'FAIL') {
        echo "expected: $needle\n";
        echo "snippet: " . substr(strip_tags($output), 0, 200) . "\n";
    }
}
