<?php
/**
 * Vue de fiche de congé enseignant.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Congé enseignant</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 720px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Demande de congé</h1>
        <div class="bloc"><strong>Type :</strong> <?= e($donnees['conge']['type_conge']) ?></div>
        <div class="bloc"><strong>Début :</strong> <?= e($donnees['conge']['date_debut']) ?></div>
        <div class="bloc"><strong>Fin :</strong> <?= e($donnees['conge']['date_fin']) ?></div>
        <div class="bloc"><strong>Statut :</strong> <?= e($donnees['conge']['statut']) ?></div>
        <div class="bloc"><strong>Raison :</strong> <?= e($donnees['conge']['raison']) ?></div>
    </div>
</body>
</html>
