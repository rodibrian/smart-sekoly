<?php
$_SERVER['REQUEST_URI'] = '/smart-sekoly/eleves/inscription';
require_once __DIR__ . '/../index.php';

var_dump(class_exists('EleveController'));
var_dump(class_exists('ElevesController'));
var_dump(class_exists('InstallationController'));
