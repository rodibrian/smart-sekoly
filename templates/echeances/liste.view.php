<?php
/**
 * Liste des échéances.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Échéances</title>
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
        <h1>Échéances</h1>
        <p>Suivi des échéances de paiement liées aux factures.</p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Facture</th>
                    <th>Date échéance</th>
                    <th>Montant prévu</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['echeances'] as $echeance): ?>
                    <tr>
                        <td><?= e($echeance['id']) ?></td>
                        <td><?= e($echeance['facture']) ?></td>
                        <td><?= e($echeance['date']) ?></td>
                        <td><?= e($echeance['montant']) ?> MGA</td>
                        <td><?= e(ucfirst($echeance['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/echeances/fiche/' . $echeance['id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
