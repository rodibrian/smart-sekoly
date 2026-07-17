<?php
/**
 * Vue de paramétrage courant de l’établissement.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Paramétrage courant</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 900px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .grille { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input, select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 18px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
        .message { margin-top: 16px; padding: 12px; background: #eff6ff; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Paramétrage courant</h1>
        <p>Consultez et mettez à jour les principales valeurs du fonctionnement de l’établissement.</p>

        <form method="post" action="<?= e(BASE_URL . '/parametrage/courant') ?>">
            <div class="grille">
                <div>
                    <label for="nom_etablissement">Nom de l’établissement</label>
                    <input id="nom_etablissement" name="nom_etablissement" value="<?= e($donnees['parametrage']['nom_etablissement'] ?? 'Collège') ?>">
                </div>
                <div>
                    <label for="monnaie">Monnaie</label>
                    <input id="monnaie" name="monnaie" value="<?= e($donnees['parametrage']['monnaie'] ?? 'MGA') ?>">
                </div>
                <div>
                    <label for="langue_par_defaut">Langue par défaut</label>
                    <input id="langue_par_defaut" name="langue_par_defaut" value="<?= e($donnees['parametrage']['langue_par_defaut'] ?? 'fr') ?>">
                </div>
                <div>
                    <label for="theme_par_defaut">Thème par défaut</label>
                    <input id="theme_par_defaut" name="theme_par_defaut" value="<?= e($donnees['parametrage']['theme_par_defaut'] ?? 'clair') ?>">
                </div>
                <div>
                    <label for="format_matricule">Format du matricule</label>
                    <input id="format_matricule" name="format_matricule" value="<?= e($donnees['parametrage']['format_matricule'] ?? '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}') ?>">
                </div>
                <div>
                    <label for="prefixe_matricule">Préfixe du matricule</label>
                    <input id="prefixe_matricule" name="prefixe_matricule" value="<?= e($donnees['parametrage']['prefixe_matricule'] ?? 'CE') ?>">
                </div>
                <div style="grid-column: 1 / -1;">
                    <label>
                        <input type="checkbox" name="auto_download_escpos" value="1" <?= !empty($donnees['parametrage']['auto_download_escpos']) ? 'checked' : '' ?>> Activer le téléchargement automatique du reçu thermique (.escpos) après enregistrement d'un paiement
                    </label>
                </div>
            </div>
            <button type="submit">Enregistrer</button>
        </form>

        <div class="message">
            Cette vue prépare la future persistance des paramètres dans la base de données.
        </div>
    </div>
</body>
</html>
