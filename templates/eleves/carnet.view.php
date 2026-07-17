<?php
/**
 * Vue du carnet de suivi d’un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Carnet de suivi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .evenement { border-left: 4px solid #2563eb; padding: 12px 14px; margin-top: 12px; background: #f8fafc; border-radius: 8px; }
        .evenement.warning { border-left-color: #dc2626; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Carnet de suivi</h1>
        <p>Historique des événements pour l’élève n°<?= e($donnees['id_eleve']) ?>.</p>

        <?php foreach ($donnees['evenements'] as $evenement): ?>
            <div class="evenement <?= e($evenement['type'] === 'warning' ? 'warning' : '') ?>">
                <strong><?= e($evenement['titre']) ?></strong>
                <div><?= e($evenement['description']) ?></div>
                <small><?= e($evenement['date']) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
