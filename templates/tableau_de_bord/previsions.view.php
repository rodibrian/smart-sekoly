<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Prévisions - Tableau de bord</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        table { width: 100%; background: white; border-collapse: collapse; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; overflow: hidden; }
        thead { background: #28a745; color: white; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Prévisions pour l'année suivante</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Année scolaire</th>
                    <th>Élèves attendus</th>
                    <th>Enseignants requis</th>
                    <th>Budget estimé (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['previsions'] as $prevision): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prevision['annee']); ?></td>
                        <td><?php echo htmlspecialchars($prevision['eleves_attendus']); ?></td>
                        <td><?php echo htmlspecialchars($prevision['enseignants_requis']); ?></td>
                        <td><?php echo number_format($prevision['budjet_estime'], 0, ',', ' '); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
