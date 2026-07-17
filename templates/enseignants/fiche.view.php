<?php
/**
 * Vue de fiche enseignant.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Fiche enseignant</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Fiche enseignant</h1>
        <div class="bloc"><strong>Nom :</strong> <?= e($donnees['enseignant']['nom']) ?></div>
        <div class="bloc"><strong>Prénom :</strong> <?= e($donnees['enseignant']['prenom']) ?></div>
        <div class="bloc"><strong>Email :</strong> <?= e($donnees['enseignant']['email']) ?></div>
        <div class="bloc"><strong>Matricule :</strong> <?= e($donnees['enseignant']['matricule']) ?></div>
    </div>
</body>
</html>
