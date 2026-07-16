<?php
/**
 * Formulaire de création de facture.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Nouvelle facture</title>
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
        <h1>Nouvelle facture</h1>
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

        <form method="post" action="<?= e(BASE_URL . '/factures') ?>">
            <input type="hidden" name="token_csrf" value="<?= e($donnees['token_csrf']) ?>">

            <label for="eleve">Élève</label>
            <select id="eleve" name="eleve">
                <?php foreach ($donnees['eleves'] as $eleve): ?>
                    <option value="<?= e($eleve['id']) ?>" <?= isset($donnees['donnees']['id_eleve']) && $donnees['donnees']['id_eleve'] == $eleve['id'] ? 'selected' : '' ?>><?= e($eleve['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="numero">Numéro séquentiel</label>
            <input id="numero" name="numero" type="text" value="<?= e($donnees['donnees']['numero'] ?? '') ?>" required>

            <label for="date_emission">Date d'émission</label>
            <input id="date_emission" name="date_emission" type="date" value="<?= e(date('Y-m-d')) ?>" required>

            <label for="montant_total">Montant total</label>
            <input id="montant_total" name="montant_total" type="number" step="0.01" value="<?= e($donnees['donnees']['montant_total'] ?? '') ?>" required>

            <button type="submit">Créer la facture</button>
        </form>
    </div>
</body>
</html>
