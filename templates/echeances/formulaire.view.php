<?php
/**
 * Formulaire de création d'échéance.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Nouvelle échéance</title>
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
        <h1>Nouvelle échéance</h1>
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

        <form method="post" action="<?= e(BASE_URL . '/echeances') ?>">
            <input type="hidden" name="token_csrf" value="<?= e($donnees['token_csrf']) ?>">

            <label for="id_facture">Facture</label>
            <input id="id_facture" name="id_facture" type="number" value="<?= e($donnees['donnees']['id_facture'] ?? '') ?>" required>

            <label for="date_echeance">Date d'échéance</label>
            <input id="date_echeance" name="date_echeance" type="date" value="<?= e($donnees['donnees']['date_echeance'] ?? '') ?>" required>

            <label for="montant_prevu">Montant prévu</label>
            <input id="montant_prevu" name="montant_prevu" type="number" step="0.01" value="<?= e($donnees['donnees']['montant_prevu'] ?? '') ?>" required>

            <label for="statut_echeance">Statut</label>
            <select id="statut_echeance" name="statut_echeance">
                <?php foreach ($donnees['statuts'] as $valeur => $libelle): ?>
                    <option value="<?= e($valeur) ?>" <?= isset($donnees['donnees']['statut_echeance']) && $donnees['donnees']['statut_echeance'] === $valeur ? 'selected' : '' ?>><?= e($libelle) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Créer l'échéance</button>
        </form>
    </div>
</body>
</html>
