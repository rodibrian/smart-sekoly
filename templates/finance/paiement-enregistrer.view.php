<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
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
                <label for="id_echeance">Échéance</label>
                <select name="id_echeance" id="id_echeance" required>
                    <option value="">-- Sélectionner une échéance --</option>
                    <?php foreach ($donnees['echeances'] as $echeance): ?>
                        <option value="<?php echo htmlspecialchars($echeance['id_echeance']); ?>">
                            <?php echo htmlspecialchars($echeance['id_echeance']); ?> - Facture <?php echo htmlspecialchars($echeance['id_facture']); ?> - <?php echo number_format($echeance['montant_prevu'] ?? 0, 0, ',', ' '); ?> FCFA prévu
                            (payé <?php echo number_format($echeance['montant_paye'] ?? 0, 0, ',', ' '); ?> FCFA, statut <?php echo htmlspecialchars($echeance['statut'] ?? $echeance['statut_echeance'] ?? ''); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_recu">Numéro de reçu</label>
                <input id="numero_recu" name="numero_recu" type="text" value="<?= e($donnees['numero_recu'] ?? '') ?>" readonly>
            </div>

            <div class="form-group">
                <label for="date_paiement">Date du paiement</label>
                <input id="date_paiement" name="date_paiement" type="datetime-local" value="<?= e($donnees['date_paiement'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="montant">Montant (FCFA)</label>
                <input type="number" name="montant" id="montant" min="0" step="1" required>
            </div>

            <div class="form-group">
                <label for="mode_paiement">Méthode de Paiement</label>
                <select name="mode_paiement" id="mode_paiement" required>
                    <?php foreach ($donnees['methodes_paiement'] as $valeur => $libelle): ?>
                        <option value="<?php echo htmlspecialchars($valeur); ?>"><?php echo htmlspecialchars($libelle); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn">✓ Enregistrer le paiement</button>
        </form>
    </div>
</body>
</html>
