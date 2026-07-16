<?php
require_once __DIR__ . '/../controllers/Eleve.controller.php';

if (!class_exists('EleveController')) {
    throw new RuntimeException('Le contrôleur EleveController n’est pas chargé.');
}

echo "Test AutoloadController : OK\n";
