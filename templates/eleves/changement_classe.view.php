<?php
/**
 * Vue du changement de classe d’un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Changement de classe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .carte { border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Changement de classe</h1>
        <p>Demande de transition pour l’élève n°<?= e($donnees['id_eleve']) ?>.</p>

        <div class="carte">
            <p><strong>Classe actuelle :</strong> <?= e($donnees['changement']['ancienne_classe']) ?></p>
            <p><strong>Nouvelle classe :</strong> <?= e($donnees['changement']['nouvelle_classe']) ?></p>
            <p><strong>Statut :</strong> <?= e($donnees['changement']['statut']) ?></p>
        </div>
    </div>
</body>
</html>
