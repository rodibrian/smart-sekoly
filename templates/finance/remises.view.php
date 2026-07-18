<?php
$pageTitle = 'Remises - Finance';
$pageStyles = <<<'STYLES'
* { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; overflow: hidden; }
        thead { background: #ffc107; color: #333; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        tr:hover { background: #f9f9f9; }
STYLES;
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="container">
        <h1>Remises et Réductions</h1>
        <p>Liste des remises accordées sur les factures.</p>

        <?php if (empty($donnees['remises'])): ?>
            <p style="text-align: center; color: #999; padding: 40px;">Aucune remise.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Remise</th>
                        <th>ID Facture</th>
                        <th>Pourcentage</th>
                        <th>Motif</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['remises'] as $remise): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($remise['id_remise']); ?></td>
                            <td><?php echo htmlspecialchars($remise['id_facture']); ?></td>
                            <td><?php echo htmlspecialchars($remise['pourcentage']); ?>%</td>
                            <td><?php echo htmlspecialchars($remise['motif']); ?></td>
                            <td><?php echo htmlspecialchars($remise['date_application']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
