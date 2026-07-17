<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Caisses - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .card { background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-title { color: #333; font-weight: bold; margin-bottom: 10px; }
        .card-value { font-size: 20px; color: #007bff; margin-bottom: 10px; }
        .card-meta { color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Caisses</h1>
        <p>Gestion des caisses de l'établissement.</p>

        <?php if (empty($donnees['caisses'])): ?>
            <p style="text-align: center; color: #999; padding: 40px;">Aucune caisse.</p>
        <?php else: ?>
            <div class="cards">
                <?php foreach ($donnees['caisses'] as $caisse): ?>
                    <div class="card">
                        <div class="card-title"><?php echo htmlspecialchars($caisse['nom_caisse']); ?></div>
                        <div class="card-value"><?php echo number_format($caisse['solde_actuel'], 0, ',', ' '); ?> FCFA</div>
                        <div class="card-meta">ID: <?php echo htmlspecialchars($caisse['id_caisse']); ?></div>
                        <div class="card-meta">Créée: <?php echo htmlspecialchars($caisse['date_creation']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
