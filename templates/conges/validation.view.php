<?php
/**
 * Vue de validation d'une demande de congé.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Validation congé <?= e($donnees['conge']['id']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-top: 16px; padding: 16px; background: #f8fafc; border-radius: 10px; }
        .actions { display: flex; gap: 12px; margin-top: 18px; }
        .bouton { display: inline-block; padding: 10px 18px; border-radius: 8px; text-decoration: none; color: white; }
        .valider { background: #16a34a; }
        .refuser { background: #dc2626; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Validation de congé</h1>

        <div class="bloc"><strong>ID :</strong> <?= e($donnees['conge']['id']) ?></div>
        <div class="bloc"><strong>Enseignant :</strong> <?= e($donnees['conge']['enseignant']) ?></div>
        <div class="bloc"><strong>Type :</strong> <?= e($donnees['conge']['type_conge']) ?></div>
        <div class="bloc"><strong>Début :</strong> <?= e($donnees['conge']['date_debut']) ?></div>
        <div class="bloc"><strong>Fin :</strong> <?= e($donnees['conge']['date_fin']) ?></div>
        <div class="bloc"><strong>Raison :</strong> <?= e($donnees['conge']['raison']) ?></div>
        <div class="bloc"><strong>Statut :</strong> <?= e(ucfirst($donnees['conge']['statut'])) ?></div>

        <div class="actions">
            <a class="bouton valider" href="<?= e(BASE_URL . '/conges/validation/' . $donnees['conge']['id'] . '?action=valider') ?>">Valider</a>
            <a class="bouton refuser" href="<?= e(BASE_URL . '/conges/validation/' . $donnees['conge']['id'] . '?action=refuser') ?>">Refuser</a>
        </div>
    </div>
</body>
</html>
