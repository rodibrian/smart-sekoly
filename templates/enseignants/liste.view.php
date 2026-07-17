<?php
/**
 * Vue liste des enseignants.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Liste des enseignants</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 960px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px; border: 1px solid #e2e8f0; }
        th { background: #eff6ff; }
        .actions { margin-top: 18px; }
        .bouton { display: inline-block; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Liste des enseignants</h1>
        <p>Consultez et modifiez les enseignants enregistrés.</p>

        <div class="actions">
            <a class="bouton" href="<?= e(BASE_URL . '/enseignants/inscription') ?>">Nouvel enseignant</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Matricule</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['enseignants'] as $enseignant): ?>
                    <tr>
                        <td><?= e($enseignant['nom'] ?? '') ?></td>
                        <td><?= e($enseignant['prenom'] ?? '') ?></td>
                        <td><?= e($enseignant['matricule'] ?? '') ?></td>
                        <td><?= e($enseignant['email'] ?? '') ?></td>
                        <td><?= e($enseignant['statut'] ?? '') ?></td>
                        <td>
                            <a href="<?= e(BASE_URL . '/enseignants/fiche/' . ($enseignant['id'] ?? 0)) ?>">Voir</a>
                            | <a href="<?= e(BASE_URL . '/enseignants/edition/' . ($enseignant['id'] ?? 0)) ?>">Éditer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
