<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Vision Directeur - Tableau de bord</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-title { color: #999; font-size: 12px; text-transform: uppercase; margin-bottom: 10px; }
        .card-value { font-size: 32px; font-weight: bold; color: #007bff; }
        .card-subtitle { color: #666; font-size: 14px; margin-top: 10px; }
        .ratio-card .card-value { color: #28a745; }
        .revenue-card .card-value { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vision Directeur</h1>
        
        <div class="cards-grid">
            <div class="card">
                <div class="card-title">Taux d'occupation</div>
                <div class="card-value"><?php echo htmlspecialchars($donnees['vision']['taux_occupation']); ?>%</div>
                <div class="card-subtitle"><?php echo htmlspecialchars($donnees['vision']['places_utilisees']); ?> / <?php echo htmlspecialchars($donnees['vision']['places_totales']); ?> places</div>
            </div>
            
            <div class="card ratio-card">
                <div class="card-title">Ratio élève/enseignant</div>
                <div class="card-value"><?php echo htmlspecialchars($donnees['vision']['ratio_eleve_enseignant']); ?></div>
                <div class="card-subtitle"><?php echo htmlspecialchars($donnees['vision']['enseignants_total']); ?> enseignants</div>
            </div>
            
            <div class="card revenue-card">
                <div class="card-title">Revenus mensuels</div>
                <div class="card-value"><?php echo number_format($donnees['vision']['revenus_mensuels'], 0, ',', ' '); ?> FCFA</div>
                <div class="card-subtitle">Basé sur les paiements</div>
            </div>
        </div>
    </div>
</body>
</html>
