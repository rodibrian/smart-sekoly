<?php
/**
 * Vue des annonces scolaires.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Annonces scolaires</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #111827; }
        .page { max-width: 1000px; margin: 32px auto; padding: 24px; }
        .carte { background: white; padding: 22px; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); margin-bottom: 20px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; color: #334155; }
        input, textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; }
        textarea { min-height: 140px; resize: vertical; }
        button { padding: 12px 18px; background: #2563eb; color: white; border: none; border-radius: 10px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .annonce { background: #eff6ff; padding: 14px; border-radius: 12px; margin-bottom: 12px; }
        .annonce h3 { margin: 0 0 6px; }
        .annonce small { color: #64748b; }
    </style>
</head>
<body>
    <div class="page">
        <div class="carte">
            <h1>Annonces scolaires</h1>
            <p>Publiez une annonce pour l'établissement ou tracez un événement dans les carnets.</p>
            <form method="post" action="?module=communication&action=annonces">
                <input type="hidden" name="csrf_token" value="<?= e($data['token_csrf']) ?>">
                <div class="form-group">
                    <label for="titre">Titre de l'annonce</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                <div class="form-group">
                    <label for="contenu">Contenu</label>
                    <textarea id="contenu" name="contenu" required></textarea>
                </div>
                <button type="submit">Publier</button>
            </form>
        </div>

        <div class="carte">
            <h2>Historique des annonces</h2>
            <?php if (empty($data['annonces'])): ?>
                <p>Aucune annonce publiée pour le moment.</p>
            <?php else: ?>
                <?php foreach ($data['annonces'] as $annonce): ?>
                    <div class="annonce">
                        <h3><?= e($annonce['titre']) ?></h3>
                        <small><?= e($annonce['date']) ?></small>
                        <p><?= e($annonce['contenu']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
