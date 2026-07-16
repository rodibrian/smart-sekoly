<?php
/**
 * Vue d'import de données élèves.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Import de données</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 820px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        label, pre { display: block; margin-top: 12px; }
        input, button { padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; }
        button { background: #2563eb; color: #fff; cursor: pointer; }
        pre { background: #f8fafc; padding: 12px; overflow: auto; }
        .message { margin-top: 16px; padding: 12px; border-radius: 8px; background: #eff6ff; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Import de données élèves</h1>
        <p>Téléchargez un fichier CSV contenant les élèves à importer.</p>

        <form method="post" enctype="multipart/form-data" action="<?= e(BASE_URL . '/import') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
            <label for="fichier_csv">Fichier CSV</label>
            <input id="fichier_csv" name="fichier_csv" type="file" accept=".csv" required>
            <button type="submit">Importer</button>
        </form>

        <h2>Modèle CSV</h2>
        <pre><?= e($donnees['modele_csv']) ?></pre>

        <?php if (!empty($donnees['resultat'])): ?>
            <div class="message">
                <strong>Résultat :</strong><br>
                Lignes traitées : <?= e($donnees['resultat']['total_lignes']) ?><br>
                Lignes validées : <?= e($donnees['resultat']['lignes_validees']) ?><br>
                Erreurs : <?= e($donnees['resultat']['lignes_erreur']) ?>
                <?php if (!empty($donnees['resultat']['erreurs'])): ?>
                    <pre><?= e(implode("\n", $donnees['resultat']['erreurs'])) ?></pre>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
