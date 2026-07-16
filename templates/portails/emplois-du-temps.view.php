<?php
/**
 * Vue du portail emploi du temps pour élèves/parents.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Emplois du temps</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; color: #111827; }
        .page { max-width: 980px; margin: 32px auto; padding: 24px; }
        .card { background: white; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); padding: 22px; margin-bottom: 20px; }
        h1, h2 { margin-top: 0; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Emplois du temps</h1>
            <p>Visualisez l'emploi du temps de la semaine pour les élèves.</p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Matières</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['emplois'] as $ligne): ?>
                        <tr>
                            <td><?= e($ligne['jour']) ?></td>
                            <td><?= e($ligne['matieres']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
