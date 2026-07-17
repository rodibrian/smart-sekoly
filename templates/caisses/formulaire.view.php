<?php
/**
 * Formulaire de création de caisse.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Nouvelle caisse</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        form { display: grid; gap: 18px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; }
        input, button { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { background: #1d4ed8; color: #fff; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Nouvelle caisse</h1>
        <?php if (!empty($donnees['erreurs'])): ?>
            <div class="message erreur">
                <strong>Erreurs :</strong>
                <ul>
                    <?php foreach ($donnees['erreurs'] as $erreur): ?>
                        <li><?= e($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL . '/caisses') ?>">
            <input type="hidden" name="token_csrf" value="<?= e($donnees['token_csrf']) ?>">

            <label for="date_caisse">Date de caisse</label>
            <input id="date_caisse" name="date_caisse" type="date" value="<?= e($donnees['donnees']['date_caisse'] ?? date('Y-m-d')) ?>" required>

            <label for="fond_de_caisse">Fond de caisse initial</label>
            <input id="fond_de_caisse" name="fond_de_caisse" type="number" step="0.01" value="<?= e($donnees['donnees']['fond_de_caisse'] ?? '') ?>" required>

            <button type="submit">Créer la caisse</button>
        </form>
    </div>
</body>
</html>
