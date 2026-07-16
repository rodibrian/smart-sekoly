<?php
/**
 * Vue du portail de consultation pour parents et élèves.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Consultation Portail</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; color: #111827; }
        .page { max-width: 980px; margin: 32px auto; padding: 24px; }
        .card { background: white; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); padding: 22px; margin-bottom: 20px; }
        .title { margin-top: 0; }
        .grid { display: grid; gap: 18px; }
        .info { display: flex; gap: 12px; flex-wrap: wrap; }
        .chip { background: #e2e8f0; border-radius: 999px; padding: 8px 14px; font-size: 14px; }
        .btn { display: inline-block; padding: 10px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 10px; }
        .btn:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1 class="title">Portail de consultation</h1>
            <p>Consultez les informations scolaires accessibles aux parents et élèves.</p>
            <div class="info">
                <?php foreach ($data['modes'] as $mode => $label): ?>
                    <span class="chip"><?= e($label) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card">
            <h2>Accès disponible</h2>
            <?php if (empty($data['portails'])): ?>
                <p>Aucun code d'accès n'est généré pour le moment.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Parent</th>
                            <th>Enfants</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['portails'] as $portail): ?>
                            <tr>
                                <td><?= e($portail['code']) ?></td>
                                <td><?= e($portail['parent_nom']) ?> (<?= e($portail['parent_type']) ?>)</td>
                                <td><?= e(implode(', ', array_map(fn($id) => 'ID ' . e($id), $portail['enfants']))) ?></td>
                                <td><?= e($portail['statut']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
