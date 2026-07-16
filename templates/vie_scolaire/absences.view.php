<?php
/**
 * Vue du suivi des absences.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Suivi des absences</title>
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
        <h1>Suivi des absences</h1>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Motif</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['absences'] as $absence): ?>
                    <tr>
                        <td><?= e($absence['date_absence']) ?></td>
                        <td><?= e($absence['motif']) ?></td>
                        <td><?= e(ucfirst($absence['statut'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
