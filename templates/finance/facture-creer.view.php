<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer Facture - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input:focus, select:focus { outline: none; border-color: #007bff; }
        .btn { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer une Facture</h1>

        <form method="POST">
            <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($donnees['token_csrf']); ?>">
            <input type="hidden" name="action" value="creer">

            <div class="form-group">
                <label for="id_eleve">Élève</label>
                <select name="id_eleve" id="id_eleve" required>
                    <option value="">-- Sélectionner un élève --</option>
                    <?php foreach ($donnees['eleves'] as $eleve): ?>
                        <option value="<?php echo htmlspecialchars($eleve['id_eleve']); ?>">
                            <?php echo htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']); ?> (<?php echo htmlspecialchars($eleve['matricule']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="montant_total">Montant Total (FCFA)</label>
                <input type="number" name="montant_total" id="montant_total" min="0" step="1" required>
            </div>

            <div class="form-group">
                <label>Types de Frais</label>
                <?php foreach ($donnees['types_frais'] as $idx => $type): ?>
                    <div style="margin-bottom: 10px;">
                        <input type="checkbox" name="type_frais[]" value="<?php echo htmlspecialchars($type['nom_type']); ?>" id="frais_<?php echo $idx; ?>">
                        <label for="frais_<?php echo $idx; ?>"><?php echo htmlspecialchars($type['nom_type']); ?> (<?php echo number_format($type['montant_default'], 0, ',', ' '); ?> FCFA)</label>
                        <input type="hidden" name="montant_ligne[]" value="<?php echo htmlspecialchars($type['montant_default']); ?>">
                        <input type="hidden" name="quantite[]" value="1">
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn">✓ Créer la facture</button>
        </form>
    </div>
</body>
</html>
