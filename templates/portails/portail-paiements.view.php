<?php
/**
 * Vue du portail paiements pour parents.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Portail Paiements</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; color: #111827; }
        .page { max-width: 980px; margin: 32px auto; padding: 24px; }
        .card { background: white; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); padding: 22px; margin-bottom: 20px; }
        .title { margin-top: 0; }
        .stats { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .stat { flex: 1; min-width: 180px; background: #eff6ff; padding: 18px; border-radius: 12px; }
        .stat strong { display: block; font-size: 24px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1 class="title">Portail Paiements</h1>
            <p>Suivi des factures et paiements liés aux enfants des parents.</p>
            <div class="stats">
                <div class="stat">
                    <strong><?= e(number_format($data['total_factures'], 0, '', ' ')) ?> FCFA</strong>
                    Total factures
                </div>
                <div class="stat">
                    <strong><?= e(number_format($data['total_paye'], 0, '', ' ')) ?> FCFA</strong>
                    Total payé
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Factures</h2>
            <?php if (empty($data['factures'])): ?>
                <p>Aucune facture enregistrée.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Enfant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['factures'] as $facture): ?>
                            <tr>
                                <td><?= e($facture['numero'] ?? 'N/A') ?></td>
                                <td><?= e(number_format($facture['montant'] ?? 0, 0, '', ' ')) ?> FCFA</td>
                                <td><?= e($facture['statut'] ?? 'inconnu') ?></td>
                                <td><?= e($facture['id_eleve'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
