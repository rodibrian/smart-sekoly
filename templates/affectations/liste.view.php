<?php
/**
 * Vue liste des affectations.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Affectations pédagogiques</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 980px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Affectations pédagogiques</h1>
        <p>Liste des affectations en cours et terminées pour les enseignants.</p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enseignant</th>
                    <th>Classe</th>
                    <th>Matière</th>
                    <th>Année</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['affectations'] as $affectation): ?>
                    <tr>
                        <td><?= e($affectation['id']) ?></td>
                        <td><?= e($affectation['enseignant']) ?></td>
                        <td><?= e($affectation['classe']) ?></td>
                        <td><?= e($affectation['matiere']) ?></td>
                        <td><?= e($affectation['annee']) ?></td>
                        <td><?= e($affectation['date']) ?></td>
                        <td><?= e(ucfirst($affectation['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/affectations/fiche/' . $affectation['id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
