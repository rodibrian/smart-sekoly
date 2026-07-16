<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Facture - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 800px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .facture { background: white; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; }
        table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; font-weight: bold; }
        .total { font-size: 18px; font-weight: bold; color: #007bff; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Détails de la Facture</h1>

        <div class="facture">
            <div class="header">
                <h2><?php echo htmlspecialchars($donnees['facture']['id_facture']); ?></h2>
            </div>

            <div class="info-row">
                <div>
                    <div class="label">ID Élève:</div>
                    <div class="value"><?php echo htmlspecialchars($donnees['facture']['id_eleve']); ?></div>
                </div>
                <div>
                    <div class="label">Date d'émission:</div>
                    <div class="value"><?php echo htmlspecialchars($donnees['facture']['date_emission']); ?></div>
                </div>
            </div>

            <div class="info-row">
                <div>
                    <div class="label">Statut:</div>
                    <div class="value"><?php echo htmlspecialchars($donnees['facture']['statut']); ?></div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Montant Total</td>
                        <td class="total"><?php echo number_format($donnees['facture']['montant_total'], 0, ',', ' '); ?> FCFA</td>
                    </tr>
                </tbody>
            </table>

            <a href="?module=finance&action=factures" class="btn">← Retour</a>
        </div>
    </div>
</body>
</html>
