<?php
/**
 * Vue des versions de document dans la bibliothèque documentaire.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Versions du document</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 920px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .lien { color: #1d4ed8; text-decoration: none; }
        .version { padding: 16px; border-radius: 14px; background: #eef2ff; margin-bottom: 16px; }
        .version h3 { margin: 0 0 8px; }
        .version p { margin: 4px 0; }
        textarea { width: 100%; min-height: 140px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 10px; margin-top: 6px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Versions du document</h1>
        <?php if ($data['document'] === null): ?>
            <p>Document introuvable.</p>
            <p><a class="lien" href="<?= e(BASE_URL . '/bibliotheque/index') ?>">Retour à la bibliothèque</a></p>
            <?php return; ?>
        <?php endif; ?>

        <div style="margin-bottom: 20px;">
            <p><strong>Titre :</strong> <?= e($data['document']['titre']) ?></p>
            <p><strong>Catégorie :</strong> <?= e($data['document']['categorie']) ?></p>
            <p><strong>Description :</strong> <?= e($data['document']['description']) ?></p>
            <p><strong>Date de création :</strong> <?= e($data['document']['date_creation']) ?></p>
        </div>

        <div class="version">
            <h2>Ajouter une version</h2>
            <form method="post" action="<?= e(BASE_URL . '/bibliotheque/versions/' . $data['document']['id']) ?>">
                <input type="hidden" name="csrf_token" value="<?= e($data['token_csrf']) ?>">
                <div>
                    <label>Auteur</label><br>
                    <input type="text" name="auteur" required style="width:100%; padding:8px; margin-top:4px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
                <div style="margin-top:10px;">
                    <label>Commentaire</label><br>
                    <input type="text" name="commentaire" style="width:100%; padding:8px; margin-top:4px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
                <div style="margin-top:10px;">
                    <label>Contenu</label><br>
                    <textarea name="contenu" required></textarea>
                </div>
                <button type="submit" style="margin-top:14px; padding:10px 16px; border:0; border-radius:8px; background:#4f46e5; color:#fff; cursor:pointer;">Ajouter la version</button>
            </form>
        </div>

        <?php if (empty($data['versions'])): ?>
            <p>Aucune version enregistrée pour ce document.</p>
        <?php else: ?>
            <?php foreach ($data['versions'] as $version): ?>
                <div class="version">
                    <h3>Version #<?= e($version['id']) ?> — <?= e($version['date_version']) ?></h3>
                    <p><strong>Auteur :</strong> <?= e($version['auteur']) ?></p>
                    <p><strong>Commentaire :</strong> <?= e($version['commentaire']) ?></p>
                    <p><strong>Contenu :</strong></p>
                    <pre style="white-space: pre-wrap; background: #fff; border:1px solid #cbd5e1; padding:12px; border-radius:10px;"><?= e($version['contenu']) ?></pre>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <p><a class="lien" href="<?= e(BASE_URL . '/bibliotheque/index') ?>">Retour à la bibliothèque</a></p>
    </div>
</body>
</html>
