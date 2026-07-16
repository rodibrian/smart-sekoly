<?php
/**
 * Liste des caisses.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Caisses</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Caisses</h1>
        <p>Annuaire des caisses journalières et leur fond de caisse.</p>

        <p><a href="<?= e(BASE_URL . '/caisses/nouvelle') ?>">Nouvelle caisse</a></p>

        <?php if (!empty($donnees['message'])): ?>
            <div class="message"><?= e($donnees['message']) ?></div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Fond de caisse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['caisses'] as $caisse): ?>
                    <tr>
                        <td><?= e($caisse['id']) ?></td>
                        <td><?= e($caisse['date']) ?></td>
                        <td><?= e($caisse['fond']) ?> MGA</td>
                        <td><a href="<?= e(BASE_URL . '/caisses/fiche/' . $caisse['id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
