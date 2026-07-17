<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Créer Caisse - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; padding: 0 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input:focus { outline: none; border-color: #007bff; }
        .btn { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer une Caisse</h1>

        <form method="POST">
            <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($donnees['token_csrf']); ?>">
            <input type="hidden" name="action" value="creer">

            <div class="form-group">
                <label for="nom_caisse">Nom de la Caisse</label>
                <input type="text" name="nom_caisse" id="nom_caisse" placeholder="Ex: Caisse principale" required>
            </div>

            <div class="form-group">
                <label for="solde_initial">Solde Initial (FCFA)</label>
                <input type="number" name="solde_initial" id="solde_initial" min="0" step="1" value="0">
            </div>

            <button type="submit" class="btn">✓ Créer la caisse</button>
        </form>
    </div>
</body>
</html>
