<?php
/**
 * Vue liste des demandes de congés.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Demandes de congés</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 960px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        .statut-demande { color: #c2410c; font-weight: 700; }
        .statut-valide { color: #166534; font-weight: 700; }
        .statut-refuse { color: #7c2d12; font-weight: 700; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Demandes de congés</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enseignant</th>
                    <th>Type de congé</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['demandes'] as $demande): ?>
                    <tr>
                        <td><?= e($demande['id']) ?></td>
                        <td><?= e($demande['enseignant']) ?></td>
                        <td><?= e($demande['type_conge']) ?></td>
                        <td><?= e($demande['date_debut']) ?></td>
                        <td><?= e($demande['date_fin']) ?></td>
                        <td class="statut-<?= e($demande['statut']) ?>"><?= e(ucfirst($demande['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/conges/validation/' . $demande['id']) ?>">Valider</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
