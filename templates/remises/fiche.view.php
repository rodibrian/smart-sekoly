<?php
/**
 * Fiche remise.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Remise</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-bottom: 18px; }
        .bloc strong { display: block; margin-bottom: 6px; color: #0f172a; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Remise <?= e($donnees['remise']['id']) ?></h1>
        <div class="bloc"><strong>Type :</strong> <?= e(ucfirst($donnees['remise']['type'])) ?></div>
        <div class="bloc"><strong>Valeur :</strong> <?= e($donnees['remise']['valeur']) ?> <?= $donnees['remise']['type'] === 'pourcentage' ? '%' : 'MGA' ?></div>
        <div class="bloc"><strong>Motif :</strong> <?= e($donnees['remise']['motif']) ?></div>
        <p><a href="<?= e(BASE_URL . '/remises') ?>">Retour à la liste des remises</a></p>
    </div>
</body>
</html>
