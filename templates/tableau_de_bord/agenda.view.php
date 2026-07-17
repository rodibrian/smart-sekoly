<?php
/**
 * Vue de l'agenda du jour.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Agenda du jour</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .page { max-width: 900px; margin: 32px auto; padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(15,23,42,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Agenda du jour</h1>
        <table>
            <thead>
                <tr>
                    <th>Heure</th>
                    <th>Événement</th>
                    <th>Lieu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['agenda'] as $ligne): ?>
                    <tr>
                        <td><?= e($ligne['heure']) ?></td>
                        <td><?= e($ligne['evenement']) ?></td>
                        <td><?= e($ligne['lieu']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
