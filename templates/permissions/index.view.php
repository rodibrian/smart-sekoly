<?php
/**
 * Vue de gestion des permissions.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Gestion des permissions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .bouton { display: inline-block; margin-top: 16px; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
        .alerte { margin-top: 16px; padding: 12px; border-radius: 10px; }
        .alerte.success { background: #ecfdf5; color: #166534; }
        .alerte.error { background: #fef2f2; color: #991b1b; }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Gestion des permissions</h1>
        <p>Ajoutez et consultez les permissions du système.</p>

        <?php if (!empty($donnees['message'])): ?>
            <div class="alerte success"><?= e($donnees['message']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL . '/permissions/ajouter') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="module">Module</label>
            <input id="module" name="module" placeholder="ex. finance" required>

            <label for="sous_module">Sous-module</label>
            <input id="sous_module" name="sous_module" placeholder="ex. factures">

            <label for="action">Action</label>
            <input id="action" name="action" placeholder="ex. lire" required>

            <button type="submit">Ajouter la permission</button>
        </form>

        <h2>Permissions existantes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Module</th>
                    <th>Sous-module</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['permissions'] as $permission): ?>
                    <tr>
                        <td><?= e($permission['id_permission']) ?></td>
                        <td><?= e($permission['module']) ?></td>
                        <td><?= e($permission['sous_module'] ?? '-') ?></td>
                        <td><?= e($permission['action']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
