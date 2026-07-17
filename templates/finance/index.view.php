<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Gestion Financière - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-label { color: #999; font-size: 12px; text-transform: uppercase; margin-bottom: 10px; }
        .stat-value { font-size: 24px; font-weight: bold; color: #007bff; }
        .actions { margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; cursor: pointer; border: none; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .sections { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .section { background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .section h3 { color: #333; margin-bottom: 15px; }
        .item { padding: 10px 0; border-bottom: 1px solid #eee; }
        .item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion Financière</h1>
        <p>Tableau de bord financier de l'établissement.</p>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Factures</div>
                <div class="stat-value"><?php echo htmlspecialchars($donnees['stats']['total_factures']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Paiements</div>
                <div class="stat-value"><?php echo htmlspecialchars($donnees['stats']['total_paiements']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Montant Collecté</div>
                <div class="stat-value"><?php echo number_format($donnees['stats']['montant_collecte'], 0, ',', ' '); ?> FCFA</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Montant Impayé</div>
                <div class="stat-value"><?php echo number_format($donnees['stats']['montant_impayé'], 0, ',', ' '); ?> FCFA</div>
            </div>
        </div>

        <div class="actions">
            <a href="?module=finance&action=factures" class="btn">📋 Factures</a>
            <a href="?module=finance&action=facture-creer" class="btn btn-success">➕ Créer facture</a>
            <a href="?module=finance&action=paiements" class="btn">💳 Paiements</a>
            <a href="?module=finance&action=paiement-enregistrer" class="btn btn-success">➕ Enregistrer paiement</a>
            <a href="?module=finance&action=caisses" class="btn">🏦 Caisses</a>
            <a href="?module=finance&action=remises" class="btn">💰 Remises</a>
            <a href="?module=finance&action=rapports" class="btn">📊 Rapports</a>
            <a href="?module=finance&action=impayés" class="btn">⚠️ Impayés</a>
        </div>

        <div class="sections">
            <div class="section">
                <h3>Dernières factures</h3>
                <?php if (empty($donnees['dernieres_factures'])): ?>
                    <p>Aucune facture.</p>
                <?php else: ?>
                    <?php foreach ($donnees['dernieres_factures'] as $facture): ?>
                        <div class="item">
                            <strong><?php echo htmlspecialchars($facture['id_facture']); ?></strong><br>
                            Montant: <?php echo number_format($facture['montant_total'], 0, ',', ' '); ?> FCFA<br>
                            <small><?php echo htmlspecialchars($facture['date_emission']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="section">
                <h3>Derniers paiements</h3>
                <?php if (empty($donnees['derniers_paiements'])): ?>
                    <p>Aucun paiement.</p>
                <?php else: ?>
                    <?php foreach ($donnees['derniers_paiements'] as $paiement): ?>
                        <div class="item">
                            <strong><?php echo htmlspecialchars($paiement['id_paiement']); ?></strong><br>
                            Montant: <?php echo number_format($paiement['montant_paye'], 0, ',', ' '); ?> FCFA<br>
                            <small><?php echo htmlspecialchars($paiement['date_paiement']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
