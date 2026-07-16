<?php
/**
 * Vue d'accès refusé.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Accès refusé</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .carte { width: 100%; max-width: 500px; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08); }
        h1 { margin-top: 0; color: #111827; }
        p { color: #475569; }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="page">
        <div class="carte">
            <h1>403 — Accès refusé</h1>
            <p>Vous n’avez pas les permissions nécessaires pour accéder à cette page.</p>
            <p><a href="<?= e(BASE_URL . '/auth/login') ?>">Se connecter</a> ou retourner à l’accueil.</p>
        </div>
    </div>
</body>
</html>
