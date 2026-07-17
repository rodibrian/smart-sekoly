<?php
/**
 * Vue des actualités et annonces scolaires.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Actualités</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .page { max-width: 900px; margin: 32px auto; padding: 24px; }
        .actualite { background: #fff; padding: 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .actualite .date { font-size: 12px; color: #94a3b8; }
        .actualite h3 { margin: 8px 0; }
        .actualite p { margin: 8px 0 0 0; color: #475569; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Actualités et annonces</h1>
        <p>Suivez les dernières actualités de l'établissement.</p>

        <?php foreach ($donnees['actualites'] as $actualite): ?>
            <div class="actualite">
                <div class="date"><?= e($actualite['date']) ?></div>
                <h3><?= e($actualite['titre']) ?></h3>
                <p><?= e($actualite['contenu']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
