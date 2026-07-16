<?php
/**
 * Formulaire de création de paiement.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Nouveau paiement</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        form { display: grid; gap: 18px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; }
        input, select, button { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { background: #1d4ed8; color: #fff; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Nouveau paiement</h1>
        <?php if (!empty($donnees['erreurs'])): ?>
            <div class="message erreur">
                <strong>Erreurs :</strong>
                <ul>
                    <?php foreach ($donnees['erreurs'] as $erreur): ?>
                        <li><?= e($erreur) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL . '/paiements') ?>">
            <input type="hidden" name="token_csrf" value="<?= e($donnees['token_csrf']) ?>">

            <label for="id_echeance">Échéance</label>
            <input id="id_echeance" name="id_echeance" type="number" min="1" value="<?= e($donnees['donnees']['id_echeance'] ?? '') ?>" required>

            <label for="numero_recu">Numéro de reçu</label>
            <input id="numero_recu" name="numero_recu" type="text" value="<?= e($donnees['donnees']['numero_recu'] ?? '') ?>" required>

            <label for="date_paiement">Date et heure</label>
            <input id="date_paiement" name="date_paiement" type="datetime-local" value="<?= e($donnees['donnees']['date_paiement'] ?? date('Y-m-d\TH:i')) ?>" required>

            <label for="montant">Montant</label>
            <input id="montant" name="montant" type="number" step="0.01" required>

            <label for="mode_paiement">Mode de paiement</label>
            <select id="mode_paiement" name="mode_paiement">
                <?php foreach ($donnees['modes'] as $valeur => $libelle): ?>
                    <option value="<?= e($valeur) ?>"><?= e($libelle) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enregistrer le paiement</button>
        </form>
    </div>
</body>
</html>
