<?php
/**
 * Vue liste des heures supplémentaires.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Suivi heures supplémentaires</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 980px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .statut-en\ attente { color: #c2410c; font-weight: 700; }
        .statut-validé { color: #166534; font-weight: 700; }
        .statut-refusé { color: #7c2d12; font-weight: 700; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Suivi des heures supplémentaires</h1>
        <p><a href="<?= e(BASE_URL . '/heures-supplementaires/nouvelle') ?>">Ajouter une demande</a></p>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Enseignant</th>
                    <th>Classe</th>
                    <th>Matière</th>
                    <th>Date</th>
                    <th>Heures</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['heures'] as $heure): ?>
                    <tr>
                        <td><?= e($heure['id']) ?></td>
                        <td><?= e($heure['enseignant']) ?></td>
                        <td><?= e($heure['classe']) ?></td>
                        <td><?= e($heure['matiere']) ?></td>
                        <td><?= e($heure['date']) ?></td>
                        <td><?= e($heure['nombre_heures']) ?></td>
                        <td><?= e(number_format($heure['montant'], 0, ',', ' ')) ?> MGA</td>
                        <td class="statut-<?= e(str_replace(' ', '-', $heure['statut'])) ?>"><?= e(ucfirst($heure['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/heures-supplementaires') ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
