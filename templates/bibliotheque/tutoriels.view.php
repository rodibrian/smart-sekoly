<?php
/**
 * Vue des tutoriels par rôle pour la bibliothèque documentaire.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Tutoriels par rôle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 980px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        h1, h2 { margin: 0 0 14px; }
        .section { margin-top: 24px; }
        .role-card { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .role-card h3 { margin-top: 0; }
        .role-card ul { margin: 12px 0 0 20px; }
        .bouton { display: inline-block; margin-top: 16px; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Tutoriels par rôle</h1>
        <p>Guides rapides et pratiques selon votre fonction dans l’établissement.</p>

        <?php foreach ($data['tutoriels'] as $tutoriel): ?>
            <div class="role-card">
                <h2><?= e($tutoriel['role']) ?></h2>
                <p><?= e($tutoriel['introduction']) ?></p>
                <ul>
                    <?php foreach ($tutoriel['etapes'] as $etape): ?>
                        <li><?= e($etape) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <a class="bouton" href="<?= e(BASE_URL . '/bibliotheque/index') ?>">Retour à la bibliothèque</a>
    </div>
</body>
</html>
