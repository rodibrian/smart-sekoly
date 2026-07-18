<?php
$pageTitle = 'Factures - Finance';
$pageStyles = <<<'STYLES'
* { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; overflow: hidden; }
        thead { background: #007bff; color: white; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        .status-active { color: #28a745; }
        .status-cancelled { color: #dc3545; }
        .btn { display: inline-block; padding: 8px 12px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; }
        .btn:hover { background: #0056b3; }
STYLES;
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="container">
        <h1>Factures</h1>
        <p>Liste de toutes les factures émises.</p>

        <?php if (empty($donnees['factures'])): ?>
            <p style="text-align: center; color: #999; padding: 40px;">Aucune facture.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Facture</th>
                        <th>ID Élève</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['factures'] as $facture): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($facture['id_facture']); ?></td>
                            <td><?php echo htmlspecialchars($facture['id_eleve']); ?></td>
                            <td><?php echo number_format($facture['montant_total'], 0, ',', ' '); ?> FCFA</td>
                            <td><?php echo htmlspecialchars($facture['date_emission']); ?></td>
                            <td class="status-<?php echo htmlspecialchars($facture['statut']); ?>"><?php echo htmlspecialchars($facture['statut']); ?></td>
                            <td>
                                <a href="?module=finance&action=facture-details&parametre=<?php echo htmlspecialchars($facture['id_facture']); ?>" class="btn">Voir</a>
                                <a href="?module=finance&action=facture-editer&parametre=<?php echo htmlspecialchars($facture['id_facture']); ?>" class="btn">Éditer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
