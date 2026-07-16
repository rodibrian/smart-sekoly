<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer Facture - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .info { background: #e7f3ff; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Éditer une Facture</h1>

        <div class="info">
            <strong>ID Facture:</strong> <?php echo htmlspecialchars($donnees['facture']['id_facture']); ?><br>
            <strong>Date d'émission:</strong> <?php echo htmlspecialchars($donnees['facture']['date_emission']); ?>
        </div>

        <form method="POST">
            <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($donnees['token_csrf']); ?>">
            <input type="hidden" name="action" value="editer">

            <div class="form-group">
                <label for="montant_total">Montant Total (FCFA)</label>
                <input type="number" name="montant_total" id="montant_total" min="0" step="1" value="<?php echo htmlspecialchars($donnees['facture']['montant_total']); ?>">
            </div>

            <div class="form-group">
                <label for="statut">Statut</label>
                <select name="statut" id="statut">
                    <option value="active" <?php echo ($donnees['facture']['statut'] === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="cancelled" <?php echo ($donnees['facture']['statut'] === 'cancelled' ? 'selected' : ''); ?>>Annulée</option>
                </select>
            </div>

            <button type="submit" class="btn">✓ Mettre à jour</button>
        </form>
    </div>
</body>
</html>
