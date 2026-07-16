<?php
/**
 * Vue fiche contrat.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Contrat <?= e($donnees['contrat']['id']) ?></title>
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
        <h1>Fiche contrat</h1>

        <div class="bloc"><strong>ID contrat :</strong> <?= e($donnees['contrat']['id']) ?></div>
        <div class="bloc"><strong>Enseignant :</strong> <?= e($donnees['contrat']['enseignant']) ?></div>
        <div class="bloc"><strong>Type :</strong> <?= e($donnees['contrat']['type']) ?></div>
        <div class="bloc"><strong>Début :</strong> <?= e($donnees['contrat']['date_debut']) ?></div>
        <div class="bloc"><strong>Fin :</strong> <?= e($donnees['contrat']['date_fin']) ?></div>
        <div class="bloc"><strong>Salaire :</strong> <?= e($donnees['contrat']['salaire']) ?> MGA</div>
        <div class="bloc"><strong>Statut :</strong> <?= e(ucfirst($donnees['contrat']['statut'])) ?></div>

        <div class="actions"><a href="<?= e(BASE_URL . '/contrats') ?>">← Retour à la liste des contrats</a></div>
    </div>
</body>
</html>
