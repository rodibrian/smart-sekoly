<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrer Paiement - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input:focus, select:focus { outline: none; border-color: #28a745; }
        .btn { width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #1e7e34; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enregistrer un Paiement</h1>

        <form method="POST">
            <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($donnees['token_csrf']); ?>">
            <input type="hidden" name="action" value="enregistrer">

            <div class="form-group">
                <label for="id_facture">Facture</label>
                <select name="id_facture" id="id_facture" required>
                    <option value="">-- Sélectionner une facture --</option>
                    <?php foreach ($donnees['factures'] as $facture): ?>
                        <option value="<?php echo htmlspecialchars($facture['id_facture']); ?>">
                            <?php echo htmlspecialchars($facture['id_facture']); ?> - <?php echo number_format($facture['montant_total'], 0, ',', ' '); ?> FCFA
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="montant_paye">Montant Payé (FCFA)</label>
                <input type="number" name="montant_paye" id="montant_paye" min="0" step="1" required>
            </div>

            <div class="form-group">
                <label for="methode_paiement">Méthode de Paiement</label>
                <select name="methode_paiement" id="methode_paiement" required>
                    <?php foreach ($donnees['methodes_paiement'] as $methode): ?>
                        <option value="<?php echo htmlspecialchars($methode); ?>"><?php echo htmlspecialchars($methode); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="reference">Référence (Numéro de chèque, etc.)</label>
                <input type="text" name="reference" id="reference" placeholder="Optionnel">
            </div>

            <button type="submit" class="btn">✓ Enregistrer le paiement</button>
        </form>
    </div>
</body>
</html>
