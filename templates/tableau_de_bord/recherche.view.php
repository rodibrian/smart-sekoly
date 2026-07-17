<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Recherche globale - Tableau de bord</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 900px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .search-box { margin-bottom: 30px; }
        .search-box input { width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ddd; border-radius: 4px; }
        .search-box button { background: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; margin-top: 10px; }
        .search-box button:hover { background: #0056b3; }
        .resultats { background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .resultat-item { padding: 15px; border-bottom: 1px solid #eee; }
        .resultat-item:last-child { border-bottom: none; }
        .resultat-type { display: inline-block; background: #007bff; color: white; padding: 4px 8px; border-radius: 3px; font-size: 12px; margin-bottom: 8px; }
        .resultat-nom { font-weight: bold; color: #333; margin-bottom: 5px; }
        .resultat-matricule { color: #666; font-size: 14px; }
        .no-results { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Recherche globale</h1>
        
        <div class="search-box">
            <form method="GET">
                <input type="hidden" name="module" value="<?php echo htmlspecialchars($donnees['module']); ?>">
                <input type="hidden" name="action" value="recherche">
                <input type="text" name="q" placeholder="Rechercher par nom, prénom ou matricule..." value="<?php echo htmlspecialchars($donnees['query']); ?>" autofocus>
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <?php if (!empty($donnees['query'])): ?>
            <?php if (empty($donnees['resultats'])): ?>
                <div class="no-results">
                    <p>Aucun résultat trouvé pour "<strong><?php echo htmlspecialchars($donnees['query']); ?></strong>"</p>
                </div>
            <?php else: ?>
                <div class="resultats">
                    <?php foreach ($donnees['resultats'] as $resultat): ?>
                        <div class="resultat-item">
                            <div class="resultat-type"><?php echo htmlspecialchars($resultat['type']); ?></div>
                            <div class="resultat-nom"><?php echo htmlspecialchars($resultat['nom']); ?></div>
                            <div class="resultat-matricule">Matricule: <?php echo htmlspecialchars($resultat['matricule']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
