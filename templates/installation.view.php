<?php
/**
 * Vue d'installation initiale.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Installation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #1f2937; }
        .conteneur { max-width: 900px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        h1 { margin-top: 0; color: #0f172a; }
        .badge { display: inline-block; padding: 8px 12px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-weight: 700; margin-bottom: 16px; }
        .ok { color: #166534; }
        .warn { color: #92400e; }
        ul { line-height: 1.7; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <span class="badge">Smart-Sekoly • Structure initiale</span>
        <h1>Installation de la plateforme</h1>
        <p>La structure initiale du projet est opérationnelle et l'entrée unique répond correctement.</p>

        <h2>État de l'installation</h2>
        <ul>
            <li>Point d'entrée : <strong class="ok">OK</strong></li>
            <li>Autoloader : <strong class="ok">OK</strong></li>
            <li>Configuration : <strong class="ok">OK</strong></li>
            <li>Base de données : <strong><?= $donnees['base_donnees_disponible'] ? '<span class="ok">Disponible</span>' : '<span class="warn">Vérification à compléter</span>' ?></strong></li>
        </ul>

        <h2>Prochaines étapes</h2>
        <ol>
            <li>Créer la base de données <code>smart_sekoly</code>.</li>
            <li>Importer le schéma SQL depuis <code>database/schema_smart_sekoly.sql</code>.</li>
            <li>Valider l'architecture avant de démarrer les classes métier.</li>
        </ol>

        <p>Jeton CSRF généré : <code><?= e($donnees['token_csrf']) ?></code></p>
    </div>
</body>
</html>
