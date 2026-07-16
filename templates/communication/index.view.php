<?php
/**
 * Vue principale du module Communication interne.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Communication interne</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #111827; }
        .page { max-width: 1000px; margin: 32px auto; padding: 24px; }
        .cartes { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; }
        .carte { background: white; padding: 22px; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); }
        .carte h2 { margin-top: 0; }
        .btn { display: inline-block; padding: 10px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 10px; }
        .btn:hover { background: #1d4ed8; }
        .stat { font-size: 28px; margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Communication interne</h1>
        <p>Messagerie, annonces et suivi des événements dans les carnets des élèves.</p>

        <div class="cartes">
            <div class="carte">
                <h2>Messages internes</h2>
                <div class="stat"><?= e($data['stats']['messages']) ?></div>
                <p>Envoyer ou consulter des messages entre utilisateurs.</p>
                <a href="?module=communication&action=messages" class="btn">Voir les messages</a>
            </div>
            <div class="carte">
                <h2>Annonces scolaires</h2>
                <div class="stat"><?= e($data['stats']['annonces']) ?></div>
                <p>Publier des annonces collectives pour l'établissement.</p>
                <a href="?module=communication&action=annonces" class="btn">Voir les annonces</a>
            </div>
            <div class="carte">
                <h2>Événements de carnet</h2>
                <div class="stat"><?= e($data['stats']['evenements']) ?></div>
                <p>Tracer des événements dans les carnets des élèves.</p>
                <a href="?module=communication&action=annonces" class="btn">Gérer les annonces</a>
            </div>
        </div>
    </div>
</body>
</html>
