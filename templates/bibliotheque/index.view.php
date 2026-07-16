<?php
/**
 * Vue du module bibliothèque documentaire.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Bibliothèque documentaire</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 980px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-top: 18px; }
        .carte { padding: 20px; border-radius: 16px; background: #eef2ff; border: 1px solid #c7d2fe; }
        .carte h2 { margin: 0 0 12px; font-size: 1.15rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .puce { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 999px; background: #c7d2fe; color: #4338ca; margin-right: 8px; }
        .lien { color: #1d4ed8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Bibliothèque documentaire</h1>
        <p>Gérez les documents administratifs et leurs versions.</p>

        <div class="carte">
            <h2>Ajouter un document</h2>
            <form method="post" action="<?= e(BASE_URL . '/bibliotheque/index') ?>">
                <input type="hidden" name="csrf_token" value="<?= e($data['token_csrf']) ?>">
                <div>
                    <label>Titre</label><br>
                    <input type="text" name="titre" required style="width:100%; padding:8px; margin-top:4px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
                <div style="margin-top:10px;">
                    <label>Catégorie</label><br>
                    <input type="text" name="categorie" required style="width:100%; padding:8px; margin-top:4px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
                <div style="margin-top:10px;">
                    <label>Description</label><br>
                    <textarea name="description" rows="3" style="width:100%; padding:8px; margin-top:4px; border:1px solid #cbd5e1; border-radius:8px;"></textarea>
                </div>
                <button type="submit" style="margin-top:14px; padding:10px 16px; border:0; border-radius:8px; background:#4f46e5; color:#fff; cursor:pointer;">Ajouter</button>
            </form>
        </div>
        <div class="carte">
            <h2>Manuel utilisateur</h2>
            <p>Consultez le guide intégré pour comprendre l’utilisation de la bibliothèque documentaire.</p>
            <a class="lien" href="<?= e(BASE_URL . '/bibliotheque/manuel') ?>">Lire le manuel utilisateur</a>
        </div>

        <?php if (empty($data['documents'])): ?>
            <p>Aucun document administratif n'a encore été ajouté.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['documents'] as $document): ?>
                        <tr>
                            <td><?= e($document['id']) ?></td>
                            <td><?= e($document['titre']) ?></td>
                            <td><?= e($document['categorie']) ?></td>
                            <td><?= e($document['description']) ?></td>
                            <td><?= e($document['date_creation']) ?></td>
                            <td>
                        <a class="lien" href="<?= e(BASE_URL . '/bibliotheque/versions/' . $document['id']) ?>">Versions</a>
                    </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
