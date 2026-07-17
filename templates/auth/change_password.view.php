<?php
/**
 * Vue de changement de mot de passe obligatoire.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Changer le mot de passe</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .carte { width: 100%; max-width: 480px; background: #ffffff; padding: 32px; border-radius: 16px; box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08); }
        h1 { margin-top: 0; color: #111827; }
        label { display: block; margin-top: 18px; margin-bottom: 8px; color: #334155; font-weight: 700; }
        input { width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 1rem; }
        button { width: 100%; margin-top: 22px; padding: 12px 16px; border: none; border-radius: 10px; background: #2563eb; color: white; font-size: 1rem; cursor: pointer; }
        .erreur { margin-bottom: 16px; padding: 12px 14px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 10px; color: #b91c1c; }
        .note { margin-bottom: 16px; padding: 12px 14px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; color: #1e3a8a; }
    </style>
</head>
<body>
    <div class="page">
        <div class="carte">
            <h1>Changer le mot de passe</h1>
            <div class="note">Vous devez changer votre mot de passe avant de continuer.</div>
            <?php if (!empty($donnees['erreurs'])): ?>
                <div class="erreur">
                    <ul>
                        <?php foreach ($donnees['erreurs'] as $erreur): ?>
                            <li><?= e($erreur) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <form method="post" action="<?= e(BASE_URL . '/auth/changer-mot-de-passe') ?>">
                <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

                <label for="mot_de_passe">Nouveau mot de passe</label>
                <input id="mot_de_passe" name="mot_de_passe" type="password" required>

                <label for="confirmation_mot_de_passe">Confirmer le mot de passe</label>
                <input id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" type="password" required>

                <button type="submit">Valider</button>
            </form>
        </div>
    </div>
</body>
</html>
