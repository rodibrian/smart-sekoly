<?php
/**
 * Liste des factures.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Factures</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 960px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .statut-active { color: #166534; font-weight: 600; }
        .statut-annulee { color: #7c2d12; font-weight: 600; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Factures</h1>
        <p>Liste des factures émises et leur état.</p>

        <?php if (!empty($donnees['message'])): ?>
            <div class="message"><?= e($donnees['message']) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Numéro</th>
                    <th>Date émission</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['factures'] as $facture): ?>
                    <tr>
                        <td><?= e($facture['id']) ?></td>
                        <td><?= e($facture['numero']) ?></td>
                        <td><?= e($facture['date']) ?></td>
                        <td><?= e($facture['montant_total']) ?> MGA</td>
                        <td class="statut-<?= e($facture['statut']) ?>"><?= e(ucfirst($facture['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/factures/fiche/' . $facture['id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
