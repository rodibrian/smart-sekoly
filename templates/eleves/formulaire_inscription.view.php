<?php
/**
 * Formulaire d’inscription d’un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Inscription élève</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 780px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 16px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
        .message { margin-top: 16px; padding: 12px; background: #eff6ff; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Formulaire d’inscription élève</h1>
        <p>Enregistrez les informations de base d’un nouvel élève.</p>

        <form method="post" action="<?= e(BASE_URL . '/eleves/inscription') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="nom">Nom</label>
            <input id="nom" name="nom" required>

            <label for="prenom">Prénom</label>
            <input id="prenom" name="prenom" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" required>

            <label for="date_naissance">Date de naissance</label>
            <input id="date_naissance" name="date_naissance" type="date" required>

            <label for="matricule">Matricule</label>
            <input id="matricule" name="matricule" required>

            <button type="submit">Enregistrer</button>
        </form>

        <div class="message">
            Ce formulaire prépare l’inscription réelle et la génération du matricule à l’étape suivante.
        </div>
    </div>
</body>
</html>
