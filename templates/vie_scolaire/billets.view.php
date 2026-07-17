<?php
/**
 * Vue du suivi des billets.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Suivi des billets</title>
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
        <h1>Suivi des billets</h1>
        <table>
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Motif</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['billets'] as $billet): ?>
                    <tr>
                        <td><?= e($billet['eleve']) ?></td>
                        <td><?= e($billet['date']) ?></td>
                        <td><?= e($billet['type']) ?></td>
                        <td><?= e($billet['motif']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
