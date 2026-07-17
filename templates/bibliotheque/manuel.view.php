<?php
/**
 * Vue du manuel utilisateur intégré pour la bibliothèque documentaire.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Manuel utilisateur</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 980px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        h1, h2 { margin-top: 0; }
        p { line-height: 1.7; }
        .section { margin-top: 24px; }
        .liste { margin-top: 12px; padding-left: 20px; }
        .bouton { display: inline-block; margin-top: 16px; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Manuel utilisateur — Bibliothèque documentaire</h1>
        <p>Cette documentation explique comment utiliser l’espace documentaire interne et gérer les versions des documents administratifs.</p>

        <div class="section">
            <h2>1. Ajouter un document</h2>
            <p>Utilisez le formulaire "Ajouter un document" pour créer un document administratif. Renseignez :</p>
            <ul class="liste">
                <li>Le titre du document</li>
                <li>La catégorie</li>
                <li>La description</li>
            </ul>
            <p>Le document est enregistré en session et apparaît immédiatement dans la liste.</p>
        </div>

        <div class="section">
            <h2>2. Consulter un document</h2>
            <p>Chaque ligne de la liste affiche le titre, la catégorie et la date de création. Cliquez sur le lien "Versions" pour ouvrir l’historique des versions d’un document.</p>
        </div>

        <div class="section">
            <h2>3. Gérer les versions</h2>
            <p>Dans l’écran des versions, vous pouvez :</p>
            <ul class="liste">
                <li>Ajouter une nouvelle version</li>
                <li>Saisir l’auteur</li>
                <li>Ajouter un commentaire</li>
                <li>Enregistrer le contenu de la version</li>
            </ul>
            <p>Chaque version est conservée par ordre inverse pour faciliter la lecture des dernières mises à jour.</p>
        </div>

        <div class="section">
            <h2>4. Astuces</h2>
            <ul class="liste">
                <li>Utilisez le manuel intégré pour retrouver rapidement la logique de gestion documentaire.</li>
                <li>Les versions sont utiles pour tracer les modifications du règlement intérieur ou des circulaires.</li>
                <li>Si vous souhaitez enrichir ce manuel, modifiez cette page directement.</li>
            </ul>
        </div>

        <a class="bouton" href="<?= e(BASE_URL . '/bibliotheque/index') ?>">Retour à la bibliothèque</a>
    </div>
</body>
</html>
