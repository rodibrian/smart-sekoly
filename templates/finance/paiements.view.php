<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiements - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; overflow: hidden; }
        thead { background: #28a745; color: white; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Paiements</h1>
        <p>Historique des paiements effectués.</p>

        <?php if (empty($donnees['paiements'])): ?>
            <p style="text-align: center; color: #999; padding: 40px;">Aucun paiement.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Paiement</th>
                        <th>ID Facture</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Date</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['paiements'] as $paiement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($paiement['id_paiement']); ?></td>
                            <td><?php echo htmlspecialchars($paiement['id_facture']); ?></td>
                            <td><?php echo number_format($paiement['montant_paye'], 0, ',', ' '); ?> FCFA</td>
                            <td><?php echo htmlspecialchars($paiement['methode_paiement']); ?></td>
                            <td><?php echo htmlspecialchars($paiement['date_paiement']); ?></td>
                            <td><?php echo htmlspecialchars($paiement['reference']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
