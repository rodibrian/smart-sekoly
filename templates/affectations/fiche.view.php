<?php
/**
 * Vue fiche affectation.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Affectation <?= e($donnees['affectation']['id']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-top: 16px; padding: 16px; background: #f8fafc; border-radius: 10px; }
        .actions { margin-top: 24px; }
        .actions a { color: #1d4ed8; text-decoration: none; }
        .actions a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Fiche affectation</h1>

        <div class="bloc"><strong>ID :</strong> <?= e($donnees['affectation']['id']) ?></div>
        <div class="bloc"><strong>Enseignant :</strong> <?= e($donnees['affectation']['enseignant']) ?></div>
        <div class="bloc"><strong>Classe :</strong> <?= e($donnees['affectation']['classe']) ?></div>
        <div class="bloc"><strong>Matière :</strong> <?= e($donnees['affectation']['matiere']) ?></div>
        <div class="bloc"><strong>Année scolaire :</strong> <?= e($donnees['affectation']['annee']) ?></div>
        <div class="bloc"><strong>Date d'affectation :</strong> <?= e($donnees['affectation']['date_affectation']) ?></div>
        <div class="bloc"><strong>Statut :</strong> <?= e(ucfirst($donnees['affectation']['statut'])) ?></div>

        <div class="actions"><a href="<?= e(BASE_URL . '/affectations') ?>">← Retour à la liste</a></div>
    </div>
</body>
</html>
