<?php
/**
 * Vue calcul des salaires selon type de contrat.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Calcul salaires</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Calcul salaires selon type de contrat</h1>
        <table>
            <thead>
                <tr>
                    <th>Type de contrat</th>
                    <th>Brut</th>
                    <th>Retenues</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['calculs'] as $calcul): ?>
                    <tr>
                        <td><?= e(ucfirst($calcul['type_contrat'])) ?></td>
                        <td><?= e(number_format($calcul['montant_brut'], 0, ',', ' ')) ?> MGA</td>
                        <td><?= e(number_format($calcul['retenues'], 0, ',', ' ')) ?> MGA</td>
                        <td><?= e(number_format($calcul['montant_net'], 0, ',', ' ')) ?> MGA</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
