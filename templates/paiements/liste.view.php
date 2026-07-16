<?php
/**
 * Liste des paiements.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Paiements</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 960px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Paiements</h1>
        <p>Liste des paiements enregistrés pour les échéances.</p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reçu</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Mode</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['paiements'] as $paiement): ?>
                    <tr>
                        <td><?= e($paiement['id']) ?></td>
                        <td><?= e($paiement['recu']) ?></td>
                        <td><?= e($paiement['date']) ?></td>
                        <td><?= e($paiement['montant']) ?> MGA</td>
                        <td><?= e(ucfirst($paiement['mode'])) ?></td>
                        <td><?= e(ucfirst($paiement['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/paiements/fiche/' . $paiement['id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
