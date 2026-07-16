<?php
$uri = $argv[1] ?? '/';
$needle = $argv[2] ?? '';
$_SERVER['REQUEST_URI'] = $uri;
ob_start();
require 'index.php';
$output = ob_get_clean();
if ($needle !== '' && strpos($output, $needle) !== false) {
    echo 'OK';
    exit(0);
}
if ($needle === '') {
    echo 'NO_NEEDLE';
    exit(0);
}
echo "FAIL\nexpected: $needle\n";
echo "snippet: " . substr(strip_tags($output), 0, 200) . "\n";
