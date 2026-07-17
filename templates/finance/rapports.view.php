<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Rapports Financiers - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; overflow: hidden; }
        thead { background: #6c757d; color: white; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rapports Financiers</h1>
        <p>Résumé financier par période.</p>

        <table>
            <thead>
                <tr>
                    <th>Période</th>
                    <th>Total Factures</th>
                    <th>Total Paiements</th>
                    <th>Impayé</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['rapports'] as $rapport): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rapport['periode']); ?></td>
                        <td><?php echo number_format($rapport['montant_factures'], 0, ',', ' '); ?> FCFA</td>
                        <td><?php echo number_format($rapport['montant_paiements'], 0, ',', ' '); ?> FCFA</td>
                        <td><?php echo number_format($rapport['montant_impayé'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
