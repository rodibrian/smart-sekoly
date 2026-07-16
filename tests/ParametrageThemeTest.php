<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../controllers/Parametrage.controller.php';

$controleur = new ParametrageController('parametrage', 'themes');
$resultat = $controleur->traiter_theme_formulaire([
    'theme' => 'sombre',
    'csrf_token' => generer_token_csrf(),
]);

if ($resultat['valide'] !== true) {
    throw new RuntimeException('La gestion du thème a échoué.');
}

if ($resultat['donnees']['theme'] !== 'sombre') {
    throw new RuntimeException('Le thème sélectionné n’a pas été conservé.');
}

echo "Test ParametrageTheme : OK\n";
