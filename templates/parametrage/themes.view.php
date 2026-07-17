<?php
/**
 * Vue de gestion des thèmes clair/sombre.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
$theme_actuel = $donnees['theme_actuel'] ?? 'clair';
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Thèmes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: <?= $theme_actuel === 'sombre' ? '#0f172a' : '#f8fafc' ?>; color: <?= $theme_actuel === 'sombre' ? '#f8fafc' : '#0f172a' ?>; }
        .conteneur { max-width: 720px; margin: 40px auto; padding: 28px; background: <?= $theme_actuel === 'sombre' ? '#111827' : '#ffffff' ?>; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 12px; font-weight: 700; }
        select, button { width: 100%; padding: 10px; margin-top: 8px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { background: #2563eb; color: #fff; cursor: pointer; }
        .message { margin-top: 16px; padding: 12px; border-radius: 8px; background: <?= $theme_actuel === 'sombre' ? '#1f2937' : '#eff6ff' ?>; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Gestion des thèmes</h1>
        <p>Choisissez le thème visuel de l’application pour l’utilisateur courant.</p>

        <form method="post" action="<?= e(BASE_URL . '/parametrage/themes') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
            <label for="theme">Thème</label>
            <select id="theme" name="theme">
                <option value="clair" <?= $theme_actuel === 'clair' ? 'selected' : '' ?>>Clair</option>
                <option value="sombre" <?= $theme_actuel === 'sombre' ? 'selected' : '' ?>>Sombre</option>
            </select>
            <button type="submit">Appliquer</button>
        </form>

        <div class="message">
            Thème actuel : <strong><?= e($theme_actuel) ?></strong>
        </div>
    </div>
</body>
</html>
