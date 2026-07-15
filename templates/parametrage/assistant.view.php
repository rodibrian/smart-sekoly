<?php
/**
 * Vue d’assistant de paramétrage initial.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Paramétrage</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        h1 { margin-top: 0; }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input, select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 16px; padding: 10px 16px; border: 0; border-radius: 8px; background: #0f766e; color: #fff; cursor: pointer; }
        .message { margin-top: 16px; padding: 12px; border-radius: 8px; background: #ecfeff; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Assistant de configuration initiale</h1>
        <p>Définissez les informations essentielles de l’établissement et le format du matricule.</p>

        <form method="post" action="<?= e(BASE_URL . '/parametrage/assistant') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="nom_etablissement">Nom de l’établissement</label>
            <input id="nom_etablissement" name="nom_etablissement" value="" required>

            <label for="format_matricule">Format du matricule</label>
            <input id="format_matricule" name="format_matricule" value="{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}" required>

            <label for="prefixe_matricule">Préfixe du matricule</label>
            <input id="prefixe_matricule" name="prefixe_matricule" value="EL" required>

            <label for="annee_courante">Année scolaire</label>
            <input id="annee_courante" name="annee_courante" value="2026" required>

            <button type="submit">Enregistrer</button>
        </form>

        <div class="message">
            Le format du matricule reste entièrement paramétrable et peut être modifié sans toucher au code.
        </div>
    </div>
</body>
</html>
