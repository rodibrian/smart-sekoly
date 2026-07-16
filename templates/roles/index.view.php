<?php
/**
 * Vue de gestion des rôles.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Gestion des rôles</title>
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
        button { margin-top: 16px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Gestion des rôles</h1>
        <p>Ajoutez et consultez les rôles disponibles dans le système.</p>

        <?php if (!empty($donnees['message'])): ?>
            <div class="alerte success"><?= e($donnees['message']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL . '/roles/ajouter') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="libelle">Nouveau rôle</label>
            <input id="libelle" name="libelle" placeholder="ex. enseignant" required>

            <button type="submit">Ajouter le rôle</button>
        </form>

        <h2>Rôles existants</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['roles'] as $role): ?>
                    <tr>
                        <td><?= e($role['id_role']) ?></td>
                        <td><?= e($role['libelle']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
