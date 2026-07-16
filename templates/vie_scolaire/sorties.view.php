<?php
/**
 * Vue des autorisations de sortie.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Autorisation de sortie</title>
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
        <h1>Autorisation de sortie</h1>
        <table>
            <thead>
                <tr>
                    <th>Élève</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Responsable</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['sorties'] as $sortie): ?>
                    <tr>
                        <td><?= e($sortie['eleve']) ?></td>
                        <td><?= e($sortie['date']) ?></td>
                        <td><?= e($sortie['heure']) ?></td>
                        <td><?= e($sortie['responsable']) ?></td>
                        <td><?= e(ucfirst($sortie['statut'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
