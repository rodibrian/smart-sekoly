<?php
/**
 * Vue du carnet de suivi collectif.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Carnet de suivi collectif</title>
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
        <h1>Carnet de suivi collectif</h1>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Événement</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['carnet'] as $ligne): ?>
                    <tr>
                        <td><?= e($ligne['date']) ?></td>
                        <td><?= e($ligne['evenement']) ?></td>
                        <td><?= e($ligne['responsable']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
