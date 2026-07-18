<?php
$pageTitle = 'Factures Impayées - Finance';
$pageStyles = <<<'STYLES'
* { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .alert { background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; overflow: hidden; }
        thead { background: #dc3545; color: white; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        tr:hover { background: #f9f9f9; }
STYLES;
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="container">
        <h1>Factures Impayées</h1>
        <div class="alert">
            ⚠️ Attention : <?php echo count($donnees['impayés']); ?> facture(s) impayée(s)
        </div>

        <?php if (empty($donnees['impayés'])): ?>
            <p style="text-align: center; color: #999; padding: 40px;">✓ Tous les paiements sont à jour!</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Facture</th>
                        <th>ID Élève</th>
                        <th>Montant</th>
                        <th>Date d'émission</th>
                        <th>Jours en retard</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['impayés'] as $impayé): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($impayé['id_facture']); ?></td>
                            <td><?php echo htmlspecialchars($impayé['id_eleve']); ?></td>
                            <td><?php echo number_format($impayé['montant_total'], 0, ',', ' '); ?> FCFA</td>
                            <td><?php echo htmlspecialchars($impayé['date_emission']); ?></td>
                            <td><?php echo max(0, (int) ((time() - strtotime($impayé['date_emission'])) / 86400)); ?> jours</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
