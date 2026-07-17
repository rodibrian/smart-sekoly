<?php
/**
 * Fiche détail d'une facture.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Détail facture</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-bottom: 18px; }
        .bloc strong { display: block; margin-bottom: 6px; color: #0f172a; }
        .remise { padding: 12px; background: #f8fafc; border-radius: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Facture <?= e($donnees['facture']['numero']) ?></h1>
        <div class="bloc"><strong>Date d'émission :</strong> <?= e($donnees['facture']['date_emission']) ?></div>
        <div class="bloc"><strong>Montant total :</strong> <?= e($donnees['facture']['montant_total']) ?> MGA</div>
        <div class="bloc"><strong>Montant net après remises :</strong> <?= e($donnees['facture']['montant_net']) ?> MGA</div>
        <div class="bloc"><strong>Statut :</strong> <?= e(ucfirst($donnees['facture']['statut'])) ?></div>

        <h2>Lignes de facture</h2>
        <?php if (empty($donnees['facture']['lignes'])): ?>
            <p>Aucune ligne de facture saisie.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type de frais</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['facture']['lignes'] as $ligne): ?>
                        <tr>
                            <td><?= e($ligne['id']) ?></td>
                            <td><?= e($ligne['type_frais']) ?></td>
                            <td><?= e($ligne['montant']) ?> MGA</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2>Remises appliquées</h2>
        <?php if (empty($donnees['facture']['remises'])): ?>
            <p>Aucune remise enregistrée pour cette facture.</p>
        <?php else: ?>
            <?php foreach ($donnees['facture']['remises'] as $remise): ?>
                <div class="remise">
                    <div><strong>Type :</strong> <?= e($remise['type']) ?></div>
                    <div><strong>Valeur :</strong> <?= e($remise['valeur']) ?> <?= $remise['type'] === 'pourcentage' ? '%' : 'MGA' ?></div>
                    <div><strong>Motif :</strong> <?= e($remise['motif']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <p><a href="<?= e(BASE_URL . '/factures') ?>">Retour à la liste des factures</a></p>
    </div>
</body>
</html>
