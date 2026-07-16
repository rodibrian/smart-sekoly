<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer Remise - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #ffc107; }
        .btn { width: 100%; padding: 12px; background: #ffc107; color: #333; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #e0a800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer une Remise</h1>

        <form method="POST">
            <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($donnees['token_csrf']); ?>">
            <input type="hidden" name="action" value="creer">

            <div class="form-group">
                <label for="id_facture">Facture</label>
                <select name="id_facture" id="id_facture" required>
                    <option value="">-- Sélectionner une facture --</option>
                    <?php foreach ($donnees['factures'] as $facture): ?>
                        <option value="<?php echo htmlspecialchars($facture['id_facture']); ?>">
                            <?php echo htmlspecialchars($facture['id_facture']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="pourcentage">Pourcentage de Remise (%)</label>
                <input type="number" name="pourcentage" id="pourcentage" min="0" max="100" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="motif">Motif de la Remise</label>
                <textarea name="motif" id="motif" placeholder="Ex: Difficultés financières, fidélité client..." rows="3"></textarea>
            </div>

            <button type="submit" class="btn">✓ Appliquer la remise</button>
        </form>
    </div>
</body>
</html>
